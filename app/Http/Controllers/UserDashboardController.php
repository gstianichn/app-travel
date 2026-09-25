<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Route;
use App\Models\SavedPassenger;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TripReview;
use App\Models\User;
use App\Models\UserVoucher;
use App\Notifications\BookingStatusNotification;
use App\Notifications\OperationalInfoNotification;
use App\Notifications\TripReminderNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
            ->with(['schedule.route.origin', 'schedule.route.destination', 'payment', 'schedule.vehicle'])
            ->where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        // Metrik statistik Client Area
        $activeTicketsCount = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['paid', 'confirmed', 'success', 'lunas', 'checked_in'])
            ->whereHas('schedule', function ($q) {
                $q->whereDate('departure_date', '>=', Carbon::today()->toDateString());
            })
            ->count();

        $completedTripsCount = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->count();

        $pendingPaymentsCount = Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('expired_at', '>', Carbon::now())
            ->count();

        $unreadNotifCount = Auth::user()?->unreadNotifications()->count() ?? 0;

        return view('dashboard.user.index', compact(
            'locations',
            'activeBooking',
            'promos',
            'recentBookings',
            'activeTicketsCount',
            'completedTripsCount',
            'pendingPaymentsCount',
            'unreadNotifCount'
        ));
    }

    /**
     * Sub-Flow 1: Pencarian Rute & Penjadwalan Shuttle.
     */
    public function search(Request $request): View
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        $searchPerformed = $request->hasAny(['origin', 'destination', 'departure_date', 'passengers']);

        $schedules = collect();
        $routeConnected = false;
        $originLoc = null;
        $destLoc = null;

        if ($searchPerformed) {
            $validated = $request->validate([
                'origin' => ['required', 'integer', 'exists:locations,id'],
                'destination' => ['required', 'integer', 'exists:locations,id', 'different:origin'],
                'departure_date' => ['required', 'date', 'after_or_equal:today'],
                'passengers' => ['required', 'integer', 'min:1', 'max:10'],
            ]);

            $originLoc = Location::find($validated['origin']);
            $destLoc = Location::find($validated['destination']);

            // Cek apakah rute antara kedua kota terhubung ke jaringan shuttle aktif
            $route = Route::with(['origin', 'destination'])
                ->where('origin_id', $validated['origin'])
                ->where('destination_id', $validated['destination'])
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->first();

            $routeConnected = ($route !== null);
            $transitHub = null;

            // Jika rute terhubung, pastikan jadwal harian tersedia
            if ($routeConnected) {
                Schedule::ensureSchedulesExistForDateAndRoute($route, $validated['departure_date']);
            } elseif ($originLoc && $destLoc) {
                // Temukan kota transit hub yang menghubungkan origin -> hub dan hub -> dest
                $originConnectedDestIds = Route::where('origin_id', $originLoc->id)
                    ->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    })
                    ->pluck('destination_id');

                $destConnectedOriginIds = Route::where('destination_id', $destLoc->id)
                    ->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    })
                    ->pluck('origin_id');

                $hubIds = $originConnectedDestIds->intersect($destConnectedOriginIds);
                if ($hubIds->isNotEmpty()) {
                    $transitHub = Location::find($hubIds->first());
                }
            }

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
        } else {
            $transitHub = null;
        }

        return view('dashboard.user.user-search', compact(
            'locations',
            'schedules',
            'searchPerformed',
            'routeConnected',
            'originLoc',
            'destLoc',
            'transitHub'
        ));
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

        // Quick Passenger Presets dari Tab Akun / Profil Pengguna (Database)
        $user = Auth::user();
        if ($user) {
            $this->ensureUserPresets($user);
        }

        $savedList = $user ? $user->savedPassengers()->orderByDesc('is_primary')->get() : collect();
        $quickPassengers = [];

        foreach ($savedList as $sp) {
            $quickPassengers[] = [
                'id' => $sp->id,
                'name' => $sp->name,
                'phone' => $sp->phone ?? '',
                'nik' => $sp->nik ?? '',
                'label' => $sp->name.($sp->is_primary ? ' (Utama)' : ''),
            ];
        }

        // Jika kosong sama sekali, sediakan fallback data akun
        if (empty($quickPassengers) && $user) {
            $quickPassengers[] = [
                'id' => 'user-me',
                'name' => $user->name,
                'phone' => $user->phone ?? '',
                'nik' => '',
                'label' => $user->name.' (Saya / Akun)',
            ];
        }

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

        // Cek apakah ada di dompet voucher pengguna (UserVoucher)
        $user = Auth::user();
        if ($user) {
            $userVoucher = $user->vouchers()->where('code', $code)->first();
            if ($userVoucher) {
                if (! $userVoucher->isActive()) {
                    $statusMsg = $userVoucher->isUsed() ? 'sudah pernah digunakan' : 'sudah kedaluwarsa';

                    return response()->json([
                        'valid' => false,
                        'message' => "Voucher {$code} {$statusMsg}.",
                    ], 422);
                }

                if ($userVoucher->applicable_route) {
                    $routeMatch = str_contains(strtolower($originName), 'bandung') || str_contains(strtolower($destinationName), 'jakarta');
                    if (! $routeMatch) {
                        return response()->json([
                            'valid' => false,
                            'message' => "Voucher {$code} hanya berlaku untuk rute: {$userVoucher->applicable_route}.",
                        ], 422);
                    }
                }

                if ($subtotal < $userVoucher->min_transaction) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Minimum order untuk voucher '.$code.' adalah Rp '.number_format($userVoucher->min_transaction, 0, ',', '.').'.',
                    ], 422);
                }

                if ($userVoucher->discount_type === 'percent') {
                    $rawDiscount = $subtotal * ($userVoucher->discount_value / 100);
                    $discount = $userVoucher->max_discount ? min($userVoucher->max_discount, $rawDiscount) : $rawDiscount;
                } else {
                    $discount = min($subtotal, (float) $userVoucher->discount_value);
                }

                return response()->json([
                    'valid' => true,
                    'code' => $code,
                    'discount' => $discount,
                    'formatted_discount' => 'Rp '.number_format($discount, 0, ',', '.'),
                    'new_total' => max(0, $subtotal - $discount),
                    'formatted_new_total' => 'Rp '.number_format(max(0, $subtotal - $discount), 0, ',', '.'),
                    'message' => "Voucher {$code} ({$userVoucher->title}) berhasil dipasang! Hemat Rp ".number_format($discount, 0, ',', '.'),
                ]);
            }
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

        // Event Trigger: Status Transaksi - Menunggu Bayar (disertai batas waktu & deep link bayar)
        $booking->load(['schedule.route.origin', 'schedule.route.destination', 'user']);
        $booking->user?->notify(new BookingStatusNotification($booking, 'pending'));

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

            // Event Trigger: Transaksi Dibatalkan karena waktu habis
            $booking->load(['schedule.route.origin', 'schedule.route.destination', 'user']);
            $booking->user?->notify(new BookingStatusNotification($booking, 'expired'));

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

    public function notifikasi(Request $request): View
    {
        $user = Auth::user();
        $selectedCategory = $request->query('category');

        $query = $user->notifications()->latest();

        if ($selectedCategory && in_array($selectedCategory, ['transaksi', 'pengingat', 'operasional'])) {
            $query->where('data->category', $selectedCategory);
        }

        $notifications = $query->paginate(15)->withQueryString();

        // Hitung unread per kategori
        $allNotifications = $user->notifications()->get();
        $unreadCount = $user->unreadNotifications()->count();
        $countTransaksi = $allNotifications->where('data.category', 'transaksi')->count();
        $countPengingat = $allNotifications->where('data.category', 'pengingat')->count();
        $countOperasional = $allNotifications->where('data.category', 'operasional')->count();

        return view('dashboard.user.user-notifikasi', compact(
            'notifications',
            'unreadCount',
            'selectedCategory',
            'countTransaksi',
            'countPengingat',
            'countOperasional'
        ));
    }

    /**
     * Deep Linking: Menandai notifikasi sebagai dibaca dan langsung mengarahkan ke layar terkait.
     */
    public function openNotification(string $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $deepLinkUrl = $notification->data['deep_link_url'] ?? route('dashboard.user.user-tiket');

        return redirect()->to($deepLinkUrl);
    }

    /**
     * Menandai semua notifikasi pengguna sebagai sudah dibaca.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    /**
     * Menghapus notifikasi spesifik dari database riwayat pengguna.
     */
    public function destroyNotification(string $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();
        }

        return back()->with('success', 'Notifikasi berhasil dihapus dari riwayat.');
    }

    /**
     * Panel Pengujian: Memicu simulasi notifikasi (Status Transaksi, Pengingat H-2 Jam, Info Operasional).
     */
    public function simulateNotification(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $type = $request->input('type', 'pending');

        $booking = Booking::with(['schedule.route.origin', 'schedule.route.destination', 'schedule.vehicle', 'schedule.driver.user', 'passengers.seat', 'ticket'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (! $booking) {
            $schedule = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user'])->first();
            if ($schedule) {
                $booking = Booking::create([
                    'booking_code' => 'TRV-'.strtoupper(Str::random(6)),
                    'user_id' => $user->id,
                    'schedule_id' => $schedule->id,
                    'total_passenger' => 1,
                    'subtotal' => 125000,
                    'total_price' => 125000,
                    'status' => 'pending',
                    'expired_at' => Carbon::now()->addMinutes(15),
                ]);
                $booking->load(['schedule.route.origin', 'schedule.route.destination', 'schedule.vehicle', 'schedule.driver.user']);
            }
        }

        if (! $booking) {
            return back()->with('error', 'Belum ada data jadwal untuk pengujian simulasi.');
        }

        switch ($type) {
            case 'pending':
                $user->notify(new BookingStatusNotification($booking, 'pending'));
                $msg = 'Push & Riwayat Notifikasi Terkirim: Menunggu Bayar (Batas Waktu 15 Menit)';
                break;

            case 'confirmed':
                if (! $booking->ticket) {
                    Ticket::firstOrCreate(
                        ['booking_id' => $booking->id],
                        [
                            'ticket_number' => 'TKT-'.Carbon::now()->format('ymd').'-'.strtoupper(Str::random(5)),
                            'qr_code' => 'QR-'.$booking->booking_code,
                            'status' => 'active',
                        ]
                    );
                    $booking->load('ticket');
                }
                $user->notify(new BookingStatusNotification($booking, 'confirmed'));
                $msg = 'Push & Riwayat Notifikasi Terkirim: Pembayaran Berhasil ✅ (E-Ticket Terbit)';
                break;

            case 'expired':
                $user->notify(new BookingStatusNotification($booking, 'expired'));
                $msg = 'Push & Riwayat Notifikasi Terkirim: Transaksi Dibatalkan ⚠️ (Waktu Habis)';
                break;

            case 'reminder_2h':
                $user->notify(new TripReminderNotification(
                    $booking,
                    departureTime: '14:30',
                    requiredArrivalTime: '14:15 WIB',
                    seatNumbers: '1A, 1B'
                ));
                $msg = 'Push & Riwayat Notifikasi Terkirim: Pengingat Perjalanan H-2 Jam ⏰ (Rute, Jam Tiba Pool, Kursi)';
                break;

            case 'operational_vehicle':
                $user->notify(new OperationalInfoNotification(
                    $booking,
                    infoType: 'vehicle_assigned',
                    plateNumber: 'B 1234 XYZ',
                    driverName: 'Pak Budi Santoso',
                    pickupLocation: 'Pool Dipatiukur (Gate 2)'
                ));
                $msg = 'Push & Riwayat Notifikasi Terkirim: Info Operasional Hari-H 🚐 (Pelat Minibus & Lokasi Pool)';
                break;

            case 'operational_delay':
                $user->notify(new OperationalInfoNotification(
                    $booking,
                    infoType: 'delay_traffic',
                    plateNumber: 'B 1234 XYZ',
                    driverName: 'Pak Budi Santoso',
                    delayReason: 'Kepadatan arus di Tol Cipularang KM 90',
                    delayMinutes: 20
                ));
                $msg = 'Push & Riwayat Notifikasi Terkirim: Peringatan Keterlambatan Armada ⚠️ (+20 Menit)';
                break;

            default:
                $msg = 'Tipe notifikasi tidak dikenal.';
                break;
        }

        return back()->with('success', $msg);
    }

    /**
     * Tab Akun & Profil (Manajemen Identitas, Data Tersimpan, & CS).
     */
    public function akun(): View
    {
        $user = Auth::user();
        if ($user) {
            $this->ensureUserPresets($user);
        }

        $savedPassengers = $user ? $user->savedPassengers()
            ->orderByDesc('is_primary')
            ->latest()
            ->get() : collect();

        $vouchers = $user ? $user->vouchers()->latest()->get() : collect();
        $activeVouchersCount = $vouchers->filter->isActive()->count();

        // Ambil tiket terbaru untuk konteks bantuan otomatis (WhatsApp CS & Live Chat)
        $recentBookings = $user ? $user->bookings()
            ->with(['schedule.route.origin', 'schedule.route.destination', 'schedule.vehicle', 'ticket'])
            ->latest()
            ->take(8)
            ->get() : collect();

        return view('dashboard.user.user-akun', compact(
            'user',
            'savedPassengers',
            'vouchers',
            'activeVouchersCount',
            'recentBookings'
        ));
    }

    /**
     * Simpan data penumpang baru (Saved Passenger List).
     */
    public function storeSavedPassenger(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'regex:/^[0-9]{16}$/'],
            'phone' => ['nullable', 'string', 'max:25'],
            'is_primary' => ['nullable', 'boolean'],
        ], [
            'nik.regex' => 'Nomor Identitas Kependudukan (NIK) harus terdiri dari 16 digit angka.',
        ]);

        $user = $request->user();
        $isPrimary = $request->boolean('is_primary');

        if ($isPrimary) {
            $user->savedPassengers()->update(['is_primary' => false]);
        }

        $user->savedPassengers()->create([
            'name' => $validated['name'],
            'nik' => $validated['nik'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_primary' => $isPrimary,
        ]);

        return back()->with('success', 'Data penumpang tersimpan berhasil ditambahkan.');
    }

    /**
     * Update data penumpang tersimpan.
     */
    public function updateSavedPassenger(Request $request, SavedPassenger $savedPassenger): RedirectResponse
    {
        if ($savedPassenger->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'regex:/^[0-9]{16}$/'],
            'phone' => ['nullable', 'string', 'max:25'],
            'is_primary' => ['nullable', 'boolean'],
        ], [
            'nik.regex' => 'Nomor Identitas Kependudukan (NIK) harus terdiri dari 16 digit angka.',
        ]);

        $isPrimary = $request->boolean('is_primary');

        if ($isPrimary) {
            $request->user()->savedPassengers()
                ->where('id', '!=', $savedPassenger->id)
                ->update(['is_primary' => false]);
        }

        $savedPassenger->update([
            'name' => $validated['name'],
            'nik' => $validated['nik'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_primary' => $isPrimary,
        ]);

        return back()->with('success', 'Data penumpang tersimpan berhasil diperbarui.');
    }

    /**
     * Hapus data penumpang tersimpan.
     */
    public function destroySavedPassenger(SavedPassenger $savedPassenger): RedirectResponse
    {
        if ($savedPassenger->user_id !== Auth::id()) {
            abort(403);
        }

        $savedPassenger->delete();

        return back()->with('success', 'Data penumpang berhasil dihapus dari daftar.');
    }

    /**
     * Tab Dompet Voucher & Manajemen Promo (My Vouchers).
     */
    public function promo(Request $request): View
    {
        $user = Auth::user();
        if ($user) {
            $this->ensureUserPresets($user);
        }

        $filterStatus = $request->query('status', 'all');

        $allVouchers = $user ? $user->vouchers()->latest()->get() : collect();

        $totalCount = $allVouchers->count();
        $activeCount = $allVouchers->filter->isActive()->count();
        $usedCount = $allVouchers->filter->isUsed()->count();
        $expiredCount = $allVouchers->filter->isExpired()->count();

        $vouchers = match ($filterStatus) {
            'active' => $allVouchers->filter->isActive(),
            'used' => $allVouchers->filter->isUsed(),
            'expired' => $allVouchers->filter->isExpired(),
            default => $allVouchers,
        };

        return view('dashboard.user.user-promo', compact(
            'vouchers',
            'filterStatus',
            'totalCount',
            'activeCount',
            'usedCount',
            'expiredCount'
        ));
    }

    /**
     * Klaim kode voucher promo atau kompensasi tiket ke Dompet Voucher.
     */
    public function claimVoucher(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $user = $request->user();
        if ($user) {
            $this->ensureUserPresets($user);
        }

        $code = strtoupper(trim($validated['code']));

        // Master Katalog Promo & Voucher Resmi
        $catalog = [
            'HEMATLIBURAN' => [
                'title' => 'Diskon Spesial Liburan 25%',
                'discount_type' => 'percent',
                'discount_value' => 25,
                'max_discount' => 50000,
                'min_transaction' => 100000,
                'applicable_route' => 'Bandung - Jakarta',
                'source' => 'claim_promo',
                'valid_days' => 90,
            ],
            'TRAVELHEMAT' => [
                'title' => 'Potongan Langsung Rp 20.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'max_discount' => null,
                'min_transaction' => 100000,
                'applicable_route' => null,
                'source' => 'claim_promo',
                'valid_days' => 60,
            ],
            'KOMPENSASI50' => [
                'title' => 'Kompensasi Keterlambatan Shuttle Rp 50.000',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'max_discount' => null,
                'min_transaction' => 50000,
                'applicable_route' => null,
                'source' => 'compensation',
                'valid_days' => 180,
            ],
            'WELCOME2026' => [
                'title' => 'Voucher Pengguna Baru Fast Travel Rp 20.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'max_discount' => null,
                'min_transaction' => 75000,
                'applicable_route' => null,
                'source' => 'welcome_gift',
                'valid_days' => 30,
            ],
            'SUPERDISCOUNT' => [
                'title' => 'Promo Super Hemat 30%',
                'discount_type' => 'percent',
                'discount_value' => 30,
                'max_discount' => 60000,
                'min_transaction' => 150000,
                'applicable_route' => null,
                'source' => 'claim_promo',
                'valid_days' => 45,
            ],
        ];

        if (! isset($catalog[$code])) {
            return back()->with('error', "Kode voucher '{$code}' tidak valid atau promo sudah berakhir.");
        }

        // Cek apakah voucher dengan kode ini sudah ada dan masih aktif
        $existing = $user->vouchers()->where('code', $code)->first();
        if ($existing && $existing->isActive()) {
            return back()->with('error', "Voucher '{$code}' sudah ada di Dompet Voucher Anda dan masih aktif.");
        }

        $meta = $catalog[$code];

        $user->vouchers()->create([
            'code' => $code,
            'title' => $meta['title'],
            'discount_type' => $meta['discount_type'],
            'discount_value' => $meta['discount_value'],
            'max_discount' => $meta['max_discount'],
            'min_transaction' => $meta['min_transaction'],
            'applicable_route' => $meta['applicable_route'],
            'source' => $meta['source'],
            'status' => 'active',
            'expires_at' => Carbon::now()->addDays($meta['valid_days']),
        ]);

        return back()->with('success', "Selamat! Voucher '{$code}' berhasil diklaim dan disimpan ke Dompet Voucher Anda.");
    }

    /**
     * Perbarui profil akun pengguna.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:25'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi akun pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }

    /**
     * Helper: Inisialisasi data bawaan untuk demo/testing (Saved Passengers & Vouchers).
     * Hanya dijalankan pada automated test suite agar simulasi lokal pengguna tetap bersih.
     */
    protected function ensureUserPresets(User $user): void
    {
        if (! app()->runningUnitTests()) {
            return;
        }

        // 1. Saved Passengers
        if ($user->savedPassengers()->count() === 0) {
            $user->savedPassengers()->create([
                'name' => $user->name,
                'nik' => '3273010198000001',
                'phone' => $user->phone ?? '081234567890',
                'is_primary' => true,
            ]);

            $user->savedPassengers()->create([
                'name' => 'Budi Santoso',
                'nik' => '3273021405880002',
                'phone' => '081234567891',
                'is_primary' => false,
            ]);

            $user->savedPassengers()->create([
                'name' => 'Sari Dewi',
                'nik' => '3273045209930003',
                'phone' => '081298765432',
                'is_primary' => false,
            ]);
        }

        // 2. User Vouchers
        if ($user->vouchers()->count() === 0) {
            $user->vouchers()->create([
                'code' => 'HEMATLIBURAN',
                'title' => 'Diskon Spesial Liburan 25%',
                'discount_type' => 'percent',
                'discount_value' => 25,
                'max_discount' => 50000,
                'min_transaction' => 100000,
                'applicable_route' => 'Bandung - Jakarta',
                'source' => 'claim_promo',
                'status' => 'active',
                'expires_at' => Carbon::now()->addMonths(3),
            ]);

            $user->vouchers()->create([
                'code' => 'KOMPENSASI50',
                'title' => 'Kompensasi Keterlambatan Shuttle Rp 50.000',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'max_discount' => null,
                'min_transaction' => 50000,
                'applicable_route' => null,
                'source' => 'compensation',
                'status' => 'active',
                'expires_at' => Carbon::now()->addMonths(6),
            ]);

            $user->vouchers()->create([
                'code' => 'WELCOME2026',
                'title' => 'Voucher Pengguna Baru Rp 20.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'max_discount' => null,
                'min_transaction' => 80000,
                'applicable_route' => null,
                'source' => 'welcome_gift',
                'status' => 'used',
                'used_at' => Carbon::now()->subDays(4),
                'expires_at' => Carbon::now()->addMonths(1),
            ]);

            $user->vouchers()->create([
                'code' => 'KILATHEMAT',
                'title' => 'Flash Sale Libur Panjang',
                'discount_type' => 'percent',
                'discount_value' => 30,
                'max_discount' => 40000,
                'min_transaction' => 120000,
                'applicable_route' => null,
                'source' => 'claim_promo',
                'status' => 'expired',
                'expires_at' => Carbon::now()->subDays(2),
            ]);
        }
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
            $booking->load(['schedule.route.origin', 'schedule.route.destination', 'user']);
            $booking->user?->notify(new BookingStatusNotification($booking, 'expired'));
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
            $ticket = Ticket::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'ticket_number' => $ticketNumber,
                    'qr_code' => 'QR-'.$booking->booking_code,
                    'status' => 'active',
                ]
            );

            // Perbarui sisa kursi
            $booking->schedule?->recalculateAvailableSeats();

            // Event Trigger: Pembayaran Sukses & Terbit E-Ticket
            $booking->setRelation('ticket', $ticket);
            $booking->load(['schedule.route.origin', 'schedule.route.destination', 'user', 'ticket']);
            $booking->user?->notify(new BookingStatusNotification($booking, 'confirmed'));
        });
    }
}
