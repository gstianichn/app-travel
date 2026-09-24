<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\TripIncident;
use App\Models\Vehicle;
use App\Notifications\OperationalInfoNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DriverDashboardController extends Controller
{
    /**
     * Mengambil dan menyiapkan seluruh data terpusat operasional pengemudi.
     */
    public function getDriverDashboardData(Request $request): array
    {
        $user = auth()->user();

        // 1. Dapatkan atau inisialisasi profil driver yang terhubung dengan akun login
        $driver = $user->driver;
        if (! $driver) {
            $driver = Driver::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $user->phone ?? '0812-8899-7711',
                    'status' => 'standby',
                    'license_number' => 'SIM-B1-'.rand(100000, 999999),
                    'license_type' => 'B1 Umum',
                    'license_expired_at' => Carbon::now()->addMonths(10),
                    'emergency_contact_name' => 'Koordinator Operasional Pool',
                    'emergency_contact_phone' => '0811-2233-4455',
                    'emergency_contact_relation' => 'Pusat Kendali',
                ]
            );
        }

        // 2. Dapatkan jadwal yang sedang ditugaskan (jadwal aktif hari ini)
        $scheduleId = $request->query('schedule_id');
        $activeSchedule = null;

        if ($scheduleId) {
            $activeSchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver.user',
                'bookings.passengers.seat',
                'bookings.user',
                'bookings.payment',
                'incidents',
            ])->find($scheduleId);
        }

        if (! $activeSchedule) {
            $activeSchedule = $driver->activeSchedule();
        }

        // Jika driver belum memiliki jadwal penugasan, carikan jadwal hari ini yang tersedia di DB
        if (! $activeSchedule) {
            $todaySchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver.user',
                'bookings.passengers.seat',
                'bookings.user',
                'bookings.payment',
                'incidents',
            ])
                ->whereDate('departure_date', '>=', Carbon::today()->subDays(1))
                ->whereNotIn('trip_status', ['completed', 'cancelled'])
                ->whereNotIn('status', ['completed', 'Cancelled', 'cancelled'])
                ->orderBy('departure_date')
                ->orderBy('departure_time')
                ->first();

            if ($todaySchedule) {
                // Assign driver ke jadwal ini secara otomatis jika belum ada driver
                if (! $todaySchedule->driver_id) {
                    $todaySchedule->update(['driver_id' => $driver->id]);
                }
                $activeSchedule = $todaySchedule;
            }
        }

        // 3. Fallback dummy schedule jika database benar-benar kosong
        if (! $activeSchedule) {
            $vehicle = Vehicle::first() ?? new Vehicle([
                'plate_number' => 'D 7812 AB',
                'name' => 'Toyota HiAce Premio Eksekutif',
                'type' => 'Toyota HiAce Premio',
                'capacity' => 10,
                'status' => 'siap_jalan',
            ]);

            $activeSchedule = new Schedule([
                'id' => 1,
                'departure_date' => Carbon::today(),
                'departure_time' => '13:00:00',
                'status' => 'Scheduled',
                'trip_status' => 'scheduled',
                'available_seats' => 8,
                'price' => 125000,
            ]);
            $activeSchedule->vehicle = $vehicle;
        }

        // Pastikan armada memiliki relasi kursi dan kelengkapan spesifikasi
        if ($activeSchedule->vehicle) {
            $activeSchedule->vehicle->ensureSeatsExist();
            if (! $activeSchedule->vehicle->stnk_tax_due_date) {
                $activeSchedule->vehicle->stnk_tax_due_date = Carbon::now()->addMonths(8);
            }
            if (! $activeSchedule->vehicle->kir_expiry_date) {
                $activeSchedule->vehicle->kir_expiry_date = Carbon::now()->addMonths(5);
            }
            if (! $activeSchedule->vehicle->manufacture_year) {
                $activeSchedule->vehicle->manufacture_year = 2024;
            }
            if (! $activeSchedule->vehicle->chassis_number) {
                $activeSchedule->vehicle->chassis_number = 'MHF21TRP'.str_pad((string) ($activeSchedule->vehicle->id ?: 1), 6, '0', STR_PAD_LEFT);
            }
            if (! $activeSchedule->vehicle->engine_number) {
                $activeSchedule->vehicle->engine_number = '1GD-FTV-'.(882100 + ($activeSchedule->vehicle->id ?: 1));
            }
        }

        // 4. Daftar jadwal penugasan milik driver (Hari Ini, Besok, dan Jadwal Mendatang)
        $todaySchedules = $driver->schedules()
            ->with(['route.origin', 'route.destination', 'vehicle'])
            ->whereDate('departure_date', Carbon::today())
            ->orderBy('departure_time')
            ->get();

        if ($todaySchedules->isEmpty() && $activeSchedule && $activeSchedule->exists) {
            $todaySchedules = collect([$activeSchedule]);
        }

        $tomorrowSchedules = $driver->schedules()
            ->with(['route.origin', 'route.destination', 'vehicle'])
            ->whereDate('departure_date', Carbon::tomorrow())
            ->orderBy('departure_time')
            ->get();

        if ($tomorrowSchedules->isEmpty()) {
            $tomorrowRoute = $activeSchedule->route ?? ($activeSchedule->route_id ? Route::with(['origin', 'destination'])->find($activeSchedule->route_id) : null);
            $tomorrowVehicle = $activeSchedule->vehicle ?? Vehicle::first();

            $simTomorrow = new Schedule([
                'id' => 902,
                'departure_date' => Carbon::tomorrow(),
                'departure_time' => '08:30:00',
                'status' => 'Scheduled',
                'trip_status' => 'scheduled',
                'available_seats' => 9,
                'price' => 125000,
            ]);
            $simTomorrow->route = $tomorrowRoute;
            $simTomorrow->vehicle = $tomorrowVehicle;
            $simTomorrow->driver_id = $driver->id;
            $tomorrowSchedules = collect([$simTomorrow]);
        }

        $upcomingSchedules = $driver->schedules()
            ->with(['route.origin', 'route.destination', 'vehicle'])
            ->whereDate('departure_date', '>', Carbon::tomorrow())
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->take(10)
            ->get();

        if ($upcomingSchedules->isEmpty()) {
            $upcomingRoute = $activeSchedule->route ?? ($activeSchedule->route_id ? Route::with(['origin', 'destination'])->find($activeSchedule->route_id) : null);
            $upcomingVehicle = $activeSchedule->vehicle ?? Vehicle::first();

            $simUpcoming1 = new Schedule([
                'id' => 903,
                'departure_date' => Carbon::today()->addDays(2),
                'departure_time' => '13:00:00',
                'status' => 'Scheduled',
                'trip_status' => 'scheduled',
                'available_seats' => 8,
                'price' => 125000,
            ]);
            $simUpcoming1->route = $upcomingRoute;
            $simUpcoming1->vehicle = $upcomingVehicle;
            $simUpcoming1->driver_id = $driver->id;

            $simUpcoming2 = new Schedule([
                'id' => 904,
                'departure_date' => Carbon::today()->addDays(3),
                'departure_time' => '16:00:00',
                'status' => 'Scheduled',
                'trip_status' => 'scheduled',
                'available_seats' => 10,
                'price' => 125000,
            ]);
            $simUpcoming2->route = $upcomingRoute;
            $simUpcoming2->vehicle = $upcomingVehicle;
            $simUpcoming2->driver_id = $driver->id;

            $upcomingSchedules = collect([$simUpcoming1, $simUpcoming2]);
        }

        // 5. Waypoints navigasi terurut (Pool Asal -> Titik Jemput Penumpang -> Rest Area -> Pool Tujuan)
        $waypoints = $activeSchedule->navigationWaypoints();

        // 6. Manifest Penumpang Lengkap
        $manifestPassengers = collect();
        if ($activeSchedule->exists && $activeSchedule->id) {
            $bookings = Booking::where('schedule_id', $activeSchedule->id)
                ->whereNotIn('status', ['Cancelled', 'cancelled'])
                ->with(['passengers.seat', 'user', 'payment', 'ticket'])
                ->get();

            foreach ($bookings as $b) {
                $isPaid = in_array(strtolower($b->status), ['paid', 'confirmed', 'success', 'lunas', 'completed']);
                $paymentMethod = $b->payment?->payment_method ?? ($b->source === 'walk_in' ? 'cash' : 'online');
                $isCod = in_array(strtolower($paymentMethod), ['cash', 'cod', 'tunai', 'bayar_ditempat']);

                foreach ($b->passengers as $p) {
                    $manifestPassengers->push((object) [
                        'id' => $p->id,
                        'passenger_model' => $p,
                        'booking_id' => $b->id,
                        'booking_code' => $b->booking_code,
                        'name' => $p->name,
                        'phone' => $p->phone ?: ($b->user?->phone ?? '-'),
                        'wa_phone' => $p->waPhone(),
                        'seat_number' => $p->seat?->seat_number ?? '1A',
                        'seat_id' => $p->seat_id,
                        'pickup_point' => $p->pickupLabel(),
                        'dropoff_point' => $p->dropoffLabel(),
                        'baggage_notes' => $p->baggageLabel(),
                        'special_request' => $p->special_request,
                        'boarding_status' => $p->boarding_status ?: 'belum_hadir',
                        'is_boarded' => $p->isBoarded(),
                        'boarded_at' => $p->boarded_at ? $p->boarded_at->format('H:i').' WIB' : null,
                        'is_paid' => $isPaid,
                        'payment_label' => $isPaid ? 'Lunas Online' : 'Tagih Tunai / COD: Rp '.number_format(! $isPaid ? ($b->total_price / max(1, $b->total_passenger)) : 0, 0, ',', '.'),
                        'payment_badge_class' => $isPaid ? 'badge-paid' : 'badge-cod',
                        'amount_due' => ! $isPaid ? ($b->total_price / max(1, $b->total_passenger)) : 0,
                    ]);
                }
            }
        }

        // Fallback contoh manifest realistis jika belum ada booking di database
        if ($manifestPassengers->isEmpty()) {
            $manifestPassengers = collect([
                (object) [
                    'id' => 101,
                    'passenger_model' => null,
                    'booking_id' => 1,
                    'booking_code' => 'TKV-7821-JKT',
                    'name' => 'Budi Santoso',
                    'phone' => '0812-3456-7890',
                    'wa_phone' => '6281234567890',
                    'seat_number' => '1A',
                    'seat_id' => 1,
                    'pickup_point' => 'Pool Dipatiukur (Gate 1)',
                    'dropoff_point' => 'Pool Semanggi Plaza',
                    'baggage_notes' => '1 Koper 24 inch + 1 Ransel',
                    'special_request' => 'Kursi dekat jendela depan',
                    'boarding_status' => 'belum_hadir',
                    'is_boarded' => false,
                    'boarded_at' => null,
                    'is_paid' => true,
                    'payment_label' => 'Lunas Online',
                    'payment_badge_class' => 'badge-paid',
                    'amount_due' => 0,
                ],
                (object) [
                    'id' => 102,
                    'passenger_model' => null,
                    'booking_id' => 2,
                    'booking_code' => 'TKV-8920-JKT',
                    'name' => 'Siti Rahmawati',
                    'phone' => '0813-8899-7711',
                    'wa_phone' => '6281388997711',
                    'seat_number' => '2A',
                    'seat_id' => 2,
                    'pickup_point' => 'Pool Pasteur (Gate Tol)',
                    'dropoff_point' => 'Halte Pancoran Barat',
                    'baggage_notes' => '2 Tas Jinjing Sedang',
                    'special_request' => 'Perjalanan membawa anak',
                    'boarding_status' => 'belum_hadir',
                    'is_boarded' => false,
                    'boarded_at' => null,
                    'is_paid' => true,
                    'payment_label' => 'Lunas Online',
                    'payment_badge_class' => 'badge-paid',
                    'amount_due' => 0,
                ],
                (object) [
                    'id' => 103,
                    'passenger_model' => null,
                    'booking_id' => 3,
                    'booking_code' => 'TKV-9102-JKT',
                    'name' => 'Hendra Gunawan',
                    'phone' => '0857-1122-3344',
                    'wa_phone' => '6285711223344',
                    'seat_number' => '2B',
                    'seat_id' => 3,
                    'pickup_point' => 'Pool Dipatiukur Bandung',
                    'dropoff_point' => 'Pool Semanggi (Jakarta Pusat)',
                    'baggage_notes' => '1 Kardus Oleh-oleh Bandung',
                    'special_request' => '-',
                    'boarding_status' => 'belum_hadir',
                    'is_boarded' => false,
                    'boarded_at' => null,
                    'is_paid' => false,
                    'payment_label' => 'Tagih Tunai / COD: Rp 125.000',
                    'payment_badge_class' => 'badge-cod',
                    'amount_due' => 125000,
                ],
                (object) [
                    'id' => 104,
                    'passenger_model' => null,
                    'booking_id' => 4,
                    'booking_code' => 'TKV-9344-JKT',
                    'name' => 'Dewi Anggraini',
                    'phone' => '0878-3344-5566',
                    'wa_phone' => '6287833445566',
                    'seat_number' => '3A',
                    'seat_id' => 4,
                    'pickup_point' => 'Pool Dipatiukur (Gate 2)',
                    'dropoff_point' => 'Stasiun Gambir',
                    'baggage_notes' => '1 Ransel Laptop Kabin',
                    'special_request' => 'Dinginkan AC',
                    'boarding_status' => 'belum_hadir',
                    'is_boarded' => false,
                    'boarded_at' => null,
                    'is_paid' => true,
                    'payment_label' => 'Lunas Online',
                    'payment_badge_class' => 'badge-paid',
                    'amount_due' => 0,
                ],
            ]);
        }

        // Urutkan manifest berdasarkan nomor kursi
        $manifestPassengers = $manifestPassengers->sortBy('seat_number')->values();

        // 7. Denah Kursi (Seat Map)
        $seatOccupancyRows = $activeSchedule->exists ? $activeSchedule->getSeatOccupancyMap() : [];

        // 8. Riwayat Ritase yang telah selesai
        $completedTrips = $driver->completedRitaseList(10);

        // 9. Insiden Aktif pada Jadwal ini
        $activeIncidents = $activeSchedule->exists && $activeSchedule->id
            ? $activeSchedule->incidents()->latest()->get()
            : collect();

        // 10. Ringkasan Statistik Performa
        $totalBookedPassengers = $manifestPassengers->count();
        $boardedCount = $manifestPassengers->where('is_boarded', true)->count();
        $pendingBoardingCount = $totalBookedPassengers - $boardedCount;
        $totalCompletedTrips = $driver->completedTripsCount();
        $driverRating = 4.9;

        // Status action berikutnya
        $nextAction = $activeSchedule->nextStatusAction();

        // Target penjemputan / aksi berikutnya
        $unboardedPassenger = $manifestPassengers->firstWhere('is_boarded', false);
        $nextPassenger = $unboardedPassenger ?? $manifestPassengers->first();

        if ($unboardedPassenger) {
            $nextTargetTitle = $unboardedPassenger->name;
            $nextTargetType = 'Jemput Penumpang Berikutnya';
            $nextTargetAddress = $unboardedPassenger->pickup_point;
            $nextTargetNotes = ! empty($unboardedPassenger->special_request) && $unboardedPassenger->special_request !== '-'
                ? $unboardedPassenger->special_request
                : $unboardedPassenger->baggage_notes;
            $nextSeatNumber = 'Seat '.$unboardedPassenger->seat_number;
        } else {
            $nextTargetTitle = $activeSchedule->route?->destination?->name ?? 'Pool Kedatangan';
            $nextTargetType = 'Menuju Titik Turun Akhir';
            $nextTargetAddress = $activeSchedule->route?->destination?->address ?? ($activeSchedule->route?->destination?->name ?? 'Pool Semanggi Plaza');
            $nextTargetNotes = 'Semua penumpang telah naik kabin. Meluncur aman menuju destinasi akhir.';
            $nextSeatNumber = 'Semua Naik';
        }

        $targetQuery = urlencode($nextTargetAddress);
        $nextGmapsUrl = "https://www.google.com/maps/search/?api=1&query={$targetQuery}";
        $nextWazeUrl = "https://waze.com/ul?q={$targetQuery}&navigate=yes";

        $seatCapacity = $activeSchedule->vehicle?->capacity ?? 10;
        $occupancyPct = round(($totalBookedPassengers / max(1, $seatCapacity)) * 100);
        $emptySeats = max(0, $seatCapacity - $totalBookedPassengers);

        return compact(
            'user',
            'driver',
            'activeSchedule',
            'todaySchedules',
            'tomorrowSchedules',
            'upcomingSchedules',
            'waypoints',
            'manifestPassengers',
            'seatOccupancyRows',
            'completedTrips',
            'activeIncidents',
            'totalBookedPassengers',
            'boardedCount',
            'pendingBoardingCount',
            'totalCompletedTrips',
            'driverRating',
            'nextAction',
            'unboardedPassenger',
            'nextPassenger',
            'nextTargetTitle',
            'nextTargetType',
            'nextTargetAddress',
            'nextTargetNotes',
            'nextSeatNumber',
            'targetQuery',
            'nextGmapsUrl',
            'nextWazeUrl',
            'seatCapacity',
            'occupancyPct',
            'emptySeats'
        );
    }

    /**
     * 1. Dashboard — Ringkasan operasional harian yang sedang berjalan (kartu kendali utama)
     */
    public function index(Request $request)
    {
        return view('dashboard.driver.index', $this->getDriverDashboardData($request));
    }

    /**
     * 2. Melihat Jadwal Perjalanan
     */
    public function jadwal(Request $request)
    {
        return view('dashboard.driver.jadwal', $this->getDriverDashboardData($request));
    }

    /**
     * 3. Melihat Kendaraan yang Ditugaskan
     */
    public function kendaraan(Request $request)
    {
        return view('dashboard.driver.kendaraan', $this->getDriverDashboardData($request));
    }

    /**
     * 4. Melihat Daftar Penumpang (Manifes Absensi)
     */
    public function daftarPenumpang(Request $request)
    {
        return view('dashboard.driver.daftar-penumpang', $this->getDriverDashboardData($request));
    }

    /**
     * 5. Melihat Seat Map
     */
    public function seatMap(Request $request)
    {
        return view('dashboard.driver.seat-map', $this->getDriverDashboardData($request));
    }

    /**
     * 6. Melihat Rute Perjalanan
     */
    public function rute(Request $request)
    {
        return view('dashboard.driver.rute', $this->getDriverDashboardData($request));
    }

    /**
     * 7. Melihat Status Perjalanan
     */
    public function statusPerjalanan(Request $request)
    {
        return view('dashboard.driver.status-perjalanan', $this->getDriverDashboardData($request));
    }

    /**
     * 8. Mengubah Status Perjalanan
     */
    public function ubahStatusPerjalanan(Request $request)
    {
        return view('dashboard.driver.ubah-status', $this->getDriverDashboardData($request));
    }

    /**
     * 9. Melihat Informasi Penumpang
     */
    public function infoPenumpang(Request $request)
    {
        return view('dashboard.driver.info-penumpang', $this->getDriverDashboardData($request));
    }

    /**
     * 10. Melaporkan Masalah Perjalanan
     */
    public function laporMasalah(Request $request)
    {
        return view('dashboard.driver.lapor-masalah', $this->getDriverDashboardData($request));
    }

    /**
     * 11. Emergency Center
     */
    public function emergencyCenter(Request $request)
    {
        return view('dashboard.driver.emergency', $this->getDriverDashboardData($request));
    }

    /**
     * 12. GPS Tracking Saat Perjalanan Aktif
     */
    public function gpsTracking(Request $request)
    {
        return view('dashboard.driver.gps-tracking', $this->getDriverDashboardData($request));
    }

    /**
     * 13. Melihat Performa
     */
    public function performa(Request $request)
    {
        return view('dashboard.driver.performa', $this->getDriverDashboardData($request));
    }

    /**
     * 14. Melihat Reward
     */
    public function reward(Request $request)
    {
        return view('dashboard.driver.reward', $this->getDriverDashboardData($request));
    }

    /**
     * 15. Melihat Riwayat Perjalanan
     */
    public function riwayat(Request $request)
    {
        return view('dashboard.driver.riwayat', $this->getDriverDashboardData($request));
    }

    /**
     * Memperbarui status perjalanan secara bertahap
     * (Mulai Jemput -> Berangkat -> Tiba di Tujuan -> Selesaikan Ritase)
     */
    public function updateTripStatus(Request $request, Schedule $schedule)
    {
        $request->validate([
            'status' => 'required|string|in:pickup,departed,arrived,completed',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $newStatus = $request->input('status');
        $now = Carbon::now();
        $driver = auth()->user()->driver;

        $updateData = ['trip_status' => $newStatus];

        if ($request->filled('latitude')) {
            $updateData['current_latitude'] = $request->input('latitude');
        }
        if ($request->filled('longitude')) {
            $updateData['current_longitude'] = $request->input('longitude');
        }
        if ($request->filled('latitude') || $request->filled('longitude')) {
            $updateData['last_gps_at'] = $now;
        }

        if ($newStatus === 'pickup') {
            $updateData['pickup_started_at'] = $now;
            $updateData['status'] = 'Persiapan';
            if ($driver) {
                $driver->update(['status' => 'on_duty']);
            }

            // Kirim notifikasi operasional ke penumpang
            $this->notifyPassengers($schedule, 'pickup_location');
            $message = 'Status perjalanan diperbarui: Mulai Penjemputan Penumpang. Penumpang telah diberi tahu.';
        } elseif ($newStatus === 'departed') {
            $updateData['departed_at'] = $now;
            $updateData['status'] = 'Dalam Perjalanan';
            if ($driver) {
                $driver->update(['status' => 'on_duty']);
            }
            if ($schedule->vehicle) {
                $schedule->vehicle->update(['status' => 'beroperasi']);
            }

            // Kirim notifikasi keberangkatan
            $this->notifyPassengers($schedule, 'vehicle_assigned');
            $message = 'Status perjalanan diperbarui: Armada telah berangkat meluncur ke tujuan.';
        } elseif ($newStatus === 'arrived') {
            $updateData['arrived_at'] = $now;
            $updateData['status'] = 'Tiba di Tujuan';
            $message = 'Status perjalanan diperbarui: Armada telah tiba di pool kedatangan.';
        } elseif ($newStatus === 'completed') {
            $updateData['completed_at'] = $now;
            $updateData['status'] = 'Completed';
            if ($driver) {
                $driver->update(['status' => 'standby']);
            }
            if ($schedule->vehicle) {
                $schedule->vehicle->update(['status' => 'siap_jalan']);
            }

            // Tandai semua tiket booking yang aktif menjadi selesai
            Booking::where('schedule_id', $schedule->id)
                ->whereNotIn('status', ['Cancelled', 'cancelled'])
                ->update(['status' => 'completed']);

            $message = 'Selamat! Perjalanan ritase telah berhasil diselesaikan dan dicatat ke riwayat operasional.';
        } else {
            $message = 'Status berhasil diperbarui.';
        }

        $schedule->update($updateData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'trip_status' => $schedule->trip_status,
                'status_label' => $schedule->tripStatusLabel(),
                'next_action' => $schedule->nextStatusAction(),
            ]);
        }

        return redirect()->route('dashboard.driver.index', ['schedule_id' => $schedule->id])
            ->with('success', $message);
    }

    /**
     * AJAX: Sinkronisasi koordinat GPS latar belakang secara berkala
     */
    public function updateGps(Request $request, Schedule $schedule): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'speed' => 'nullable|numeric',
            'heading' => 'nullable|numeric',
        ]);

        $schedule->update([
            'current_latitude' => $request->input('latitude'),
            'current_longitude' => $request->input('longitude'),
            'last_gps_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Koordinat GPS berhasil diperbarui di server.',
            'latitude' => $schedule->current_latitude,
            'longitude' => $schedule->current_longitude,
            'updated_at' => Carbon::now()->format('H:i:s'),
        ]);
    }

    /**
     * Validasi penumpang (Check-in saat naik ke kabin & konfirmasi bayar COD/tunai)
     */
    public function updateCheckin(Request $request, BookingPassenger $passenger)
    {
        $request->validate([
            'boarding_status' => 'required|string|in:boarded,belum_hadir',
            'mark_paid' => 'nullable|boolean',
        ]);

        $status = $request->input('boarding_status');
        $boardedAt = ($status === 'boarded') ? Carbon::now() : null;

        $passenger->update([
            'boarding_status' => $status,
            'boarded_at' => $boardedAt,
        ]);

        // Jika supir mengonfirmasi penerimaan uang tunai / COD di tempat
        if ($request->boolean('mark_paid') && $passenger->booking) {
            $booking = $passenger->booking;
            $booking->update(['status' => 'confirmed']);

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'order_id' => 'COD-'.$booking->booking_code,
                    'payment_method' => 'cash',
                    'gross_amount' => $booking->total_price,
                    'status' => 'success',
                    'paid_at' => Carbon::now(),
                    'notes' => 'Pembayaran tunai/COD diterima langsung oleh Pengemudi di kabin armada.',
                ]
            );
        }

        $message = ($status === 'boarded')
            ? "Penumpang {$passenger->name} berhasil dikonfirmasi Naik ke Kabin (Kursi {$passenger->seat?->seat_number})."
            : "Status boarding {$passenger->name} diatur kembali ke Belum Naik.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'passenger_id' => $passenger->id,
                'boarding_status' => $passenger->boarding_status,
                'boarded_at' => $passenger->boarded_at?->format('H:i').' WIB',
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Form Cepat Lapor Kendala Perjalanan (Ban bocor, macet, mesin mogok, dll.)
     */
    public function reportIncident(Request $request, Schedule $schedule)
    {
        $request->validate([
            'category' => 'required|string|in:ban_bocor,macet_parah,mesin_mogok,penumpang_batal,kecelakaan,darurat_medis,sos_emergency,lainnya',
            'severity' => 'required|string|in:rendah,sedang,tinggi,darurat',
            'description' => 'required|string|max:1000',
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $driver = auth()->user()->driver;
        if (! $driver) {
            return back()->with('error', 'Akun pengemudi belum terdaftar.');
        }

        $incident = TripIncident::create([
            'schedule_id' => $schedule->id,
            'driver_id' => $driver->id,
            'category' => $request->input('category'),
            'severity' => $request->input('severity'),
            'description' => $request->input('description'),
            'location_name' => $request->input('location_name') ?: 'Jalur Rute Perjalanan',
            'latitude' => $request->input('latitude') ?: $schedule->current_latitude,
            'longitude' => $request->input('longitude') ?: $schedule->current_longitude,
            'status' => 'reported',
            'reported_at' => Carbon::now(),
        ]);

        // Jika kendala adalah kemacetan parah, otomatis kirim info keterlambatan ke penumpang
        if ($incident->category === 'macet_parah') {
            $this->notifyPassengers($schedule, 'delay_traffic', $incident->description);
        }

        $message = "Laporan kendala '{$incident->categoryLabel()}' berhasil dikirim ke Pusat Kendali Operasional.";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'incident' => $incident,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Tombol Darurat (Emergency SOS) 1-Klik
     */
    public function triggerSos(Request $request, Schedule $schedule)
    {
        $driver = auth()->user()->driver;
        if (! $driver) {
            return response()->json(['success' => false, 'message' => 'Driver tidak ditemukan.'], 404);
        }

        $latitude = $request->input('latitude') ?: $schedule->current_latitude;
        $longitude = $request->input('longitude') ?: $schedule->current_longitude;
        $desc = $request->input('description') ?: 'EMERGENCY SOS diaktifkan oleh pengemudi melalui tombol darurat dashboard.';

        $incident = TripIncident::create([
            'schedule_id' => $schedule->id,
            'driver_id' => $driver->id,
            'category' => 'sos_emergency',
            'severity' => 'darurat',
            'description' => $desc,
            'location_name' => $request->input('location_name') ?: 'Posisi Darurat Lapangan',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => 'reported',
            'emergency_dispatched' => true,
            'reported_at' => Carbon::now(),
        ]);

        Log::emergency("EMERGENCY SOS: Driver {$driver->user?->name} ({$driver->phone}) pada Armada {$schedule->vehicle?->plate_number}. Lokasi: Lat {$latitude}, Lng {$longitude}");

        $message = 'SINYAL DARURAT SOS TELAH TERKIRIM! Pusat Kendali Operasional dan tim darurat segera merespons ke koordinat Anda.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'incident_id' => $incident->id,
                'emergency_contacts' => [
                    ['name' => 'Pusat Kendali Travel (HQ)', 'phone' => '081122334455', 'tel' => 'tel:081122334455'],
                    ['name' => 'Ambulans Darurat', 'phone' => '118', 'tel' => 'tel:118'],
                    ['name' => 'Kepolisian Lalu Lintas', 'phone' => '110', 'tel' => 'tel:110'],
                ],
            ]);
        }

        return back()->with('emergency', $message);
    }

    /**
     * Helper untuk mengirim notifikasi operasional ke seluruh penumpang yang memesan jadwal ini
     */
    protected function notifyPassengers(Schedule $schedule, string $infoType, ?string $reason = null): void
    {
        try {
            $bookings = Booking::where('schedule_id', $schedule->id)
                ->whereNotIn('status', ['Cancelled', 'cancelled'])
                ->with(['user', 'schedule.vehicle', 'schedule.driver.user', 'schedule.route.origin'])
                ->get();

            foreach ($bookings as $b) {
                if ($b->user) {
                    $b->user->notify(new OperationalInfoNotification(
                        booking: $b,
                        infoType: $infoType,
                        plateNumber: $schedule->vehicle?->plate_number,
                        driverName: $schedule->driver?->user?->name,
                        pickupLocation: $schedule->route?->origin?->name,
                        delayReason: $reason,
                        delayMinutes: 20
                    ));
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal menyiarkan notifikasi operasional ke penumpang: '.$e->getMessage());
        }
    }
}
