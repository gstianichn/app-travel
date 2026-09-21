<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TripReview;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    /**
     * Tab Beranda (Home) Penumpang.
     */
    public function index(): View
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        // Cari perjalanan aktif / mendatang pengguna yang sudah terkonfirmasi
        $activeBooking = Booking::query()
            ->with([
                'schedule.route.origin',
                'schedule.route.destination',
                'schedule.vehicle',
                'passengers.seat',
                'ticket',
            ])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['paid', 'confirmed', 'success', 'lunas'])
            ->whereHas('schedule', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today()->toDateString());
            })
            ->latest()
            ->first();

        // Daftar promo aktif
        $promos = [
            [
                'code' => 'HEMATLIBURAN',
                'title' => 'Liburan Hemat Minibus',
                'discount_label' => 'Diskon 25%',
                'valid_until' => '30 Des 2026',
                'route' => 'Bandung ➔ Jakarta',
                'description' => 'Diskon 25% hingga Rp 50.000 untuk perjalanan rute Bandung ➔ Jakarta dengan minimum transaksi Rp 100.000.',
            ],
            [
                'code' => 'TRAVELHEMAT',
                'title' => 'Potongan Rute Eksekutif',
                'discount_label' => 'Potongan Rp 20rb',
                'valid_until' => '31 Des 2026',
                'route' => 'Semua Rute',
                'description' => 'Potongan harga langsung Rp 20.000 tanpa batas kuota armada untuk transaksi minimal Rp 100.000.',
            ],
        ];

        // Riwayat tiket terakhir
        $recentBookings = Booking::query()
            ->with(['schedule.route.origin', 'schedule.route.destination', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->take(3)
            ->get();

        return view('dashboard.user.index', compact('locations', 'activeBooking', 'promos', 'recentBookings'));
    }

    /**
     * Sub-Flow 1: Pencarian Rute & Penjadwalan Shuttle.
     */
    public function search(Request $request): View
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        $searchPerformed = $request->hasAny(['origin', 'destination', 'departure_date', 'passengers']);

        $schedules = collect();

        if ($searchPerformed) {
            $validated = $request->validate([
                'origin' => ['required', 'integer', 'exists:locations,id'],
                'destination' => ['required', 'integer', 'exists:locations,id', 'different:origin'],
                'departure_date' => ['required', 'date', 'after_or_equal:today'],
                'passengers' => ['required', 'integer', 'min:1', 'max:10'],
            ]);

            $schedules = Schedule::query()
                ->with(['route.origin', 'route.destination', 'vehicle.seats', 'driver.user'])
                ->whereDate('departure_date', $validated['departure_date'])
                ->where('available_seats', '>=', $validated['passengers'])
                ->whereNotIn('status', ['cancelled', 'Cancelled', 'completed', 'Completed'])
                ->whereHas('route', function ($q) use ($validated) {
                    $q->where('origin_id', $validated['origin'])
                        ->where('destination_id', $validated['destination']);
                })
                ->whereHas('vehicle', function ($q) {
                    $q->whereIn('status', ['siap_jalan', 'beroperasi', 'ready', 'active']);
                })
                ->orderBy('departure_time')
                ->get();
        }

        return view('dashboard.user.user-search', compact('locations', 'schedules', 'searchPerformed'));
    }

    /**
     * Sub-Flow 2: Pemilihan Armada & Denah Kursi (Seat Map).
     */
    public function selectSeat(Request $request, Schedule $schedule): View
    {
        $schedule->load(['route.origin', 'route.destination', 'vehicle', 'driver.user']);

        $passengers = max(1, min(10, (int) $request->query('passengers', 1)));

        // Pastikan kursi armada terbuat
        if ($schedule->vehicle) {
            $schedule->vehicle->ensureSeatsExist();
        }

        // Lepas booking pending yang sudah lewat expired_at
        $this->releaseExpiredLocks($schedule);

        // Ambil data denah kursi dengan status terkini (Available, Selected, Locked, Occupied)
        $occupancyMap = $this->buildSeatOccupancyMap($schedule);

        return view('dashboard.user.booking-seat', compact('schedule', 'occupancyMap', 'passengers'));
    }

    /**
     * Sub-Flow 2: Kunci Kursi Sementara (Seat Lock selama 10 Menit).
     */
    public function lockSeats(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'passengers' => ['required', 'integer', 'min:1', 'max:10'],
            'seat_ids' => ['required', 'array', 'size:'.$request->input('passengers', 1)],
            'seat_ids.*' => ['required', 'integer', 'exists:seats,id'],
        ]);

        $schedule = Schedule::with('vehicle')->findOrFail($validated['schedule_id']);

        // Bersihkan kunci yang kedaluwarsa dulu
        $this->releaseExpiredLocks($schedule);

        $seatIds = $validated['seat_ids'];

        return DB::transaction(function () use ($schedule, $seatIds) {
            // Cek apakah ada kursi yang bentrok (sudah dipesan / terkunci oleh pengguna lain)
            $occupiedSeatIds = BookingPassenger::query()
                ->whereHas('booking', function ($q) use ($schedule) {
                    $q->where('schedule_id', $schedule->id)
                        ->where(function ($sub) {
                            $sub->whereIn('status', ['paid', 'confirmed', 'success', 'lunas'])
                                ->orWhere(function ($pending) {
                                    $pending->where('status', 'pending')
                                        ->where('expired_at', '>', Carbon::now())
                                        ->where('user_id', '!=', Auth::id());
                                });
                        });
                })
                ->whereIn('seat_id', $seatIds)
                ->pluck('seat_id')
                ->toArray();

            if (! empty($occupiedSeatIds)) {
                return back()->with('error', 'Sebagian kursi yang Anda pilih baru saja dikunci atau dipesan oleh penumpang lain. Silakan pilih kursi lain.');
            }

            // Hapus atau perbarui booking pending lama milik user ini di jadwal yang sama jika ada
            Booking::where('user_id', Auth::id())
                ->where('schedule_id', $schedule->id)
                ->where('status', 'pending')
                ->delete();

            $bookingCode = 'TRV-'.Carbon::now()->format('Ymd').'-'.strtoupper(Str::random(5));
            $unitPrice = $schedule->effectivePrice();
            $totalPassenger = count($seatIds);
            $subtotal = $unitPrice * $totalPassenger;

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => Auth::id(),
                'schedule_id' => $schedule->id,
                'total_passenger' => $totalPassenger,
                'subtotal' => $subtotal,
                'discount' => 0,
                'total_price' => $subtotal,
                'status' => 'pending',
                'source' => 'online',
                'expired_at' => Carbon::now()->addMinutes(10), // Seat Lock 10 Menit!
            ]);

            // Daftarkan penumpang sementara untuk masing-masing kursi
            $user = Auth::user();
            foreach ($seatIds as $idx => $seatId) {
                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seatId,
                    'name' => $idx === 0 ? $user->name : 'Penumpang '.($idx + 1),
                    'phone' => $idx === 0 ? ($user->phone ?? '081234567890') : '',
                ]);
            }

            $schedule->recalculateAvailableSeats();

            return redirect()->route('dashboard.user.booking.data-penumpang', $booking);
        });
    }

    /**
     * Sub-Flow 3: Pengisian Data Pemesan & Penumpang (Quick Passenger) + Promo.
     */
    public function passengerData(Booking $booking): View|RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        // Cek apakah waktu lock habis
        if ($booking->status === 'pending' && $booking->expired_at && $booking->expired_at->isPast()) {
            $booking->update(['status' => 'cancelled']);
            $booking->schedule?->recalculateAvailableSeats();

            return redirect()->route('dashboard.user.search')
                ->with('error', 'Waktu reservasi kursi Anda (Seat Lock) telah habis. Silakan pilih kembali jadwal perjalanan.');
        }

        $booking->load([
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.vehicle',
            'passengers.seat',
        ]);

        // Hitung sisa detik penguncian kursi
        $remainingSeconds = max(0, Carbon::now()->diffInSeconds($booking->expired_at, false));

        // Quick Passenger Presets dari Tab Akun / Profil Pengguna
        $user = Auth::user();
        $quickPassengers = [
            [
                'id' => 'user-me',
                'name' => $user->name,
                'phone' => $user->phone ?? '081234567890',
                'nik' => '3273010198000001',
                'label' => $user->name.' (Saya / Akun)',
            ],
            [
                'id' => 'saved-1',
                'name' => 'Budi Santoso',
                'phone' => '081234567891',
                'nik' => '3273021405880002',
                'label' => 'Budi Santoso (Penumpang Tersimpan)',
            ],
            [
                'id' => 'saved-2',
                'name' => 'Sari Dewi',
                'phone' => '081298765432',
                'nik' => '3273045209930003',
                'label' => 'Sari Dewi (Penumpang Tersimpan)',
            ],
        ];

        return view('dashboard.user.booking-data', compact('booking', 'remainingSeconds', 'quickPassengers'));
    }

    /**
     * Sub-Flow 3: Validasi Kode Promo Voucher secara Realtime (AJAX/API).
     */
    public function validatePromo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'promo_code' => ['required', 'string'],
            'booking_id' => ['required', 'integer', 'exists:bookings,id'],
        ]);

        $booking = Booking::with('schedule.route.origin', 'schedule.route.destination')->findOrFail($validated['booking_id']);
        $code = strtoupper(trim($validated['promo_code']));
        $subtotal = (float) $booking->subtotal;

        $originName = strtolower($booking->schedule?->route?->origin?->name ?? '');
        $destinationName = strtolower($booking->schedule?->route?->destination?->name ?? '');

        // Aturan validasi promo:
        if ($code === 'HEMATLIBURAN') {
            // Berlaku untuk Bandung - Jakarta PP, min transaksi Rp 100.000, diskon 25% max Rp 50.000
            $isRouteValid = (str_contains($originName, 'bandung') || str_contains($destinationName, 'bandung'))
                && (str_contains($originName, 'jakarta') || str_contains($destinationName, 'jakarta'));

            if (! $isRouteValid) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Voucer HEMATLIBURAN hanya berlaku untuk rute Bandung ➔ Jakarta atau sebaliknya.',
                ], 422);
            }

            if ($subtotal < 100000) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Minimum transaksi untuk kode HEMATLIBURAN adalah Rp 100.000.',
                ], 422);
            }

            $discount = min(50000, $subtotal * 0.25);

            return response()->json([
                'valid' => true,
                'code' => 'HEMATLIBURAN',
                'discount' => $discount,
                'formatted_discount' => 'Rp '.number_format($discount, 0, ',', '.'),
                'new_total' => max(0, $subtotal - $discount),
                'formatted_new_total' => 'Rp '.number_format(max(0, $subtotal - $discount), 0, ',', '.'),
                'message' => 'Voucer HEMATLIBURAN berhasil dipasang! Hemat Rp '.number_format($discount, 0, ',', '.'),
            ]);
        }

        if ($code === 'TRAVELHEMAT') {
            if ($subtotal < 100000) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Minimum transaksi untuk kode TRAVELHEMAT adalah Rp 100.000.',
                ], 422);
            }

            $discount = 20000;

            return response()->json([
                'valid' => true,
                'code' => 'TRAVELHEMAT',
                'discount' => $discount,
                'formatted_discount' => 'Rp '.number_format($discount, 0, ',', '.'),
                'new_total' => max(0, $subtotal - $discount),
                'formatted_new_total' => 'Rp '.number_format(max(0, $subtotal - $discount), 0, ',', '.'),
                'message' => 'Voucer TRAVELHEMAT berhasil dipasang! Diskon Rp 20.000',
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Kode voucer "'.$code.'" tidak ditemukan atau sudah kedaluwarsa.',
        ], 422);
    }

    /**
     * Sub-Flow 3: Simpan Data Pemesan & Penumpang serta Terapkan Promo.
     */
    public function savePassengerData(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'booker_name' => ['required', 'string', 'max:150'],
            'booker_phone' => ['required', 'string', 'max:25'],
            'booker_email' => ['required', 'email', 'max:150'],
            'passengers' => ['required', 'array'],
            'passengers.*.id' => ['required', 'integer', 'exists:booking_passengers,id'],
            'passengers.*.name' => ['required', 'string', 'max:150'],
            'passengers.*.phone' => ['nullable', 'string', 'max:25'],
            'passengers.*.special_request' => ['nullable', 'string', 'max:255'],
            'promo_code' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $subtotal = (float) $booking->subtotal;
        $discount = 0.0;
        $promoCode = strtoupper(trim($request->input('promo_code', '')));

        if ($promoCode === 'HEMATLIBURAN' && $subtotal >= 100000) {
            $discount = min(50000, $subtotal * 0.25);
        } elseif ($promoCode === 'TRAVELHEMAT' && $subtotal >= 100000) {
            $discount = 20000;
        }

        $totalPrice = max(0, $subtotal - $discount);

        // Update data booking
        $booking->update([
            'discount' => $discount,
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
            'expired_at' => Carbon::now()->addMinutes(15), // Tambahkan perpanjangan 15 menit untuk waktu pembayaran
        ]);

        // Simpan data setiap penumpang
        foreach ($validated['passengers'] as $passengerData) {
            BookingPassenger::where('id', $passengerData['id'])
                ->where('booking_id', $booking->id)
                ->update([
                    'name' => $passengerData['name'],
                    'phone' => $passengerData['phone'] ?? $validated['booker_phone'],
                    'special_request' => $passengerData['special_request'] ?? null,
                ]);
        }

        // Siapkan atau perbarui data Payment
        $randomVaTail = str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT).rand(1000, 9999);
        Payment::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'order_id' => 'INV-'.Carbon::now()->format('Ymd').'-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT),
                'gross_amount' => $totalPrice,
                'payment_method' => 'Virtual Account BCA',
                'bank_name' => 'BCA',
                'account_name' => 'PT FAST TRAVEL INDONESIA',
                'account_number' => '8271'.$randomVaTail,
                'gateway_provider' => 'midtrans',
                'gateway_status' => 'pending',
                'status' => 'pending',
                'expired_at' => $booking->expired_at,
            ]
        );

        return redirect()->route('dashboard.user.booking.pembayaran', $booking);
    }

    /**
     * Sub-Flow 4: Halaman Pembayaran (VA, QRIS, E-Wallet) dengan Payment Timer.
     */
    public function payment(Booking $booking): View|RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika sudah terverifikasi, langsung ke halaman sukses
        if (in_array(strtolower($booking->status), ['paid', 'confirmed', 'success', 'lunas'])) {
            return redirect()->route('dashboard.user.booking.sukses', $booking);
        }

        $booking->load([
            'payment',
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.vehicle',
            'passengers.seat',
        ]);

        $payment = $booking->payment;
        if (! $payment) {
            return redirect()->route('dashboard.user.booking.data-penumpang', $booking);
        }

        $remainingPaymentSeconds = max(0, Carbon::now()->diffInSeconds($payment->expired_at ?? $booking->expired_at, false));

        return view('dashboard.user.booking-payment', compact('booking', 'payment', 'remainingPaymentSeconds'));
    }

    /**
     * Sub-Flow 4: Verifikasi Pembayaran Otomatis (Simulasi Tombol Bayar Gateway di UI).
     */
    public function simulatePayment(Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $this->confirmAndIssueTicket($booking, 'Virtual Account BCA');

        return redirect()->route('dashboard.user.booking.sukses', $booking)
            ->with('success', 'Pembayaran berhasil diverifikasi secara otomatis oleh Payment Gateway!');
    }

    /**
     * Sub-Flow 4: Webhook Payment Gateway Otomatis (Midtrans / Xendit / Simulator).
     */
    public function paymentWebhook(Request $request): JsonResponse
    {
        $orderId = $request->input('order_id') ?? $request->input('external_id');
        $transactionStatus = strtolower($request->input('transaction_status') ?? $request->input('status') ?? 'settlement');

        if (! $orderId) {
            return response()->json(['status' => 'error', 'message' => 'Missing order_id'], 400);
        }

        $payment = Payment::where('order_id', $orderId)->first();
        if (! $payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        $booking = $payment->booking;

        if (in_array($transactionStatus, ['settlement', 'capture', 'paid', 'success'])) {
            $this->confirmAndIssueTicket($booking, $request->input('payment_type', 'Virtual Account BCA'), $request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Booking status updated from pending to confirmed.',
                'booking_code' => $booking->booking_code,
            ]);
        }

        if (in_array($transactionStatus, ['expire', 'expired', 'cancel', 'failed'])) {
            $booking->update(['status' => 'cancelled']);
            $payment->update(['status' => 'expired']);
            $booking->schedule?->recalculateAvailableSeats();

            return response()->json([
                'status' => 'cancelled',
                'message' => 'Booking marked as expired/cancelled.',
            ]);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Sub-Flow 4: Halaman Sukses & E-Ticket Boarding Pass QR.
     */
    public function paymentSuccess(Booking $booking): View|RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load([
            'payment',
            'ticket',
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.vehicle',
            'schedule.driver.user',
            'passengers.seat',
        ]);

        return view('dashboard.user.booking-success', compact('booking'));
    }

    /**
     * Tab Tiket Saya: Menampilkan Tiket Aktif & Riwayat Pemesanan Riil.
     */
    public function tiket(): View
    {
        $userId = Auth::id();

        // 1. Auto-trigger penyelesaian: jika jam keberangkatan telah lewat, ubah ke completed
        $this->autoCompletePastTrips($userId);

        // 2. Tiket Aktif & Mendatang (Confirmed, Paid, Checked-in) diurutkan keberangkatan terdekat
        $activeBookings = Booking::query()
            ->with([
                'schedule.route.origin',
                'schedule.route.destination',
                'schedule.vehicle',
                'schedule.driver.user',
                'passengers.seat',
                'payment',
                'ticket',
            ])
            ->where('user_id', $userId)
            ->whereIn('status', ['paid', 'confirmed', 'success', 'lunas', 'checked_in'])
            ->whereHas('schedule', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereDate('departure_date', '>', Carbon::today()->toDateString())
                        ->orWhere(function ($todaySub) {
                            $todaySub->whereDate('departure_date', Carbon::today()->toDateString())
                                ->where('departure_time', '>=', Carbon::now()->subHours(2)->format('H:i'));
                        });
                });
            })
            ->get()
            ->sortBy(function ($booking) {
                return $booking->departure_date_time->timestamp;
            })
            ->values();

        // 3. Riwayat Selesai & Dibatalkan
        $historyBookings = Booking::query()
            ->with([
                'schedule.route.origin',
                'schedule.route.destination',
                'schedule.vehicle',
                'schedule.driver.user',
                'passengers.seat',
                'payment',
                'ticket',
                'refund',
                'review',
            ])
            ->where('user_id', $userId)
            ->whereIn('status', ['completed', 'cancelled', 'batal'])
            ->latest()
            ->get();

        // 4. Daftar Refund Aktif & Riwayat Pengajuan
        $refunds = Refund::query()
            ->with([
                'booking.schedule.route.origin',
                'booking.schedule.route.destination',
                'booking.schedule.vehicle',
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        // 5. Cek apakah ada perjalanan completed yang belum diberi review (untuk evaluasi ulasan)
        $pendingReviewBooking = Booking::query()
            ->with([
                'schedule.vehicle',
                'schedule.driver.user',
                'schedule.route.origin',
                'schedule.route.destination',
            ])
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->doesntHave('review')
            ->latest()
            ->first();

        return view('dashboard.user.user-tiket', compact(
            'activeBookings',
            'historyBookings',
            'refunds',
            'pendingReviewBooking'
        ));
    }

    /**
     * Sub-Flow A: Check-in di Pool / Scan QR Code Boarding Pass.
     */
    public function checkInTicket(Ticket $ticket): RedirectResponse
    {
        $booking = $ticket->booking;
        if ($booking && $booking->user_id !== Auth::id()) {
            abort(403);
        }

        $ticket->update([
            'status' => 'checked_in',
            'boarded_at' => Carbon::now(),
        ]);

        if ($booking) {
            $booking->update(['status' => 'checked_in']);
        }

        return back()->with('success', 'Check-in di Pool berhasil! Status tiket Anda kini Checked-in / On-Board.');
    }

    /**
     * Sub-Flow B: Alur Pembatalan & Pengajuan Refund dengan Cut-off Time & Pembebasan Kursi.
     */
    public function submitRefund(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Pengecekan Syarat Waktu (Cut-off Time maksimal H-1 jam sebelum keberangkatan)
        if (! $booking->canRefund()) {
            return back()->with('error', 'Batas waktu pembatalan telah berakhir. Pembatalan dan refund hanya dapat diajukan maksimal H-1 jam sebelum keberangkatan armada.');
        }

        $schedule = $booking->schedule;

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'bank_name' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:100'],
        ]);

        $originalAmount = (float) $booking->total_price;
        $cancellationFee = round($originalAmount * 0.25, 2); // Potongan administrasi 25%
        $refundAmount = max(0, round($originalAmount * 0.75, 2)); // Dana kembali 75%

        DB::transaction(function () use ($booking, $validated, $originalAmount, $cancellationFee, $refundAmount, $schedule) {
            Refund::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'original_amount' => $originalAmount,
                'cancellation_fee' => $cancellationFee,
                'refund_percentage' => 75.00,
                'amount' => $refundAmount,
                'reason' => $validated['reason'],
                'bank_name' => $validated['bank_name'],
                'account_number' => $validated['account_number'],
                'account_holder' => $validated['account_holder'],
                'status' => 'Diajukan',
            ]);

            $booking->update([
                'status' => 'cancelled',
            ]);

            if ($booking->ticket) {
                $booking->ticket->update(['status' => 'cancelled']);
            }

            // Pembebasan Kursi Otomatis ke inventori publik
            if ($schedule) {
                $schedule->recalculateAvailableSeats();
            }
        });

        return back()->with('success', 'Pengajuan pembatalan & refund berhasil dikirim. Kursi telah dibebaskan kembali ke publik dan pengembalian dana 75% sedang diproses.');
    }

    /**
     * Sub-Flow C: Alur Rating & Review (Pascaperjalanan).
     */
    public function submitReview(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'cleanliness_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'driver_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'punctuality_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $overall = round(($validated['cleanliness_rating'] + $validated['driver_rating'] + $validated['punctuality_rating']) / 3, 2);

        TripReview::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'user_id' => Auth::id(),
                'schedule_id' => $booking->schedule_id,
                'vehicle_id' => $booking->schedule?->vehicle_id,
                'driver_id' => $booking->schedule?->driver_id,
                'cleanliness_rating' => $validated['cleanliness_rating'],
                'driver_rating' => $validated['driver_rating'],
                'punctuality_rating' => $validated['punctuality_rating'],
                'overall_rating' => $overall,
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with('success', 'Terima kasih atas ulasan Anda! Penilaian Anda membantu pengelola meningkatkan kualitas armada dan pengemudi.');
    }

    /**
     * Helper: Menandai perjalanan yang jamnya sudah lewat menjadi completed.
     */
    protected function autoCompletePastTrips(int $userId): void
    {
        $pastBookings = Booking::where('user_id', $userId)
            ->whereIn('status', ['paid', 'confirmed', 'success', 'checked_in'])
            ->whereHas('schedule', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereDate('departure_date', '<', Carbon::today()->toDateString())
                        ->orWhere(function ($todaySub) {
                            $todaySub->whereDate('departure_date', Carbon::today()->toDateString())
                                ->where('departure_time', '<=', Carbon::now()->subHours(1)->format('H:i'));
                        });
                });
            })
            ->get();

        foreach ($pastBookings as $b) {
            $b->update(['status' => 'completed']);
            if ($b->ticket) {
                $b->ticket->update(['status' => 'completed']);
            }
        }
    }

    public function notifikasi(): View
    {
        return view('dashboard.user.user-notifikasi');
    }

    public function akun(): View
    {
        return view('dashboard.user.user-akun');
    }

    /**
     * Helper: Menghapus / membatalkan status pending yang sudah expired.
     */
    protected function releaseExpiredLocks(Schedule $schedule): void
    {
        $expiredBookings = Booking::where('schedule_id', $schedule->id)
            ->where('status', 'pending')
            ->where('expired_at', '<=', Carbon::now())
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'cancelled']);
        }

        if ($expiredBookings->isNotEmpty()) {
            $schedule->recalculateAvailableSeats();
        }
    }

    /**
     * Helper: Membangun susunan denah kursi interaktif minibus (Toyota HiAce Premio dll).
     */
    protected function buildSeatOccupancyMap(Schedule $schedule): array
    {
        $vehicle = $schedule->vehicle;
        if (! $vehicle) {
            return [];
        }

        $seats = $vehicle->seats()->orderBy('seat_row')->orderBy('seat_column')->get();

        // Cari semua passenger yang memesan jadwal ini di booking yang belum dibatalkan
        $activeBookings = $schedule->bookings()
            ->with(['passengers.seat', 'user'])
            ->whereNotIn('status', ['cancelled', 'Cancelled'])
            ->get();

        $occupiedSeats = [];
        foreach ($activeBookings as $b) {
            $isPaid = in_array(strtolower($b->status), ['paid', 'confirmed', 'success', 'lunas']);
            $isPendingLock = ($b->status === 'pending') && $b->expired_at && $b->expired_at->isFuture();

            if (! $isPaid && ! $isPendingLock) {
                continue;
            }

            foreach ($b->passengers as $p) {
                if ($p->seat_id) {
                    $status = $isPaid ? 'paid' : ($b->user_id === Auth::id() ? 'my_lock' : 'locked');
                    $occupiedSeats[$p->seat_id] = [
                        'status' => $status,
                        'passenger_name' => $p->name,
                        'booking_code' => $b->booking_code,
                    ];
                }
            }
        }

        $rows = [];
        foreach ($seats as $seat) {
            $occupancy = $occupiedSeats[$seat->id] ?? [
                'status' => 'available',
                'passenger_name' => null,
                'booking_code' => null,
            ];

            $rowKey = (string) $seat->seat_row;
            if (! isset($rows[$rowKey])) {
                $rows[$rowKey] = [];
            }

            $rows[$rowKey][] = [
                'seat_id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_row' => $seat->seat_row,
                'seat_column' => $seat->seat_column,
                'occupancy' => $occupancy,
            ];
        }

        return $rows;
    }

    /**
     * Helper: Mengubah pesanan menjadi confirmed, pembayaran paid, dan menerbitkan tiket QR.
     */
    protected function confirmAndIssueTicket(Booking $booking, string $paymentMethod = 'Virtual Account BCA', array $rawPayload = []): void
    {
        DB::transaction(function () use ($booking, $paymentMethod, $rawPayload) {
            $booking->update([
                'status' => 'confirmed',
            ]);

            $payment = Payment::firstOrNew(['booking_id' => $booking->id]);
            $payment->payment_method = $paymentMethod;
            $payment->status = 'paid';
            $payment->paid_at = Carbon::now();
            $payment->gateway_status = 'settlement';
            $payment->raw_response = $rawPayload;
            $payment->save();

            // Terbitkan E-Ticket Boarding Pass
            $ticketNumber = 'TKT-'.Carbon::now()->format('ymd').'-'.strtoupper(Str::random(5));
            Ticket::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'ticket_number' => $ticketNumber,
                    'qr_code' => 'QR-'.$booking->booking_code,
                    'status' => 'active',
                ]
            );

            // Perbarui sisa kursi
            $booking->schedule?->recalculateAvailableSeats();
        });
    }
}
