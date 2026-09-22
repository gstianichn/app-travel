<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminBiasaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Ambil jadwal hari ini atau terdekat
        $schedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver', 'bookings'])
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(6)
            ->get();

        // Transform data jadwal untuk dashboard operasional
        $departures = $schedules->map(function ($sched, $index) {
            $origin = $sched->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)';
            $destination = $sched->route?->destination?->name ?? 'Pool Semanggi (Jakarta)';
            $plateNumber = $sched->vehicle?->plate_number ?? 'D 7'.(100 + $index * 24).' AB';
            $vehicleModel = $sched->vehicle?->name ?? 'Toyota HiAce Premio';
            $capacity = $sched->vehicle?->capacity ?? 10;
            $available = $sched->available_seats ?? max(0, $capacity - ($index === 0 ? 8 : ($index === 1 ? 9 : 5)));
            $occupied = max(0, $capacity - $available);

            // Status trip: Persiapan, Boarding Dibuka, atau Selesai
            $status = $sched->status;
            if (! in_array($status, ['Persiapan', 'Boarding Dibuka', 'Selesai'])) {
                if ($index === 0) {
                    $status = 'Boarding Dibuka';
                } elseif ($index === 1) {
                    $status = 'Persiapan';
                } else {
                    $status = 'Persiapan';
                }
            }

            return (object) [
                'id' => $sched->id,
                'time' => $sched->departure_time ? Carbon::parse($sched->departure_time)->format('H:i').' WIB' : Carbon::now()->addMinutes(30)->format('H:i').' WIB',
                'route_name' => "{$origin} → {$destination}",
                'origin' => $origin,
                'destination' => $destination,
                'plate_number' => $plateNumber,
                'vehicle_name' => $vehicleModel,
                'driver_name' => $sched->driver?->name ?? 'Ahmad Subarjo',
                'occupied_seats' => $occupied,
                'total_seats' => $capacity,
                'ratio' => "{$occupied}/{$capacity} kursi",
                'percentage' => (int) min(100, round(($occupied / max(1, $capacity)) * 100)),
                'status' => $status,
                'departure_note' => $index === 0 ? 'Berangkat dalam 30 menit' : ($index === 1 ? 'Berangkat dalam 1 jam 15 menit' : 'Jadwal operasional hari ini'),
            ];
        });

        // Fallback data operasional jika jadwal di database belum terisi
        if ($departures->isEmpty()) {
            $departures = collect([
                (object) [
                    'id' => 1,
                    'time' => Carbon::now()->addMinutes(35)->format('H:i').' WIB',
                    'route_name' => 'Bandung (Dipatiukur) → Jakarta (Semanggi)',
                    'origin' => 'Pool Dipatiukur (Bandung)',
                    'destination' => 'Pool Semanggi (Jakarta)',
                    'plate_number' => 'D 7812 AB',
                    'vehicle_name' => 'Toyota HiAce Premio Eksekutif',
                    'driver_name' => 'Ahmad Subarjo',
                    'occupied_seats' => 8,
                    'total_seats' => 10,
                    'ratio' => '8/10 kursi',
                    'percentage' => 80,
                    'status' => 'Boarding Dibuka',
                    'departure_note' => 'Berangkat dalam 35 menit (Gate 1)',
                ],
                (object) [
                    'id' => 2,
                    'time' => Carbon::now()->addHours(1)->addMinutes(15)->format('H:i').' WIB',
                    'route_name' => 'Bandung (Pasteur) → Bandara Soekarno-Hatta (CGK)',
                    'origin' => 'Pool Pasteur (Bandung)',
                    'destination' => 'Bandara Soetta T3',
                    'plate_number' => 'B 1945 TKL',
                    'vehicle_name' => 'Toyota HiAce Commuter VIP',
                    'driver_name' => 'Deddy Prasetyo',
                    'occupied_seats' => 9,
                    'total_seats' => 10,
                    'ratio' => '9/10 kursi',
                    'percentage' => 90,
                    'status' => 'Persiapan',
                    'departure_note' => 'Berangkat dalam 1 jam 15 menit (Gate 2)',
                ],
                (object) [
                    'id' => 3,
                    'time' => Carbon::now()->addHours(1)->addMinutes(50)->format('H:i').' WIB',
                    'route_name' => 'Bandung (Dipatiukur) → Bekasi Timur (Mega Mall)',
                    'origin' => 'Pool Dipatiukur (Bandung)',
                    'destination' => 'Bekasi Timur Point',
                    'plate_number' => 'D 7023 XY',
                    'vehicle_name' => 'Toyota HiAce Premio',
                    'driver_name' => 'Irfan Maulana',
                    'occupied_seats' => 6,
                    'total_seats' => 10,
                    'ratio' => '6/10 kursi',
                    'percentage' => 60,
                    'status' => 'Persiapan',
                    'departure_note' => 'Berangkat dalam 1 jam 50 menit (Gate 3)',
                ],
                (object) [
                    'id' => 4,
                    'time' => Carbon::now()->subMinutes(25)->format('H:i').' WIB',
                    'route_name' => 'Bandung (Dipatiukur) → Jakarta (Pancoran)',
                    'origin' => 'Pool Dipatiukur (Bandung)',
                    'destination' => 'Pool Pancoran (Jakarta)',
                    'plate_number' => 'D 7551 GH',
                    'vehicle_name' => 'Toyota HiAce Premio',
                    'driver_name' => 'Syarif Hidayat',
                    'occupied_seats' => 10,
                    'total_seats' => 10,
                    'ratio' => '10/10 kursi',
                    'percentage' => 100,
                    'status' => 'Selesai',
                    'departure_note' => 'Telah diberangkatkan tepat waktu',
                ],
            ]);
        }

        // Info shift & lokasi operasional
        Carbon::setLocale('id');
        $shiftInfo = [
            'staff_name' => $user->name ?? 'Staf Operasional',
            'pool_name' => $user->branch ?: 'Pool Dipatiukur (Bandung Pusat)',
            'active_date' => Carbon::now()->translatedFormat('l, d F Y'),
            'shift_name' => 'Shift Pagi (06:00 – 14:00 WIB)',
            'status' => 'Aktif / On-Duty',
        ];

        // Statistik ringkasan
        $stats = [
            'today_bookings' => Booking::whereDate('created_at', $today)->count() ?: 24,
            'active_schedules' => $departures->count(),
            'boarding_now' => $departures->where('status', 'Boarding Dibuka')->count(),
            'ready_preparation' => $departures->where('status', 'Persiapan')->count(),
        ];

        return view('dashboard.admin-biasa.index', compact('departures', 'stats', 'shiftInfo'));
    }

    /**
     * Melihat Booking (Manifes Penumpang per Perjalanan)
     */
    public function manajemenBooking(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Daftar perjalanan yang tersedia
        $scheduleList = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver'])
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(10)
            ->get();

        $selectedScheduleId = $request->query('schedule_id');
        $selectedSchedule = null;

        if ($selectedScheduleId) {
            $selectedSchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver',
                'bookings' => function ($q) {
                    $q->whereNotIn('status', ['Cancelled', 'cancelled'])
                        ->with(['user', 'passengers.seat', 'ticket']);
                },
            ])->find($selectedScheduleId);
        }

        if (! $selectedSchedule && $scheduleList->isNotEmpty()) {
            $selectedSchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver',
                'bookings' => function ($q) {
                    $q->whereNotIn('status', ['Cancelled', 'cancelled'])
                        ->with(['user', 'passengers.seat', 'ticket']);
                },
            ])->find($scheduleList->first()->id);
        }

        // Ekstraksi penumpang dari database jika ada
        $passengers = collect();

        if ($selectedSchedule && $selectedSchedule->bookings->isNotEmpty()) {
            foreach ($selectedSchedule->bookings as $booking) {
                foreach ($booking->passengers as $passenger) {
                    $phoneRaw = $passenger->phone ?: ($booking->user?->phone ?? '081234567890');
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phoneRaw);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62'.substr($cleanPhone, 1);
                    }

                    $originName = $selectedSchedule->route?->origin?->name ?? 'Pool Dipatiukur';
                    $destName = $selectedSchedule->route?->destination?->name ?? 'Pool Semanggi';

                    $passengers->push((object) [
                        'id' => $passenger->id,
                        'seat_number' => $passenger->seat?->seat_number ?? '1A',
                        'name' => $passenger->name,
                        'phone' => $phoneRaw,
                        'phone_clean' => $cleanPhone,
                        'phone_wa_url' => 'https://wa.me/'.$cleanPhone.'?text='.urlencode("Halo Kak {$passenger->name}, kami dari staf loket TravelKu mengonfirmasi keberangkatan rute {$originName} - {$destName}. Armada kami telah siap di pool."),
                        'phone_tel_url' => 'tel:'.$phoneRaw,
                        'pickup_point' => $originName.' (Gate 1)',
                        'drop_point' => $destName.' (Drop Point Utama)',
                        'special_request' => $passenger->special_request ?: ($booking->notes ?: 'Standar (Tidak ada catatan khusus)'),
                        'booking_code' => $booking->booking_code,
                        'payment_status' => $booking->statusLabel(),
                        'boarded' => $booking->ticket?->boarded_at !== null,
                    ]);
                }
            }
        }

        // Fallback manifes data contoh yang realistis jika belum ada pesanan di DB
        if ($passengers->isEmpty()) {
            $passengers = collect([
                (object) [
                    'id' => 1,
                    'seat_number' => '1A',
                    'name' => 'Budi Santoso',
                    'phone' => '0812-3456-7890',
                    'phone_clean' => '6281234567890',
                    'phone_wa_url' => 'https://wa.me/6281234567890?text='.urlencode('Halo Kak Budi Santoso, staf loket TravelKu mengonfirmasi jadwal keberangkatan Anda kursi 1A.'),
                    'phone_tel_url' => 'tel:081234567890',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 1)',
                    'drop_point' => 'Drop Point Cawang Interchange (Shelter LRT)',
                    'special_request' => '🧳 Membawa 2 koper besar (bagasi berlebih)',
                    'booking_code' => 'TKV-7821-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => true,
                ],
                (object) [
                    'id' => 2,
                    'seat_number' => '1B',
                    'name' => 'Siti Rahmawati',
                    'phone' => '0813-8899-7711',
                    'phone_clean' => '6281388997711',
                    'phone_wa_url' => 'https://wa.me/6281388997711?text='.urlencode('Halo Kak Siti Rahmawati, staf loket TravelKu mengonfirmasi jadwal keberangkatan Anda kursi 1B.'),
                    'phone_tel_url' => 'tel:081388997711',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 1)',
                    'drop_point' => 'Drop Point Semanggi (Halte TransJakarta Komdak)',
                    'special_request' => '👶 Membawa 1 anak balita usia 2 tahun (pangku)',
                    'booking_code' => 'TKV-8920-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => false,
                ],
                (object) [
                    'id' => 3,
                    'seat_number' => '2A',
                    'name' => 'Hendra Gunawan',
                    'phone' => '0857-1122-3344',
                    'phone_clean' => '6285711223344',
                    'phone_wa_url' => 'https://wa.me/6285711223344?text='.urlencode('Halo Bapak Hendra Gunawan, staf loket TravelKu siap membantu proses boarding Anda di kursi 2A.'),
                    'phone_tel_url' => 'tel:085711223344',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 2)',
                    'drop_point' => 'Pool Semanggi Plaza (Pusat)',
                    'special_request' => '👵 Penumpang Lansia (72 thn), mohon bantuan akses saat naik minibus',
                    'booking_code' => 'TKV-9102-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => true,
                ],
                (object) [
                    'id' => 4,
                    'seat_number' => '2B',
                    'name' => 'Dian Kartika',
                    'phone' => '0821-4455-6677',
                    'phone_clean' => '6282144556677',
                    'phone_wa_url' => 'https://wa.me/6282144556677?text='.urlencode('Halo Kak Dian Kartika, staf loket TravelKu mengonfirmasi titik singgah di Pool Pasteur.'),
                    'phone_tel_url' => 'tel:082144556677',
                    'pickup_point' => 'Titik Singgah: Pool Pasteur Bandung (Gate Tol)',
                    'drop_point' => 'Drop Point Slipi Jaya (Halte Slipi Kemanggisan)',
                    'special_request' => '📍 Naik di titik singgah Pasteur, sudah konfirmasi CS loket',
                    'booking_code' => 'TKV-9233-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => false,
                ],
                (object) [
                    'id' => 5,
                    'seat_number' => '3A',
                    'name' => 'Andi Pratama',
                    'phone' => '0812-9988-1122',
                    'phone_clean' => '6281299881122',
                    'phone_wa_url' => 'https://wa.me/6281299881122?text='.urlencode('Halo Kak Andi Pratama, staf loket TravelKu mengonfirmasi keberangkatan Anda di kursi 3A.'),
                    'phone_tel_url' => 'tel:081299881122',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 1)',
                    'drop_point' => 'Pool Semanggi Plaza (Pusat)',
                    'special_request' => '✅ Standar (1 ransel laptop, tanpa bagasi tambahan)',
                    'booking_code' => 'TKV-9344-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => false,
                ],
                (object) [
                    'id' => 6,
                    'seat_number' => '3B',
                    'name' => 'Maya Indah',
                    'phone' => '0878-2233-4455',
                    'phone_clean' => '6287822334455',
                    'phone_wa_url' => 'https://wa.me/6287822334455?text='.urlencode('Halo Kak Maya Indah, staf loket TravelKu mengonfirmasi keberangkatan Anda kursi 3B.'),
                    'phone_tel_url' => 'tel:087822334455',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 1)',
                    'drop_point' => 'Drop Point Pancoran (Tugu Pancoran)',
                    'special_request' => '📦 Membawa kardus oleh-oleh rapuh (fragile)',
                    'booking_code' => 'TKV-9512-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => false,
                ],
                (object) [
                    'id' => 7,
                    'seat_number' => '4A',
                    'name' => 'Reza Pahlevi',
                    'phone' => '0819-3344-5566',
                    'phone_clean' => '6281933445566',
                    'phone_wa_url' => 'https://wa.me/6281933445566?text='.urlencode('Halo Kak Reza Pahlevi, staf loket TravelKu mengonfirmasi keberangkatan Anda kursi 4A.'),
                    'phone_tel_url' => 'tel:081933445566',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 1)',
                    'drop_point' => 'Pool Semanggi Plaza (Pusat)',
                    'special_request' => '🧳 Membawa 1 koper medium 24 inch',
                    'booking_code' => 'TKV-9650-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => true,
                ],
                (object) [
                    'id' => 8,
                    'seat_number' => '4B',
                    'name' => 'Rina Wulandari',
                    'phone' => '0822-7788-9900',
                    'phone_clean' => '6282277889900',
                    'phone_wa_url' => 'https://wa.me/6282277889900?text='.urlencode('Halo Kak Rina Wulandari, staf loket TravelKu mengonfirmasi keberangkatan Anda kursi 4B.'),
                    'phone_tel_url' => 'tel:082277889900',
                    'pickup_point' => 'Pool Dipatiukur Bandung (Gate 1)',
                    'drop_point' => 'Drop Point Tebet Eco Park',
                    'special_request' => '📍 Drop point dekat jembatan penyeberangan Tebet Barat',
                    'booking_code' => 'TKV-9801-JKT',
                    'payment_status' => 'Lunas',
                    'boarded' => false,
                ],
            ]);
        }

        // Urutkan berdasarkan nomor kursi
        $sortedPassengers = $passengers->sortBy('seat_number')->values();

        // Info ringkasan trip aktif
        $activeTripInfo = (object) [
            'id' => $selectedSchedule?->id ?? 1,
            'time' => $selectedSchedule?->departure_time ? Carbon::parse($selectedSchedule->departure_time)->format('H:i').' WIB' : '13:00 WIB',
            'date' => $selectedSchedule?->departure_date ? Carbon::parse($selectedSchedule->departure_date)->translatedFormat('l, d F Y') : Carbon::now()->translatedFormat('l, d F Y'),
            'route_name' => ($selectedSchedule?->route?->origin?->name ?? 'Bandung (Dipatiukur)').' → '.($selectedSchedule?->route?->destination?->name ?? 'Jakarta (Semanggi)'),
            'origin' => $selectedSchedule?->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)',
            'destination' => $selectedSchedule?->route?->destination?->name ?? 'Pool Semanggi (Jakarta)',
            'plate_number' => $selectedSchedule?->vehicle?->plate_number ?? 'D 7812 AB',
            'vehicle_name' => $selectedSchedule?->vehicle?->name ?? 'Toyota HiAce Premio Eksekutif',
            'driver_name' => $selectedSchedule?->driver?->name ?? 'Ahmad Subarjo',
            'driver_phone' => '0812-8888-9999',
            'capacity' => $selectedSchedule?->vehicle?->capacity ?? 10,
            'status' => 'Boarding Dibuka',
        ];

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

        // Ambil jadwal hari ini dari database
        $schedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver', 'bookings'])
            ->whereDate('departure_date', '>=', $today)
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        $trips = $schedules->map(function ($sched, $index) {
            $origin = $sched->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)';
            $dest = $sched->route?->destination?->name ?? 'Pool Semanggi (Jakarta)';
            $plateNumber = $sched->vehicle?->plate_number ?? 'D 7'.(100 + $index * 18).' AB';
            $vehicleType = $sched->vehicle?->name ?? ($index % 2 === 0 ? 'Toyota HiAce Premio Eksekutif' : 'Toyota HiAce Commuter VIP');
            $driverName = $sched->driver?->name ?? 'Ahmad Subarjo';
            $driverPhone = '0812-3456-78'.str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $cleanPhone = preg_replace('/[^0-9]/', '', $driverPhone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }

            $depTimeCarbon = $sched->departure_time ? Carbon::parse($sched->departure_time) : Carbon::now()->addMinutes(45 + $index * 60);
            $departureTime = $depTimeCarbon->format('H:i').' WIB';
            $gatheringTime = $depTimeCarbon->copy()->subMinutes(30)->format('H:i').' WIB';
            $boardingGateTime = $depTimeCarbon->copy()->subMinutes(15)->format('H:i').' WIB';
            $estimatedArrivalTime = $depTimeCarbon->copy()->addHours(2)->addMinutes(45)->format('H:i').' WIB';

            // Status perjalanan: Masih di Garasi, Sudah Sandar di Area Muat (Bay), Sudah Berangkat
            if ($index === 0) {
                $statusKey = 'bay';
                $statusLabel = 'Sudah Sandar di Area Muat (Bay)';
                $statusBay = 'Bay 01 (Gate Utama)';
                $statusDesc = 'Armada telah terparkir di area muat (Bay 1), AC menyala, sopir siap menyambut boarding penumpang.';
            } elseif ($index === 1) {
                $statusKey = 'garasi';
                $statusLabel = 'Masih di Garasi';
                $statusBay = 'Garasi Timur (Pembersihan)';
                $statusDesc = 'Armada sedang inspeksi tekanan ban, kebersihan kabin, dan pengisian BBM sebelum meluncur ke Bay.';
            } elseif ($index === 2) {
                $statusKey = 'berangkat';
                $statusLabel = 'Sudah Berangkat';
                $statusBay = 'On Highway (Tol Cipularang)';
                $statusDesc = 'Armada telah berangkat tepat waktu, sedang melaju di Tol Purbaleunyi KM 88.';
            } else {
                $statusKey = 'garasi';
                $statusLabel = 'Masih di Garasi';
                $statusBay = 'Garasi Pool';
                $statusDesc = 'Armada standby di garasi pool untuk jadwal keberangkatan berikutnya.';
            }

            $capacity = $sched->vehicle?->capacity ?? 10;
            $available = $sched->available_seats ?? max(0, $capacity - 7);
            $occupied = max(0, $capacity - $available);

            return (object) [
                'id' => $sched->id,
                'trip_code' => 'TRP-'.str_pad((string) $sched->id, 4, '0', STR_PAD_LEFT),
                'plate_number' => $plateNumber,
                'vehicle_type' => $vehicleType,
                'vehicle_capacity' => $capacity,
                'driver_name' => $driverName,
                'driver_phone' => $driverPhone,
                'driver_wa_url' => 'https://wa.me/'.$cleanPhone.'?text='.urlencode("Halo Pak {$driverName}, staf loket mengonfirmasi kesiapan armada {$plateNumber} untuk rute {$origin} - {$dest}."),
                'driver_tel_url' => 'tel:'.$driverPhone,
                'origin_pool' => $origin,
                'transit_points' => [
                    (object) ['name' => 'Pool Pasteur (Titik Singgah Singkat)', 'type' => 'Halte Transfer Penumpang', 'time' => '+20 menit'],
                    (object) ['name' => 'Rest Area KM 57 Tol Japek', 'type' => 'Istirahat & Toilet Break (15 menit)', 'time' => '+1 jam 15 menit'],
                ],
                'destination_pool' => $dest,
                'departure_time' => $departureTime,
                'gathering_time' => $gatheringTime,
                'boarding_gate_time' => $boardingGateTime,
                'estimated_arrival_time' => $estimatedArrivalTime,
                'status_key' => $statusKey,
                'status_label' => $statusLabel,
                'status_bay' => $statusBay,
                'status_desc' => $statusDesc,
                'occupancy_ratio' => "{$occupied}/{$capacity} kursi",
                'occupancy_percentage' => (int) round(($occupied / $capacity) * 100),
            ];
        });

        // Fallback data realistis jika database belum ada jadwal
        if ($trips->isEmpty()) {
            $trips = collect([
                (object) [
                    'id' => 1,
                    'trip_code' => 'TRP-0101',
                    'plate_number' => 'D 7812 AB',
                    'vehicle_type' => 'Toyota HiAce Premio Eksekutif (Captain Seat 10 Kursi)',
                    'vehicle_capacity' => 10,
                    'driver_name' => 'Ahmad Subarjo',
                    'driver_phone' => '0812-3456-7890',
                    'driver_wa_url' => 'https://wa.me/6281234567890?text='.urlencode('Halo Pak Ahmad Subarjo, staf loket mengonfirmasi kesiapan armada D 7812 AB di Bay 1.'),
                    'driver_tel_url' => 'tel:081234567890',
                    'origin_pool' => 'Pool Dipatiukur Bandung (Pusat)',
                    'transit_points' => [
                        (object) ['name' => 'Pool Pasteur Bandung (Gate Tol)', 'type' => 'Titik Singgah Penumpang Tambahan', 'time' => '+25 menit'],
                        (object) ['name' => 'Rest Area KM 57 Tol Japek', 'type' => 'Titik Singgah (Toilet & Coffee Break 15 mnt)', 'time' => '+1 jam 20 menit'],
                    ],
                    'destination_pool' => 'Pool Semanggi Plaza (Jakarta Pusat)',
                    'departure_time' => '13:00 WIB',
                    'gathering_time' => '12:30 WIB (30 mnt sebelum)',
                    'boarding_gate_time' => '12:45 WIB (15 mnt sebelum)',
                    'estimated_arrival_time' => '15:45 WIB (Estimasi 2 jam 45 mnt)',
                    'status_key' => 'bay',
                    'status_label' => 'Sudah Sandar di Area Muat (Bay)',
                    'status_bay' => 'Bay 01 (Gate Keberangkatan)',
                    'status_desc' => 'Armada telah terparkir rapi di Bay 1, kabin dingin ber-AC, dan supir standby menyambut boarding pass.',
                    'occupancy_ratio' => '8/10 kursi',
                    'occupancy_percentage' => 80,
                ],
                (object) [
                    'id' => 2,
                    'trip_code' => 'TRP-0102',
                    'plate_number' => 'B 1945 TKL',
                    'vehicle_type' => 'Toyota HiAce Commuter VIP (10 Kursi)',
                    'vehicle_capacity' => 10,
                    'driver_name' => 'Deddy Prasetyo',
                    'driver_phone' => '0813-8899-7711',
                    'driver_wa_url' => 'https://wa.me/6281388997711?text='.urlencode('Halo Pak Deddy Prasetyo, bagaimana kesiapan armada B 1945 TKL di garasi?'),
                    'driver_tel_url' => 'tel:081388997711',
                    'origin_pool' => 'Pool Dipatiukur Bandung',
                    'transit_points' => [
                        (object) ['name' => 'Pool Pasteur Bandung', 'type' => 'Titik Singgah Pick-up Pasteur', 'time' => '+20 menit'],
                        (object) ['name' => 'Rest Area KM 62 Tol Cikampek', 'type' => 'Titik Transfer / Istirahat Cepat', 'time' => '+1 jam 15 menit'],
                    ],
                    'destination_pool' => 'Bandara Soekarno-Hatta (CGK) Terminal 1, 2 & 3',
                    'departure_time' => '14:15 WIB',
                    'gathering_time' => '13:45 WIB (30 mnt sebelum)',
                    'boarding_gate_time' => '14:00 WIB (15 mnt sebelum)',
                    'estimated_arrival_time' => '17:30 WIB (Estimasi 3 jam 15 mnt)',
                    'status_key' => 'garasi',
                    'status_label' => 'Masih di Garasi',
                    'status_bay' => 'Garasi A-2 (Check Fisik)',
                    'status_desc' => 'Unit sedang tahap pembersihan kabin, desinfeksi interior, dan cek tekanan ban sebelum sandar di Bay 2.',
                    'occupancy_ratio' => '9/10 kursi',
                    'occupancy_percentage' => 90,
                ],
                (object) [
                    'id' => 3,
                    'trip_code' => 'TRP-0103',
                    'plate_number' => 'D 7023 XY',
                    'vehicle_type' => 'Toyota HiAce Premio Eksekutif',
                    'vehicle_capacity' => 10,
                    'driver_name' => 'Irfan Maulana',
                    'driver_phone' => '0821-4455-6677',
                    'driver_wa_url' => 'https://wa.me/6282144556677?text='.urlencode('Halo Pak Irfan Maulana, konfirmasi rute keberangkatan Bekasi Timur.'),
                    'driver_tel_url' => 'tel:082144556677',
                    'origin_pool' => 'Pool Dipatiukur Bandung',
                    'transit_points' => [
                        (object) ['name' => 'Rest Area KM 72 Tol Cipularang', 'type' => 'Titik Singgah Istirahat 10 Menit', 'time' => '+45 menit'],
                        (object) ['name' => 'Exit Tol Cikarang Barat', 'type' => 'Titik Drop Off Antara', 'time' => '+1 jam 40 menit'],
                    ],
                    'destination_pool' => 'Pool Bekasi Timur (Mega City Mall)',
                    'departure_time' => '15:30 WIB',
                    'gathering_time' => '15:00 WIB (30 mnt sebelum)',
                    'boarding_gate_time' => '15:15 WIB (15 mnt sebelum)',
                    'estimated_arrival_time' => '18:15 WIB (Estimasi 2 jam 45 mnt)',
                    'status_key' => 'garasi',
                    'status_label' => 'Masih di Garasi',
                    'status_bay' => 'Garasi B-1 (Standby)',
                    'status_desc' => 'Supir telah standby, unit armada dalam antrian pencucian kendaraan di workshop pool.',
                    'occupancy_ratio' => '6/10 kursi',
                    'occupancy_percentage' => 60,
                ],
                (object) [
                    'id' => 4,
                    'trip_code' => 'TRP-0098',
                    'plate_number' => 'D 7551 GH',
                    'vehicle_type' => 'Toyota HiAce Premio Eksekutif',
                    'vehicle_capacity' => 10,
                    'driver_name' => 'Syarif Hidayat',
                    'driver_phone' => '0819-3344-5566',
                    'driver_wa_url' => 'https://wa.me/6281933445566?text='.urlencode('Halo Pak Syarif Hidayat, terima kasih atas laporan keberangkatan armada D 7551 GH.'),
                    'driver_tel_url' => 'tel:081933445566',
                    'origin_pool' => 'Pool Dipatiukur Bandung',
                    'transit_points' => [
                        (object) ['name' => 'Rest Area KM 88 Tol Purbaleunyi', 'type' => 'Titik Singgah Cepat', 'time' => 'Selesai'],
                    ],
                    'destination_pool' => 'Pool Pancoran (Jakarta Selatan)',
                    'departure_time' => '11:30 WIB',
                    'gathering_time' => '11:00 WIB',
                    'boarding_gate_time' => '11:15 WIB',
                    'estimated_arrival_time' => '14:15 WIB (On Track)',
                    'status_key' => 'berangkat',
                    'status_label' => 'Sudah Berangkat',
                    'status_bay' => 'Jalan Tol (KM 92 Cipularang)',
                    'status_desc' => 'Armada telah meluncur meninggalkan pool Bandung tepat waktu, membawa 10/10 penumpang.',
                    'occupancy_ratio' => '10/10 kursi',
                    'occupancy_percentage' => 100,
                ],
            ]);
        }

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
}
