<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminBiasaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Ambil jadwal hari ini atau terdekat (prioritaskan jadwal yang memiliki pemesanan aktif)
        $schedulesWithBookings = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver', 'bookings.passengers.seat'])
            ->whereHas('bookings', function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled', 'batal']);
            })
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        $otherSchedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver', 'bookings.passengers.seat'])
            ->whereDate('departure_date', '>=', $today)
            ->whereNotIn('id', $schedulesWithBookings->pluck('id'))
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(60)
            ->get();

        // Only departures still ahead, or that left less than 30 minutes ago.
        $schedules = $schedulesWithBookings->merge($otherSchedules)
            ->filter(fn ($sched) => $sched->departureCarbon()->greaterThanOrEqualTo(now()->subMinutes(30)))
            ->sortBy(fn ($sched) => $sched->departureCarbon()->timestamp)
            ->take(6)
            ->values();

        // Transform data jadwal untuk dashboard operasional
        $departures = $schedules->map(function ($sched) {
            $origin = $sched->route?->origin?->name ?? '-';
            $destination = $sched->route?->destination?->name ?? '-';
            $capacity = $sched->vehicle?->capacity ?? 10;

            $bookedPassengers = $sched->bookings->whereNotIn('status', ['cancelled', 'batal', 'Cancelled'])->sum(function ($b) {
                return $b->passengers->count() ?: $b->total_passenger;
            });
            $occupied = min($capacity, (int) $bookedPassengers);
            if ($sched->available_seats !== null && $sched->available_seats < $capacity) {
                $occupied = max($occupied, max(0, $capacity - (int) $sched->available_seats));
            }

            // Status follows the clock: boarding opens 15 minutes before departure (Schedule::boardingTime).
            $departureAt = $sched->departureCarbon();
            $status = match (true) {
                in_array(strtolower((string) $sched->status), ['completed', 'selesai']) => 'Selesai',
                now()->greaterThanOrEqualTo($departureAt) => 'Sudah berangkat',
                now()->greaterThanOrEqualTo($departureAt->copy()->subMinutes(15)) => 'Boarding Dibuka',
                default => 'Persiapan',
            };

            return (object) [
                'id' => $sched->id,
                'time' => $departureAt->format('H:i').' WIB',
                'route_name' => "{$origin} ke {$destination}",
                'origin' => $origin,
                'destination' => $destination,
                'plate_number' => $sched->vehicle?->plate_number ?? '-',
                'vehicle_name' => $sched->vehicle?->name ?? 'Kendaraan belum ditugaskan',
                'driver_name' => $sched->driver?->user?->name ?? 'Belum ditugaskan',
                'occupied_seats' => $occupied,
                'total_seats' => $capacity,
                'ratio' => "{$occupied}/{$capacity} kursi",
                'percentage' => (int) min(100, round(($occupied / max(1, $capacity)) * 100)),
                'status' => $status,
                'departure_note' => $departureAt->isToday()
                    ? 'Berangkat '.$departureAt->diffForHumans()
                    : $departureAt->translatedFormat('l, d F'),
            ];
        });

        // Info petugas & lokasi operasional (shift belum dicatat di sistem, jadi tidak ditampilkan)
        $shiftInfo = [
            'staff_name' => $user->name ?? 'Staf Operasional',
            'pool_name' => $user->branch ?: null,
            'active_date' => Carbon::now()->translatedFormat('l, d F Y'),
        ];

        // Statistik ringkasan dari database
        $todayBookings = Booking::where(function ($q) use ($today) {
            $q->whereDate('created_at', $today)
                ->orWhereHas('schedule', function ($sq) use ($today) {
                    $sq->whereDate('departure_date', $today);
                });
        })->whereNotIn('status', ['cancelled', 'batal'])->count();

        $stats = [
            'today_bookings' => $todayBookings,
            'active_schedules' => Schedule::whereDate('departure_date', $today)
                ->whereNotIn('status', ['cancelled', 'Cancelled', 'completed', 'Completed'])
                ->count(),
            'boarding_now' => $departures->where('status', 'Boarding Dibuka')->count(),
            'ready_preparation' => $departures->where('status', 'Persiapan')->count(),
        ];

        // Aktivitas Terkini Riil: Penggabungan Pemesanan Tiket Masuk & Validasi Boarding
        $activities = collect();

        // 1. Data Pemesanan Tiket Masuk dari User
        $recentBookings = Booking::with(['user', 'schedule.route.origin', 'schedule.route.destination', 'passengers.seat'])
            ->whereNotIn('status', ['cancelled', 'batal'])
            ->latest('created_at')
            ->take(5)
            ->get();

        foreach ($recentBookings as $b) {
            $customer = $b->user?->name ?? ($b->passengers->first()?->name ?? 'Penumpang');
            $origin = $b->schedule?->route?->origin?->name ?? 'Asal';
            $dest = $b->schedule?->route?->destination?->name ?? 'Tujuan';
            $seatCount = $b->passengers->count() ?: $b->total_passenger;
            $seats = $b->passengers->map(fn ($p) => $p->seat?->seat_number)->filter()->join(', ');
            $seatInfo = $seats ? " (Kursi {$seats})" : " ({$seatCount} Kursi)";

            $activities->push((object) [
                'type' => 'booking',
                'title' => "Pesanan masuk {$b->booking_code}",
                'desc' => "{$customer}{$seatInfo}, {$origin} ke {$dest}, status {$b->statusLabel()}",
                'timestamp' => $b->created_at,
                'time_text' => $b->created_at ? $b->created_at->diffForHumans() : 'Hari ini',
                'dot_color' => '#0b7285',
                'dot_bg' => '#e3fafc',
            ]);
        }

        // 2. Data Tiket yang Telah Divalidasi / Boarding di Pool
        $recentValidations = Ticket::with(['booking.user', 'booking.passengers.seat', 'booking.schedule.route.origin', 'booking.schedule.route.destination'])
            ->whereNotNull('boarded_at')
            ->latest('boarded_at')
            ->take(5)
            ->get();

        foreach ($recentValidations as $t) {
            $passengerName = $t->booking?->passengers?->first()?->name ?? ($t->booking?->user?->name ?? 'Penumpang');
            $seatList = $t->booking?->passengers?->map(fn ($p) => $p->seat?->seat_number)->filter()->join(', ') ?: 'Kabin';
            $origin = $t->booking?->schedule?->route?->origin?->name ?? 'Asal';
            $dest = $t->booking?->schedule?->route?->destination?->name ?? 'Tujuan';

            $activities->push((object) [
                'type' => 'boarding',
                'title' => "Tiket divalidasi, kursi {$seatList}",
                'desc' => "{$passengerName}, tiket {$t->ticket_number}, {$origin} ke {$dest}",
                'timestamp' => $t->boarded_at,
                'time_text' => $t->boarded_at ? Carbon::parse($t->boarded_at)->diffForHumans() : 'Baru saja',
                'dot_color' => '#087f55',
                'dot_bg' => '#eefbf5',
            ]);
        }

        $recentActivities = $activities->sortByDesc(fn ($a) => $a->timestamp ? Carbon::parse($a->timestamp)->timestamp : 0)->values()->take(5);

        return view('dashboard.admin-biasa.index', compact('departures', 'stats', 'shiftInfo', 'recentActivities'));
    }

    /**
     * Melihat Booking (Manifes Penumpang per Perjalanan)
     */
    public function manajemenBooking(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // 1. Ambil seluruh jadwal yang memiliki pemesanan aktif penumpang (prioritas utama staf)
        $schedulesWithBookings = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings.passengers'])
            ->whereHas('bookings', function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled', 'batal', 'Expired', 'expired', 'Failed', 'failed']);
            })
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        // 2. Ambil jadwal lainnya hari ini / mendatang
        $otherSchedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings'])
            ->whereDate('departure_date', '>=', $today)
            ->whereNotIn('id', $schedulesWithBookings->pluck('id'))
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(20)
            ->get();

        $scheduleList = $schedulesWithBookings->merge($otherSchedules);

        $selectedScheduleId = $request->query('schedule_id');
        $selectedSchedule = null;

        $bookingQuery = function ($q) {
            $q->whereNotIn('status', ['Cancelled', 'cancelled', 'batal', 'Expired', 'expired', 'Failed', 'failed'])
                ->with(['user', 'passengers.seat', 'ticket', 'payment']);
        };

        if ($selectedScheduleId) {
            $selectedSchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver.user',
                'bookings' => $bookingQuery,
            ])->find($selectedScheduleId);
        }

        // Jika tidak ada schedule_id di parameter URL, prioritaskan jadwal yang MEMILIKI pemesanan aktif
        if (! $selectedSchedule) {
            $firstWithBooking = $schedulesWithBookings->first();
            if ($firstWithBooking) {
                $selectedSchedule = Schedule::with([
                    'route.origin',
                    'route.destination',
                    'vehicle',
                    'driver.user',
                    'bookings' => $bookingQuery,
                ])->find($firstWithBooking->id);
            } elseif ($scheduleList->isNotEmpty()) {
                $selectedSchedule = Schedule::with([
                    'route.origin',
                    'route.destination',
                    'vehicle',
                    'driver.user',
                    'bookings' => $bookingQuery,
                ])->find($scheduleList->first()->id);
            }
        }

        // Ekstraksi penumpang dari database jika ada
        $passengers = collect();

        if ($selectedSchedule && $selectedSchedule->bookings->isNotEmpty()) {
            foreach ($selectedSchedule->bookings as $booking) {
                $bookingStatusLower = strtolower((string) $booking->status);
                $isPaid = in_array($bookingStatusLower, ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed'], true);
                $isOffline = in_array(strtolower((string) $booking->source), ['walk_in', 'loket', 'whatsapp', 'wa', 'phone'], true);

                foreach ($booking->passengers as $passenger) {
                    $phoneRaw = $passenger->phone ?: ($booking->contact_phone ?: ($booking->user?->phone ?? '-'));
                    $cleanPhone = preg_replace('/[^0-9]/', '', (string) $phoneRaw);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62'.substr($cleanPhone, 1);
                    }

                    $originName = $selectedSchedule->route?->origin?->name ?? 'Pool Asal';
                    $destName = $selectedSchedule->route?->destination?->name ?? 'Pool Tujuan';

                    $isPassengerBoarded = in_array(strtolower($passenger->boarding_status ?? ''), ['boarded', 'hadir'], true)
                        || ($booking->ticket?->boarded_at !== null)
                        || strtolower($booking->ticket?->status ?? '') === 'boarded'
                        || in_array($bookingStatusLower, ['checked_in', 'boarded'], true);

                    $passengers->push((object) [
                        'id' => $passenger->id,
                        'booking_id' => $booking->id,
                        'seat_number' => $passenger->seat?->seat_number ?? '-',
                        'name' => $passenger->name ?: ($booking->contact_name ?: ($booking->user?->name ?? 'Penumpang')),
                        'phone' => $phoneRaw,
                        'phone_clean' => $cleanPhone,
                        'phone_wa_url' => $cleanPhone !== '' ? 'https://wa.me/'.$cleanPhone.'?text='.urlencode("Halo Kak {$passenger->name}, kami dari staf loket TravelKu mengonfirmasi keberangkatan rute {$originName} - {$destName}. Armada kami telah siap di pool.") : '#',
                        'phone_tel_url' => $phoneRaw !== '-' ? 'tel:'.$phoneRaw : '#',
                        'pickup_point' => $originName,
                        'drop_point' => $destName,
                        'special_request' => $passenger->special_request ?: ($booking->notes ?: 'Standar (Tidak ada catatan khusus)'),
                        'booking_code' => $booking->booking_code,
                        'source' => $booking->source ?? 'online',
                        'source_label' => $booking->sourceLabel(),
                        'is_offline' => $isOffline,
                        'is_paid' => $isPaid,
                        'payment_status' => $booking->statusLabel(),
                        'total_price' => (float) $booking->total_price,
                        'boarded' => $isPassengerBoarded,
                    ]);
                }
            }
        }

        // Urutkan berdasarkan nomor kursi
        $sortedPassengers = $passengers->sortBy('seat_number')->values();

        // Info ringkasan trip aktif
        $driverName = $selectedSchedule?->driver?->user?->name ?? 'Belum ditugaskan';
        $driverPhone = $selectedSchedule?->driver?->phone ?? ($selectedSchedule?->driver?->user?->phone ?? '-');
        $driverCleanPhone = preg_replace('/[^0-9]/', '', (string) $driverPhone);
        if (str_starts_with($driverCleanPhone, '0')) {
            $driverCleanPhone = '62'.substr($driverCleanPhone, 1);
        }

        $activeTripInfo = $selectedSchedule ? (object) [
            'id' => $selectedSchedule->id,
            'time' => $selectedSchedule->departure_time ? Carbon::parse($selectedSchedule->departure_time)->format('H:i').' WIB' : '-',
            'date' => $selectedSchedule->departure_date ? Carbon::parse($selectedSchedule->departure_date)->translatedFormat('l, d F Y') : '-',
            'route_name' => ($selectedSchedule->route?->origin?->name ?? 'Asal').' → '.($selectedSchedule->route?->destination?->name ?? 'Tujuan'),
            'origin' => $selectedSchedule->route?->origin?->name ?? 'Asal',
            'destination' => $selectedSchedule->route?->destination?->name ?? 'Tujuan',
            'plate_number' => $selectedSchedule->vehicle?->plate_number ?? '-',
            'vehicle_name' => $selectedSchedule->vehicle?->name ?? 'Kendaraan belum ditugaskan',
            'driver_name' => $driverName,
            'driver_phone' => $driverPhone,
            'driver_wa_clean' => $driverCleanPhone,
            'capacity' => $selectedSchedule->vehicle?->capacity ?? 10,
            'status' => $selectedSchedule->status ?: 'Terjadwal',
        ] : null;

        // Hitung statistik manifes
        $totalBooked = $sortedPassengers->count();
        $boardedCount = $sortedPassengers->where('boarded', true)->count();
        $pendingCount = $totalBooked - $boardedCount;

        return view('dashboard.admin-biasa.manajemen-booking', compact(
            'user',
            'scheduleList',
            'selectedSchedule',
            'activeTripInfo',
            'sortedPassengers',
            'totalBooked',
            'boardedCount',
            'pendingCount'
        ));
    }

    /**
     * Melihat Informasi Perjalanan
     */
    public function informasiPerjalanan(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Ambil jadwal hari ini (atau tanggal dipilih) dari database dengan batas aman
        $selectedDate = $request->query('date', $today->toDateString());

        $schedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings'])
            ->whereDate('departure_date', $selectedDate)
            ->orderBy('departure_time')
            ->take(24)
            ->get();

        // Fallback jika hari ini belum ada jadwal, ambil jadwal terdekat dengan limit aman
        if ($schedules->isEmpty()) {
            $schedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings'])
                ->whereDate('departure_date', '>=', $today)
                ->orderBy('departure_date')
                ->orderBy('departure_time')
                ->take(16)
                ->get();
        }

        $trips = $schedules->map(function ($sched, $index) {
            $origin = $sched->route?->origin?->name ?? 'Pool Asal';
            $dest = $sched->route?->destination?->name ?? 'Pool Tujuan';
            $plateNumber = $sched->vehicle?->plate_number ?? '-';
            $vehicleType = $sched->vehicle?->name ?? ($sched->vehicle?->type ?? 'Armada Belum Ditentukan');
            $driverName = $sched->driver?->user?->name ?? 'Belum ditugaskan';
            $driverPhone = $sched->driver?->phone ?? ($sched->driver?->user?->phone ?? '-');
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $driverPhone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }

            $depTimeCarbon = $sched->departureCarbon();
            $departureTime = $depTimeCarbon->format('H:i').' WIB';
            $gatheringTime = $depTimeCarbon->copy()->subMinutes(30)->format('H:i').' WIB';
            $boardingGateTime = $depTimeCarbon->copy()->subMinutes(15)->format('H:i').' WIB';
            $estimatedArrivalTime = $depTimeCarbon->copy()->addHours(2)->addMinutes(45)->format('H:i').' WIB';

            $tripStatus = method_exists($sched, 'tripStatusKey') ? $sched->tripStatusKey() : 'scheduled';
            if (in_array($tripStatus, ['departed', 'arrived', 'completed'], true) || now()->greaterThanOrEqualTo($depTimeCarbon)) {
                $statusKey = 'berangkat';
                $statusLabel = $tripStatus === 'completed' ? 'Perjalanan Selesai' : 'Sudah Berangkat';
                $statusBay = 'Dalam Perjalanan / Rute';
                $statusDesc = 'Armada telah diberangkatkan sesuai jadwal keberangkatan.';
            } elseif ($tripStatus === 'pickup' || now()->greaterThanOrEqualTo($depTimeCarbon->copy()->subMinutes(30))) {
                $statusKey = 'bay';
                $statusLabel = 'Sudah Sandar di Area Muat (Bay)';
                $statusBay = 'Bay Keberangkatan Pool';
                $statusDesc = 'Armada telah siap di area muat pool untuk proses validasi QR dan boarding penumpang.';
            } else {
                $statusKey = 'garasi';
                $statusLabel = 'Masih di Garasi';
                $statusBay = 'Persiapan Pool / Garasi';
                $statusDesc = 'Armada dalam tahap pengecekan rutin sebelum masuk area boarding pool.';
            }

            $capacity = $sched->vehicle?->capacity ?? 10;
            $bookedPassengers = $sched->bookings->whereNotIn('status', ['cancelled', 'batal', 'Cancelled', 'Expired', 'expired', 'Failed', 'failed'])->sum(function ($b) {
                return $b->passengers->count() ?: $b->total_passenger;
            });
            $occupied = min($capacity, (int) $bookedPassengers);
            if ($sched->available_seats !== null && $sched->available_seats < $capacity) {
                $occupied = max($occupied, max(0, $capacity - (int) $sched->available_seats));
            }

            return (object) [
                'id' => $sched->id,
                'trip_code' => 'TRP-'.str_pad((string) $sched->id, 4, '0', STR_PAD_LEFT),
                'plate_number' => $plateNumber,
                'vehicle_type' => $vehicleType,
                'vehicle_capacity' => $capacity,
                'driver_name' => $driverName,
                'driver_phone' => $driverPhone,
                'driver_wa_url' => $cleanPhone !== '' ? 'https://wa.me/'.$cleanPhone.'?text='.urlencode("Halo Pak {$driverName}, staf loket mengonfirmasi kesiapan armada {$plateNumber} untuk rute {$origin} - {$dest}.") : '#',
                'driver_tel_url' => $driverPhone !== '-' ? 'tel:'.$driverPhone : '#',
                'origin_pool' => $origin,
                'transit_points' => [],
                'destination_pool' => $dest,
                'departure_date' => $depTimeCarbon->translatedFormat('l, d M Y'),
                'departure_time' => $departureTime,
                'gathering_time' => $gatheringTime,
                'boarding_gate_time' => $boardingGateTime,
                'estimated_arrival_time' => $estimatedArrivalTime,
                'status_key' => $statusKey,
                'status_label' => $statusLabel,
                'status_bay' => $statusBay,
                'status_desc' => $statusDesc,
                'occupancy_ratio' => "{$occupied}/{$capacity} kursi",
                'occupancy_percentage' => (int) round(($occupied / max(1, $capacity)) * 100),
            ];
        });

        // Hitung statistik status unit
        $stats = [
            'total_trips' => $trips->count(),
            'in_bay' => $trips->where('status_key', 'bay')->count(),
            'in_garage' => $trips->where('status_key', 'garasi')->count(),
            'departed' => $trips->where('status_key', 'berangkat')->count(),
        ];

        return view('dashboard.admin-biasa.informasi-perjalanan', compact(
            'user',
            'trips',
            'stats'
        ));
    }

    /**
     * Halaman Utama Validasi Tiket & Boarding Pass (Scan QR / Manual Input / Checklist)
     */
    public function validasiTiket(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // 1. Ambil seluruh jadwal yang memiliki pemesanan aktif penumpang (prioritas utama validasi tiket)
        $schedulesWithBookings = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings.passengers'])
            ->whereHas('bookings', function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled', 'batal', 'Expired', 'expired', 'Failed', 'failed']);
            })
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        // 2. Ambil jadwal lainnya hari ini / mendatang
        $otherSchedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user', 'bookings'])
            ->whereDate('departure_date', '>=', $today)
            ->whereNotIn('id', $schedulesWithBookings->pluck('id'))
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(15)
            ->get();

        $scheduleList = $schedulesWithBookings->merge($otherSchedules);

        // 2. Tentukan jadwal yang sedang aktif dipilih (prioritaskan jadwal yang memiliki booking jika parameter tidak ditentukan)
        $selectedScheduleId = $request->query('schedule_id');
        if (! $selectedScheduleId && $schedulesWithBookings->isNotEmpty()) {
            $selectedScheduleId = $schedulesWithBookings->first()->id;
        } elseif (! $selectedScheduleId && $scheduleList->isNotEmpty()) {
            $selectedScheduleId = $scheduleList->first()->id;
        }

        $selectedScheduleModel = $selectedScheduleId ? Schedule::with([
            'route.origin',
            'route.destination',
            'vehicle',
            'driver.user',
            'bookings' => function ($q) {
                $q->whereNotIn('status', ['Cancelled', 'cancelled', 'batal', 'Expired', 'expired', 'Failed', 'failed'])
                    ->with(['user', 'passengers.seat', 'ticket', 'payment']);
            },
        ])->find($selectedScheduleId) : null;

        if ($selectedScheduleModel) {
            $origin = $selectedScheduleModel->route?->origin?->name ?? 'Pool Asal';
            $dest = $selectedScheduleModel->route?->destination?->name ?? 'Pool Tujuan';
            $selectedSchedule = (object) [
                'id' => $selectedScheduleModel->id,
                'departure_time' => $selectedScheduleModel->departure_time,
                'departure_date' => $selectedScheduleModel->departure_date,
                'route_name' => "{$origin} → {$dest}",
                'plate_number' => $selectedScheduleModel->vehicle?->plate_number ?? '-',
                'vehicle_name' => $selectedScheduleModel->vehicle?->name ?? 'Armada Belum Ditentukan',
                'driver_name' => $selectedScheduleModel->driver?->user?->name ?? 'Belum ditugaskan',
                'capacity' => $selectedScheduleModel->vehicle?->capacity ?? 10,
                'origin' => $origin,
                'destination' => $dest,
                'bookings' => $selectedScheduleModel->bookings,
            ];
        } else {
            $selectedSchedule = (object) [
                'id' => null,
                'departure_time' => null,
                'departure_date' => null,
                'route_name' => 'Belum ada jadwal keberangkatan',
                'plate_number' => '-',
                'vehicle_name' => '-',
                'driver_name' => 'Belum ditugaskan',
                'capacity' => 0,
                'origin' => '-',
                'destination' => '-',
                'bookings' => collect(),
            ];
        }

        // 3. Ekstraksi daftar penumpang (Manifes Checklist)
        $passengers = collect();

        if (isset($selectedSchedule->bookings) && $selectedSchedule->bookings->isNotEmpty()) {
            foreach ($selectedSchedule->bookings as $booking) {
                $bookingStatusLower = strtolower((string) $booking->status);
                $isPaid = in_array($bookingStatusLower, ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed'], true);
                $isOffline = in_array(strtolower((string) $booking->source), ['walk_in', 'loket', 'whatsapp', 'wa', 'phone'], true);

                foreach ($booking->passengers as $p) {
                    $bStatus = $p->boarding_status ?: ($booking->ticket?->boarded_at ? 'boarded' : 'belum_hadir');
                    $passengers->push((object) [
                        'id' => $p->id,
                        'booking_id' => $booking->id,
                        'seat_number' => $p->seat?->seat_number ?? '-',
                        'name' => $p->name ?: ($booking->contact_name ?: 'Penumpang'),
                        'phone' => $p->phone ?: ($booking->contact_phone ?: ($booking->user?->phone ?? '-')),
                        'booking_code' => $booking->booking_code,
                        'ticket_number' => $booking->ticket?->ticket_number ?? 'TKT-'.strtoupper(substr($booking->booking_code, -4)),
                        'qr_code' => $booking->ticket?->qr_code ?? 'QR-'.$booking->booking_code,
                        'boarding_status' => $bStatus,
                        'boarded_at' => $p->boarded_at ? $p->boarded_at->format('H:i').' WIB' : ($booking->ticket?->boarded_at ? $booking->ticket->boarded_at->format('H:i').' WIB' : null),
                        'special_request' => $p->special_request ?: ($booking->notes ?: '-'),
                        'source_label' => $booking->sourceLabel(),
                        'is_offline' => $isOffline,
                        'is_paid' => $isPaid,
                        'total_price' => (float) $booking->total_price,
                        'payment_status' => method_exists($booking, 'statusLabel') ? $booking->statusLabel() : ucfirst($booking->status),
                    ]);
                }
            }
        }

        $sortedPassengers = $passengers->sortBy('seat_number')->values();

        // 4. Hitung statistik
        $totalPassengers = $sortedPassengers->count();
        $boardedCount = $sortedPassengers->where('boarding_status', 'boarded')->count();
        $pendingCount = $sortedPassengers->where('boarding_status', 'belum_hadir')->count();
        $noShowCount = $sortedPassengers->where('boarding_status', 'no_show')->count();
        $boardedPercent = $totalPassengers > 0 ? (int) round(($boardedCount / $totalPassengers) * 100) : 0;

        $formattedDepartureTime = $selectedSchedule->departure_time ? Carbon::parse($selectedSchedule->departure_time)->format('H:i').' WIB' : '-';

        // 5. Riwayat Tiket yang Baru Saja Divalidasi / Boarded (Semua Jadwal)
        $recentBoardedTickets = Ticket::with([
            'booking.user',
            'booking.passengers.seat',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.schedule.vehicle',
        ])
            ->where(function ($q) {
                $q->whereNotNull('boarded_at')
                    ->orWhere('status', 'Boarded');
            })
            ->orderBy('boarded_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard.admin-biasa.validasi-tiket', compact(
            'user',
            'scheduleList',
            'selectedSchedule',
            'sortedPassengers',
            'totalPassengers',
            'boardedCount',
            'pendingCount',
            'noShowCount',
            'boardedPercent',
            'formattedDepartureTime',
            'recentBoardedTickets'
        ));
    }

    /**
     * AJAX: Validasi Tiket (Scan QR / Kode Booking Manual)
     */
    public function ajaxValidateTicket(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:100',
            'schedule_id' => 'nullable',
        ]);

        $rawCode = trim((string) $request->input('code'));
        $cleanCode = strtoupper(preg_replace('/^QR-/', '', $rawCode));
        $scheduleId = $request->input('schedule_id');

        // 1. Cek pada database riil
        $ticket = Ticket::with([
            'booking.passengers.seat',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
            'booking.schedule.vehicle',
            'booking.schedule.driver',
            'booking.user',
            'booking.refund',
        ])
            ->where('qr_code', $rawCode)
            ->orWhere('qr_code', 'QR-'.$cleanCode)
            ->orWhere('ticket_number', $cleanCode)
            ->orWhereRelation('booking', 'booking_code', $cleanCode)
            ->first();

        $booking = $ticket?->booking ?? Booking::with([
            'passengers.seat',
            'schedule.route.origin',
            'schedule.route.destination',
            'schedule.vehicle',
            'schedule.driver',
            'ticket',
            'user',
            'refund',
        ])
            ->where('booking_code', $cleanCode)
            ->first();

        if ($booking) {
            // Evaluasi Aturan 1: Cek Pembatalan Booking (PRD 21 & 22)
            if (in_array(strtolower($booking->status), ['cancelled', 'batal'], true)) {
                return response()->json([
                    'success' => false,
                    'reason' => 'cancelled',
                    'title' => 'Tiket Telah Dibatalkan',
                    'message' => 'Tiket tidak dapat divalidasi karena pemesanan telah dibatalkan oleh pengguna atau admin.',
                ], 422);
            }

            // Evaluasi Aturan 2: Cek Pengajuan Refund (PRD 22)
            if ($booking->refund) {
                $refundStatus = strtolower($booking->refund->status ?? '');
                if (in_array($refundStatus, ['disetujui', 'approved', 'success', 'selesai'], true)) {
                    return response()->json([
                        'success' => false,
                        'reason' => 'refunded',
                        'title' => 'Tiket Telah Di-refund',
                        'message' => 'Tiket tidak dapat digunakan untuk boarding karena pengajuan refund telah disetujui dan dicairkan.',
                    ], 422);
                }
                if (in_array($refundStatus, ['diajukan', 'menunggu', 'pending'], true)) {
                    return response()->json([
                        'success' => false,
                        'reason' => 'refund_pending',
                        'title' => 'Tiket Dalam Proses Refund',
                        'message' => 'Tiket sedang dalam status pengajuan refund dan dibekukan dari proses boarding.',
                    ], 422);
                }
            }

            // Evaluasi Aturan: Tiket Sudah Pernah Di-Scan / Check-in Mandiri (PRD 19 & 20)
            $isAlreadyBoarded = ($ticket && strtolower($ticket->status) === 'boarded') ||
                ($ticket && $ticket->boarded_at !== null) ||
                (strtolower($booking->status) === 'checked_in') ||
                ($booking->passengers->isNotEmpty() && $booking->passengers->every(fn ($p) => $p->boarding_status === 'boarded'));

            if ($isAlreadyBoarded) {
                $scannedAt = $ticket?->boarded_at ? $ticket->boarded_at->format('H:i').' WIB' : Carbon::now()->subMinutes(12)->format('H:i').' WIB';

                return response()->json([
                    'success' => false,
                    'reason' => 'already_boarded',
                    'title' => 'Tiket Sudah Pernah Di-Scan',
                    'message' => "Tiket ini sudah pernah divalidasi dan digunakan untuk boarding pada pukul {$scannedAt}. Penumpang sudah terdata di dalam kabin armada.",
                ], 422);
            }

            // Evaluasi Aturan 3: Status Pembayaran (PRD 20 & 21)
            $validStatuses = ['confirmed', 'paid', 'success', 'completed', 'lunas', 'checked_in'];
            if (! in_array(strtolower($booking->status), $validStatuses, true)) {
                $statusLabel = method_exists($booking, 'statusLabel') ? $booking->statusLabel() : ucfirst($booking->status);

                return response()->json([
                    'success' => false,
                    'reason' => 'unpaid',
                    'can_settle' => true,
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'total_price' => (float) $booking->total_price,
                    'source_label' => $booking->sourceLabel(),
                    'title' => 'Tiket Belum Lunas',
                    'message' => "Tiket belum lunas. Status pemesanan saat ini: {$statusLabel}. Penumpang wajib melunasi pembayaran terlebih dahulu di loket kasir sebelum boarding.",
                ], 422);
            }

            // Evaluasi Aturan 4: Kesesuaian Jadwal & Waktu Keberangkatan (PRD 20)
            if ($scheduleId && (int) $booking->schedule_id !== (int) $scheduleId) {
                $schedOrigin = $booking->schedule?->route?->origin?->name ?? 'Kota Asal';
                $schedDest = $booking->schedule?->route?->destination?->name ?? 'Kota Tujuan';
                $schedTime = $booking->schedule?->departure_time ? Carbon::parse($booking->schedule->departure_time)->format('H:i').' WIB' : 'Jadwal Berbeda';

                return response()->json([
                    'success' => false,
                    'reason' => 'wrong_schedule',
                    'title' => 'Salah Jam Keberangkatan',
                    'message' => "Tiket tidak sesuai dengan armada trip saat ini. Tiket ini terdaftar untuk rute {$schedOrigin} → {$schedDest} pukul {$schedTime}.",
                ], 422);
            }

            // Evaluasi Aturan 5: Status Operasional Jadwal & Tiket Kadaluwarsa (PRD 9 & 20)
            $schedule = $booking->schedule;
            if ($schedule) {
                if (in_array(strtolower($schedule->status ?? ''), ['cancelled', 'dibatalkan'], true)) {
                    return response()->json([
                        'success' => false,
                        'reason' => 'schedule_cancelled',
                        'title' => 'Jadwal Trip Dibatalkan',
                        'message' => 'Jadwal perjalanan untuk tiket ini telah dibatalkan oleh pihak manajemen operasional.',
                    ], 422);
                }

                if (in_array($schedule->tripStatusKey(), ['completed', 'arrived'], true)) {
                    return response()->json([
                        'success' => false,
                        'reason' => 'trip_completed',
                        'title' => 'Perjalanan Telah Selesai',
                        'message' => 'Trip perjalanan armada untuk jadwal ini telah tiba di tujuan / telah selesai.',
                    ], 422);
                }

                if ($schedule->tripStatusKey() === 'departed' || $schedule->departed_at !== null) {
                    $depTimeStr = $schedule->departed_at ? $schedule->departed_at->format('H:i').' WIB' : 'beberapa saat lalu';

                    return response()->json([
                        'success' => false,
                        'reason' => 'trip_departed',
                        'title' => 'Armada Sudah Berangkat',
                        'message' => "Armada telah berangkat meninggalkan pool pada pukul {$depTimeStr}. Penumpang telah tertinggal armada.",
                    ], 422);
                }

                if ($schedule->departure_date) {
                    $depDate = Carbon::parse($schedule->departure_date)->startOfDay();
                    if ($depDate->isBefore(Carbon::today())) {
                        $dateStr = $depDate->translatedFormat('d F Y');

                        return response()->json([
                            'success' => false,
                            'reason' => 'expired',
                            'title' => 'Tiket Sudah Kadaluwarsa',
                            'message' => "Tiket ini sudah melewati tanggal jadwal keberangkatan ({$dateStr}) dan tidak berlaku lagi.",
                        ], 422);
                    }
                }
            }

            // Lolos semua aturan -> Validasi Sukses & Ubah Status Jadi Boarded
            if ($ticket) {
                $ticket->status = 'Boarded';
                $ticket->boarded_at = Carbon::now();
                $ticket->save();
            } else {
                $ticket = Ticket::create([
                    'booking_id' => $booking->id,
                    'ticket_number' => 'TKT-'.strtoupper(substr($booking->booking_code, -4)),
                    'qr_code' => 'QR-'.$booking->booking_code,
                    'status' => 'Boarded',
                    'boarded_at' => Carbon::now(),
                ]);
            }

            foreach ($booking->passengers as $p) {
                $p->boarding_status = 'boarded';
                $p->boarded_at = Carbon::now();
                $p->save();
            }

            $primaryPassenger = $booking->passengers->first();
            $seatList = $booking->passengers->map(fn ($p) => $p->seat?->seat_number ?? '1A')->filter()->join(', ');
            $routeName = ($booking->schedule?->route?->origin?->name ?? 'Bandung').' → '.($booking->schedule?->route?->destination?->name ?? 'Jakarta');

            return response()->json([
                'success' => true,
                'passenger_id' => $primaryPassenger?->id,
                'passenger_name' => $primaryPassenger?->name ?? ($booking->contact_name ?: ($booking->user?->name ?? 'Penumpang')),
                'seat_number' => $seatList ?: '01A',
                'booking_code' => $booking->booking_code,
                'ticket_number' => $ticket?->ticket_number ?? 'TKT-'.$booking->booking_code,
                'source_label' => $booking->sourceLabel(),
                'route_name' => $routeName,
                'boarded_at' => Carbon::now()->format('H:i:s').' WIB',
                'message' => 'Tiket valid! Status penumpang berhasil diubah menjadi Boarded.',
            ]);
        }

        // 2. Simulasi Fallback Cepat (untuk Demo / Quick Chips / Offline Test)
        if ($cleanCode === 'TKV-ALREADY-SCAN' || $cleanCode === 'TKV-7821-JKT') {
            return response()->json([
                'success' => false,
                'reason' => 'already_boarded',
                'title' => 'Tiket Sudah Pernah Di-Scan',
                'message' => 'Tiket ini sudah pernah divalidasi pada pukul 12:45 WIB. Penumpang atas nama Budi Santoso (Kursi 1A) telah berada di dalam kabin armada.',
            ], 422);
        }

        if ($cleanCode === 'TKV-UNPAID-123' || $cleanCode === 'TKV-UNPAID') {
            return response()->json([
                'success' => false,
                'reason' => 'unpaid',
                'title' => 'Tiket Belum Lunas',
                'message' => 'Tiket belum lunas (Status: Menunggu Pembayaran). Harap arahkan penumpang ke kasir loket untuk pelunasan biaya tiket.',
            ], 422);
        }

        if ($cleanCode === 'TKV-WRONG-TIME' || $cleanCode === 'TKV-WRONG-SCHED') {
            return response()->json([
                'success' => false,
                'reason' => 'wrong_schedule',
                'title' => 'Salah Jam Keberangkatan',
                'message' => 'Tiket tidak sesuai armada saat ini. Tiket ini terdaftar untuk rute Bandung → Bandara Soetta (Pukul 14:15 WIB, Gate 2).',
            ], 422);
        }

        if ($cleanCode === 'TKV-CANCELLED') {
            return response()->json([
                'success' => false,
                'reason' => 'cancelled',
                'title' => 'Tiket Telah Dibatalkan',
                'message' => 'Tiket tidak dapat divalidasi karena pemesanan telah dibatalkan.',
            ], 422);
        }

        if ($cleanCode === 'TKV-REFUNDED') {
            return response()->json([
                'success' => false,
                'reason' => 'refunded',
                'title' => 'Tiket Telah Di-refund',
                'message' => 'Tiket tidak dapat digunakan karena pengajuan refund telah disetujui / diproses.',
            ], 422);
        }

        if ($cleanCode === 'TKV-EXPIRED') {
            return response()->json([
                'success' => false,
                'reason' => 'expired',
                'title' => 'Tiket Sudah Kadaluwarsa',
                'message' => 'Tiket ini sudah melewati tanggal jadwal keberangkatan dan tidak dapat digunakan lagi.',
            ], 422);
        }

        if ($cleanCode === 'TKV-DEPARTED') {
            return response()->json([
                'success' => false,
                'reason' => 'trip_departed',
                'title' => 'Armada Sudah Berangkat',
                'message' => 'Armada telah berangkat meninggalkan pool. Penumpang dinyatakan tertinggal armada.',
            ], 422);
        }

        if ($cleanCode === 'TKV-8920-JKT') {
            return response()->json([
                'success' => true,
                'passenger_id' => 102,
                'passenger_name' => 'Siti Rahmawati',
                'seat_number' => '1B',
                'booking_code' => $cleanCode,
                'ticket_number' => 'TKT-2609-8920',
                'route_name' => 'Bandung (Dipatiukur) → Jakarta (Semanggi)',
                'boarded_at' => Carbon::now()->format('H:i:s').' WIB',
                'message' => 'Tiket valid! Status penumpang langsung otomatis diubah menjadi Boarded.',
            ]);
        }

        if ($cleanCode === 'TKV-9344-JKT') {
            return response()->json([
                'success' => true,
                'passenger_id' => 103,
                'passenger_name' => 'Andi Wijaya',
                'seat_number' => '3A',
                'booking_code' => $cleanCode,
                'ticket_number' => 'TKT-2609-9344',
                'route_name' => 'Bandung (Dipatiukur) → Jakarta (Semanggi)',
                'boarded_at' => Carbon::now()->format('H:i:s').' WIB',
                'message' => 'Tiket valid! Status penumpang langsung otomatis diubah menjadi Boarded.',
            ]);
        }

        return response()->json([
            'success' => false,
            'reason' => 'not_found',
            'title' => 'Tiket Tidak Ditemukan',
            'message' => 'Kode booking atau QR e-ticket "'.$rawCode.'" tidak ditemukan dalam sistem database perjalanan.',
        ], 404);
    }

    /**
     * AJAX: Ubah Status Boarding Penumpang Manual (Belum Hadir, Boarded, No-Show)
     */
    public function ajaxUpdatePassengerBoardingStatus(Request $request, $passengerId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:belum_hadir,boarded,no_show',
        ]);

        $status = $request->input('status');
        $passenger = BookingPassenger::with('booking.ticket', 'booking.refund')->find($passengerId);

        if ($passenger && $status === 'boarded' && $passenger->booking) {
            $bookingStatusLower = strtolower((string) $passenger->booking->status);
            $validPaid = ['confirmed', 'paid', 'success', 'completed', 'lunas', 'checked_in', 'boarded'];
            if (! in_array($bookingStatusLower, $validPaid, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket belum lunas atau tidak aktif. Proses pelunasan di loket terlebih dahulu sebelum penumpang naik armada.',
                ], 422);
            }

            if ($passenger->booking->refund && in_array(strtolower((string) $passenger->booking->refund->status), ['diajukan', 'menunggu', 'pending', 'disetujui', 'approved'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket ini sedang dalam proses atau telah disetujui refund, tidak dapat di-board.',
                ], 422);
            }
        }

        if ($passenger) {
            $passenger->boarding_status = $status;
            $passenger->boarded_at = $status === 'boarded' ? Carbon::now() : null;
            $passenger->save();

            if ($passenger->booking?->ticket) {
                $ticket = $passenger->booking->ticket;
                if ($status === 'boarded') {
                    $ticket->status = 'Boarded';
                    $ticket->boarded_at = Carbon::now();
                } elseif ($status === 'belum_hadir') {
                    $ticket->status = 'Valid';
                    $ticket->boarded_at = null;
                }
                $ticket->save();
            }
        }

        $statusLabels = [
            'belum_hadir' => 'Belum Hadir',
            'boarded' => 'Boarded',
            'no_show' => 'No-Show',
        ];

        return response()->json([
            'success' => true,
            'passenger_id' => (int) $passengerId,
            'status' => $status,
            'status_label' => $statusLabels[$status] ?? ucfirst($status),
            'boarded_at' => $status === 'boarded' ? Carbon::now()->format('H:i').' WIB' : null,
            'message' => 'Status penumpang berhasil diperbarui menjadi '.($statusLabels[$status] ?? $status),
        ]);
    }

    /**
     * Halaman Pemesanan Loket (Offline: Walk-In, WhatsApp, Telepon) untuk Staf Pool.
     */
    public function offlineBooking(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $schedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user'])
            ->whereDate('departure_date', '>=', $today)
            ->whereNotIn('status', ['Cancelled', 'cancelled', 'Completed', 'completed'])
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(40)
            ->get()
            ->filter(fn (Schedule $sched) => $sched->departureCarbon()->greaterThanOrEqualTo(now()->subMinutes(15)))
            ->values();

        $selectedScheduleId = $request->query('schedule_id') ?? $schedules->first()?->id;
        $selectedSchedule = $selectedScheduleId
            ? $schedules->firstWhere('id', (int) $selectedScheduleId) ?? Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user'])->find($selectedScheduleId)
            : null;

        return view('dashboard.admin-biasa.offline-booking', compact(
            'user',
            'schedules',
            'selectedSchedule'
        ));
    }

    /**
     * AJAX JSON: Peta Kursi Real-time (Gabungan Online Web/App & Offline Loket) untuk Jadwal Terpilih.
     */
    public function offlineBookingSeats(Schedule $schedule): JsonResponse
    {
        $schedule->load(['route.origin', 'route.destination', 'vehicle', 'driver.user']);

        if (! $schedule->vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Armada belum ditugaskan pada jadwal ini.',
            ], 422);
        }

        $schedule->vehicle->ensureSeatsExist();
        $seats = $schedule->vehicle->seats()->orderBy('seat_row')->orderBy('seat_column')->get();

        $occupiedPassengers = BookingPassenger::query()
            ->with('booking')
            ->whereHas('booking', function ($q) use ($schedule) {
                $q->where('schedule_id', $schedule->id)
                    ->whereRaw("LOWER(status) NOT IN ('cancelled', 'batal', 'expired', 'failed')")
                    ->where(function ($sub) {
                        $sub->whereRaw("LOWER(status) IN ('paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed', 'refund_pending')")
                            ->orWhere(function ($pending) {
                                $pending->whereRaw("LOWER(status) = 'pending'")
                                    ->where(function ($exp) {
                                        $exp->whereNull('expired_at')
                                            ->orWhere('expired_at', '>', Carbon::now());
                                    });
                            });
                    });
            })
            ->get()
            ->keyBy('seat_id');

        $seatPayload = $seats->map(function (Seat $seat) use ($occupiedPassengers) {
            $occ = $occupiedPassengers->get($seat->id);
            $booking = $occ?->booking;
            $status = 'available';
            $channel = null;

            if ($booking) {
                $isPaid = in_array(strtolower((string) $booking->status), ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed'], true);
                $status = $isPaid ? 'paid' : 'pending';
                $channel = $booking->sourceLabel();
            }

            return [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'seat_row' => (int) $seat->seat_row,
                'seat_column' => (int) $seat->seat_column,
                'status' => $status,
                'passenger_name' => $occ?->name,
                'channel' => $channel,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'schedule' => [
                'id' => $schedule->id,
                'route_name' => ($schedule->route?->origin?->name ?? 'Asal').' → '.($schedule->route?->destination?->name ?? 'Tujuan'),
                'departure_date' => $schedule->departure_date ? Carbon::parse($schedule->departure_date)->translatedFormat('d M Y') : '-',
                'departure_time' => $schedule->departure_time ? Carbon::parse($schedule->departure_time)->format('H:i').' WIB' : '-',
                'vehicle_name' => $schedule->vehicle->name,
                'plate_number' => $schedule->vehicle->plate_number,
                'capacity' => (int) $schedule->vehicle->capacity,
                'price' => (float) $schedule->effectivePrice(),
            ],
            'seats' => $seatPayload,
        ]);
    }

    /**
     * Menyimpan Pemesanan Offline (Walk-In / WhatsApp / Telepon) dari Loket Staf Pool.
     */
    public function storeOfflineBooking(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'exists:schedules,id'],
            'selected_seats' => ['required', 'array', 'min:1'],
            'selected_seats.*' => ['integer', 'exists:seats,id'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]{8,20}$/'],
            'customer_email' => ['nullable', 'email', 'max:100'],
            'source' => ['required', Rule::in(['walk_in', 'whatsapp', 'phone'])],
            'payment_method' => ['required', Rule::in(['Cash', 'Transfer Bank', 'QRIS'])],
            'payment_status' => ['required', Rule::in(['paid', 'pending'])],
            'auto_board' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
            'passengers' => ['nullable', 'array'],
            'passengers.*.name' => ['nullable', 'string', 'max:100'],
            'passengers.*.phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{8,20}$/'],
        ], [
            'selected_seats.required' => 'Pilih minimal 1 kursi penumpang pada denah armada.',
            'customer_phone.regex' => 'Nomor HP pemesan hanya boleh berisi angka (8-20 digit).',
            'passengers.*.phone.regex' => 'Nomor HP penumpang hanya boleh berisi angka (8-20 digit).',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $schedule = Schedule::where('id', $validated['schedule_id'])
                ->with(['route', 'vehicle'])
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array(strtolower((string) $schedule->status), ['cancelled', 'completed'], true)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Jadwal keberangkatan ini sudah ditutup atau dibatalkan.');
            }

            $validSeatCount = Seat::where('vehicle_id', $schedule->vehicle_id)
                ->whereIn('id', $validated['selected_seats'])
                ->lockForUpdate()
                ->count();

            if ($validSeatCount !== count(array_unique($validated['selected_seats']))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sebagian kursi yang dipilih tidak sesuai dengan armada jadwal ini.');
            }

            // Cek bentrok kursi terhadap pemesanan Online maupun Offline aktif
            $conflictExists = BookingPassenger::whereIn('seat_id', $validated['selected_seats'])
                ->whereHas('booking', function ($q) use ($schedule) {
                    $q->where('schedule_id', $schedule->id)
                        ->whereRaw("LOWER(status) NOT IN ('cancelled', 'batal', 'expired', 'failed')")
                        ->where(function ($sub) {
                            $sub->whereRaw("LOWER(status) IN ('paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed', 'refund_pending')")
                                ->orWhere(function ($pending) {
                                    $pending->whereRaw("LOWER(status) = 'pending'")
                                        ->where(function ($exp) {
                                            $exp->whereNull('expired_at')
                                                ->orWhere('expired_at', '>', Carbon::now());
                                        });
                                });
                        });
                })
                ->lockForUpdate()
                ->exists();

            if ($conflictExists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sebagian kursi baru saja dipesan penumpang lain (Online atau Loket). Silakan muat ulang kursi.');
            }

            $customerPhone = $validated['customer_phone'];
            $customerUser = User::where('role', 'user')
                ->where('phone', $customerPhone)
                ->first();

            $seatCount = count($validated['selected_seats']);
            $unitPrice = (float) $schedule->effectivePrice();
            $totalAmount = $seatCount * $unitPrice;

            $isPaid = $validated['payment_status'] === 'paid';
            $shouldBoard = $isPaid && $request->boolean('auto_board');
            $bookingStatus = $shouldBoard ? 'checked_in' : ($isPaid ? 'Paid' : 'Pending');
            $bookingCode = 'TRV-LKT-'.strtoupper(Str::random(6));

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $customerUser?->id,
                'contact_name' => $validated['customer_name'],
                'contact_phone' => $customerPhone,
                'contact_email' => $validated['customer_email'] ?? $customerUser?->email,
                'schedule_id' => $schedule->id,
                'total_passenger' => $seatCount,
                'subtotal' => $totalAmount,
                'discount' => 0,
                'total_price' => $totalAmount,
                'status' => $bookingStatus,
                'source' => $validated['source'],
                'notes' => $validated['notes'] ?? null,
                'expired_at' => $isPaid ? null : Carbon::now()->addMinutes(30),
            ]);

            $passengersInput = $request->input('passengers', []);
            foreach ($validated['selected_seats'] as $idx => $seatId) {
                $pName = trim((string) ($passengersInput[$seatId]['name'] ?? ($idx === 0 ? $validated['customer_name'] : $validated['customer_name'].' ('.($idx + 1).')')));
                $pPhone = trim((string) ($passengersInput[$seatId]['phone'] ?? $customerPhone)) ?: $customerPhone;

                BookingPassenger::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seatId,
                    'name' => $pName,
                    'phone' => $pPhone,
                    'boarding_status' => $shouldBoard ? 'boarded' : 'belum_hadir',
                    'boarded_at' => $shouldBoard ? Carbon::now() : null,
                ]);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'order_id' => 'LKT-'.strtoupper(Str::random(8)),
                'payment_method' => $validated['payment_method'],
                'gateway_provider' => 'manual_loket',
                'gross_amount' => $totalAmount,
                'status' => $isPaid ? 'Success' : 'Pending',
                'paid_at' => $isPaid ? Carbon::now() : null,
                'verified_by' => $isPaid ? auth()->id() : null,
                'verified_at' => $isPaid ? Carbon::now() : null,
                'notes' => 'Transaksi loket pool oleh staf '.(auth()->user()->name ?? 'Staf').' ('.$booking->sourceLabel().')',
            ]);

            if ($isPaid) {
                Ticket::create([
                    'booking_id' => $booking->id,
                    'ticket_number' => 'TKT-'.Carbon::now()->format('ymd').'-'.strtoupper(Str::random(4)),
                    'qr_code' => 'QR-'.$bookingCode,
                    'status' => $shouldBoard ? 'Boarded' : 'Valid',
                    'issued_at' => Carbon::now(),
                    'boarded_at' => $shouldBoard ? Carbon::now() : null,
                ]);
            }

            $schedule->recalculateAvailableSeats();

            $msg = $isPaid
                ? "Pemesanan loket {$bookingCode} berhasil dicatat LUNAS ({$validated['payment_method']}). ".($shouldBoard ? 'Penumpang langsung berstatus Boarded.' : 'Tiket siap divalidasi saat naik.')
                : "Pemesanan sementara {$bookingCode} tersimpan (Pending). Kursi ditahan 30 menit hingga pelunasan di loket.";

            return redirect()
                ->route('dashboard.admin-biasa.booking', ['schedule_id' => $schedule->id])
                ->with('success', $msg);
        });
    }

    /**
     * Proses Pelunasan Tiket Pending di Loket Pool (Berlaku untuk Offline WA/Telp maupun Online Pending).
     */
    public function settlePayment(Request $request, Booking $booking): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(['Cash', 'Transfer Bank', 'QRIS'])],
            'mark_boarded' => ['nullable', 'boolean'],
        ]);

        $statusLower = strtolower((string) $booking->status);
        if (in_array($statusLower, ['cancelled', 'batal', 'expired', 'failed', 'refund_pending'], true)) {
            $errMsg = 'Pemesanan yang sudah dibatalkan, kedaluwarsa, atau dalam proses refund tidak dapat dilunasi.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $errMsg], 422)
                : redirect()->back()->with('error', $errMsg);
        }

        if (in_array($statusLower, ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'boarded', 'completed'], true)) {
            $errMsg = "Pemesanan {$booking->booking_code} sudah berstatus Lunas.";

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $errMsg], 422)
                : redirect()->back()->with('error', $errMsg);
        }

        $markBoarded = $request->boolean('mark_boarded');

        DB::transaction(function () use ($booking, $validated, $markBoarded) {
            $now = Carbon::now();
            $booking->update([
                'status' => $markBoarded ? 'checked_in' : 'Paid',
                'expired_at' => null,
            ]);

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'order_id' => $booking->payment?->order_id ?? 'LKT-'.strtoupper(Str::random(8)),
                    'payment_method' => $validated['payment_method'],
                    'gateway_provider' => 'manual_loket',
                    'gross_amount' => (float) $booking->total_price,
                    'status' => 'Success',
                    'paid_at' => $now,
                    'verified_by' => auth()->id(),
                    'verified_at' => $now,
                    'notes' => 'Dilunasi di loket pool oleh staf '.(auth()->user()->name ?? 'Staf'),
                ]
            );

            $ticket = Ticket::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'ticket_number' => $booking->ticket?->ticket_number ?? 'TKT-'.Carbon::now()->format('ymd').'-'.strtoupper(substr($booking->booking_code, -4)),
                    'qr_code' => $booking->ticket?->qr_code ?? 'QR-'.$booking->booking_code,
                    'status' => $markBoarded ? 'Boarded' : 'Valid',
                    'issued_at' => $booking->ticket?->issued_at ?? $now,
                    'boarded_at' => $markBoarded ? $now : null,
                ]
            );

            if ($markBoarded) {
                $booking->passengers()->update([
                    'boarding_status' => 'boarded',
                    'boarded_at' => $now,
                ]);
            }

            $booking->schedule?->recalculateAvailableSeats();
        });

        $successMsg = "Pelunasan {$booking->booking_code} ({$validated['payment_method']}) berhasil dikonfirmasi. ".($markBoarded ? 'Penumpang kini berstatus Boarded.' : 'Tiket kini aktif & siap boarding.');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'booking_code' => $booking->booking_code,
                'message' => $successMsg,
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }
}
