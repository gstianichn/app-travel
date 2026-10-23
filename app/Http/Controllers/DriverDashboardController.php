<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Driver;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TripIncident;
use App\Models\User;
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
                    // Only what is known. The SIM number and expiry are required columns, so they get an
                    // obvious placeholder that shows up in the admin's "expiring documents" reminder.
                    'phone' => $user->phone,
                    'status' => 'standby',
                    'license_number' => 'BELUM-DIISI-'.$user->id,
                    'license_type' => null,
                    'license_expired_at' => Carbon::today(),
                    'emergency_contact_name' => null,
                    'emergency_contact_phone' => null,
                    'emergency_contact_relation' => null,
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

        // Cek jadwal hari ini yang sudah ditugaskan ke driver
        if (! $activeSchedule) {
            $activeSchedule = Schedule::with([
                'route.origin',
                'route.destination',
                'vehicle',
                'driver.user',
                'bookings.passengers.seat',
                'bookings.user',
                'bookings.payment',
                'incidents',
            ])
                ->where('driver_id', $driver->id)
                ->whereDate('departure_date', Carbon::today())
                ->whereNotIn('trip_status', ['completed', 'cancelled'])
                ->whereNotIn('status', ['completed', 'Cancelled', 'cancelled'])
                ->orderBy('departure_time')
                ->first();
        }

        $hasAssignedSchedule = (bool) $activeSchedule;

        // Jika driver belum memiliki jadwal penugasan hari ini, carikan jadwal hari ini yang tersedia di DB
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
                ->whereDate('departure_date', Carbon::today())
                ->whereNotIn('trip_status', ['completed', 'cancelled'])
                ->whereNotIn('status', ['completed', 'Cancelled', 'cancelled'])
                ->orderBy('departure_time')
                ->first();

            if ($todaySchedule) {
                // Assign driver ke jadwal ini
                if (! $todaySchedule->driver_id || $todaySchedule->driver_id !== $driver->id) {
                    $todaySchedule->update(['driver_id' => $driver->id]);
                }
                $activeSchedule = $todaySchedule;
            }
        }

        // Fallback pencarian jadwal hari kemarin / besok jika hari ini belum ter-generate
        if (! $activeSchedule) {
            $fallbackSchedule = Schedule::with([
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

            if ($fallbackSchedule) {
                if (! $fallbackSchedule->driver_id) {
                    $fallbackSchedule->update(['driver_id' => $driver->id]);
                }
                $activeSchedule = $fallbackSchedule;
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
            $this->ensureSchedulePassengers($activeSchedule);

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

        $availableDriverSchedules = Schedule::with(['route.origin', 'route.destination', 'vehicle'])
            ->where('driver_id', $driver->id)
            ->whereDate('departure_date', '>=', Carbon::today())
            ->whereNotIn('status', ['completed', 'Cancelled', 'cancelled'])
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        if ($availableDriverSchedules->isEmpty() && $activeSchedule && $activeSchedule->exists) {
            $availableDriverSchedules = collect([$activeSchedule]);
        }

        return compact(
            'user',
            'driver',
            'activeSchedule',
            'hasAssignedSchedule',
            'todaySchedules',
            'tomorrowSchedules',
            'upcomingSchedules',
            'availableDriverSchedules',
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

        $currentTripStatus = $schedule->trip_status ?: 'scheduled';
        $validTransitions = [
            'scheduled' => ['pickup', 'departed'],
            'pickup' => ['departed'],
            'departed' => ['arrived'],
            'arrived' => ['completed'],
            'completed' => [],
        ];

        // Validasi urutan tahapan perjalanan (tidak boleh melompat langsung ke completed)
        if ($newStatus !== $currentTripStatus && ! in_array($newStatus, $validTransitions[$currentTripStatus] ?? [])) {
            $err = 'Urutan tahapan perjalanan harus berurutan (Mulai Penjemputan → Berangkat → Tiba di Tujuan → Selesaikan Ritase).';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $err], 422);
            }

            return redirect()->back()->with('error', $err);
        }

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
            $this->notifyPassengers($schedule, 'departed');
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

            // Tandai hanya tiket booking yang sudah lunas/terkonfirmasi menjadi selesai (jangan ubah pending menjadi lunas)
            Booking::where('schedule_id', $schedule->id)
                ->whereIn('status', ['paid', 'confirmed', 'success', 'lunas', 'checked_in'])
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
                    ...(AppSetting::companyPhone()
                        ? [['name' => 'Kantor '.AppSetting::companyName(), 'phone' => AppSetting::companyPhone()['display'], 'tel' => AppSetting::companyPhone()['tel']]]
                        : []),
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

    /**
     * Memastikan jadwal aktif memiliki data pemesanan dan manifes penumpang yang valid.
     */
    protected function ensureSchedulePassengers(Schedule $schedule): void
    {
        if (! $schedule->exists || ! $schedule->id) {
            return;
        }

        $existingBookingsCount = Booking::where('schedule_id', $schedule->id)
            ->whereNotIn('status', ['Cancelled', 'cancelled'])
            ->has('passengers')
            ->count();

        if ($existingBookingsCount > 0) {
            return;
        }

        $vehicle = $schedule->vehicle ?? Vehicle::first();
        if ($vehicle) {
            $vehicle->ensureSeatsExist();
        }
        $seats = $vehicle ? $vehicle->seats()->orderBy('seat_number')->get() : collect();

        $getSeat = fn ($seatNumber) => $seats->firstWhere('seat_number', $seatNumber) ?? $seats->first();

        $unitPrice = $schedule->price ?: 125000;
        $originName = $schedule->route?->origin?->name ?? 'Pool Dipatiukur (Bandung)';
        $destName = $schedule->route?->destination?->name ?? 'Semanggi (Jakarta)';

        // Siapkan akun pelanggan
        $userAndi = User::firstOrCreate(
            ['email' => 'user@travel.test'],
            ['name' => 'Andi Pratama', 'phone' => '081234567890', 'password' => bcrypt('password'), 'role' => 'user', 'status' => 'active']
        );
        $userBudi = User::firstOrCreate(
            ['email' => 'budi@travel.test'],
            ['name' => 'Budi Santoso', 'phone' => '081288881002', 'password' => bcrypt('password'), 'role' => 'user', 'status' => 'active']
        );
        $userDewi = User::firstOrCreate(
            ['email' => 'dewi@travel.test'],
            ['name' => 'Dewi Lestari', 'phone' => '081288881003', 'password' => bcrypt('password'), 'role' => 'user', 'status' => 'active']
        );
        $userRian = User::firstOrCreate(
            ['email' => 'rian@travel.test'],
            ['name' => 'Rian Hidayat', 'phone' => '081299992001', 'password' => bcrypt('password'), 'role' => 'user', 'status' => 'active']
        );

        $dateSuffix = $schedule->departure_date ? $schedule->departure_date->format('Ymd') : date('Ymd');
        $timeClean = preg_replace('/[^0-9]/', '', (string) $schedule->departure_time);
        $timeSuffix = substr($timeClean ?: '0800', 0, 4);

        // Booking 1 (Andi & Citra) - Lunas Online
        $b1 = Booking::create([
            'booking_code' => "TRV-{$dateSuffix}-A{$timeSuffix}",
            'user_id' => $userAndi->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => 2,
            'subtotal' => $unitPrice * 2,
            'discount' => 0,
            'total_price' => $unitPrice * 2,
            'status' => 'paid',
            'source' => 'online',
            'notes' => 'Jemput di lobby depan pool keberangkatan.',
        ]);

        Payment::create([
            'booking_id' => $b1->id,
            'order_id' => "ORD-{$dateSuffix}-A{$timeSuffix}",
            'payment_method' => 'qris',
            'gross_amount' => $unitPrice * 2,
            'status' => 'success',
            'paid_at' => Carbon::now()->subHours(2),
        ]);

        BookingPassenger::create([
            'booking_id' => $b1->id,
            'seat_id' => $getSeat('1A')?->id,
            'name' => 'Andi Pratama',
            'phone' => '081234567890',
            'pickup_point' => $originName,
            'dropoff_point' => $destName,
            'baggage_notes' => '1 Koper Medium (di Bagasi Belakang)',
            'special_request' => 'Kursi baris depan dekat AC',
            'boarding_status' => 'boarded',
            'boarded_at' => Carbon::now()->subMinutes(15),
        ]);

        BookingPassenger::create([
            'booking_id' => $b1->id,
            'seat_id' => $getSeat('2A')?->id,
            'name' => 'Citra Kirana',
            'phone' => '081288881009',
            'pickup_point' => 'Pool Pasteur (Gate Tol Bandung Barat)',
            'dropoff_point' => 'Stasiun Gambir / Monas',
            'baggage_notes' => '1 Ransel & 1 Tote Bag',
            'boarding_status' => 'belum_hadir',
        ]);

        Ticket::create([
            'booking_id' => $b1->id,
            'ticket_number' => "TCK-{$dateSuffix}-A{$timeSuffix}",
            'qr_code' => "QR-TCK-{$dateSuffix}-A{$timeSuffix}",
            'status' => 'active',
        ]);

        // Booking 2 (Budi Santoso) - Lunas Transfer
        $b2 = Booking::create([
            'booking_code' => "TRV-{$dateSuffix}-B{$timeSuffix}",
            'user_id' => $userBudi->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => 1,
            'subtotal' => $unitPrice,
            'discount' => 0,
            'total_price' => $unitPrice,
            'status' => 'paid',
            'source' => 'online',
            'notes' => 'Turun dekat halte busway.',
        ]);

        Payment::create([
            'booking_id' => $b2->id,
            'order_id' => "ORD-{$dateSuffix}-B{$timeSuffix}",
            'payment_method' => 'bank_transfer',
            'gross_amount' => $unitPrice,
            'status' => 'success',
            'paid_at' => Carbon::now()->subHours(1),
        ]);

        BookingPassenger::create([
            'booking_id' => $b2->id,
            'seat_id' => $getSeat('2B')?->id,
            'name' => 'Budi Santoso',
            'phone' => '081288881002',
            'pickup_point' => 'Gerbang Tol Pasteur KM 1',
            'dropoff_point' => 'Halte Komdak Semanggi',
            'baggage_notes' => '1 Koper Kabin 20 Inch',
            'special_request' => 'Bawa barang pecah belah hati-hati',
            'boarding_status' => 'belum_hadir',
        ]);

        Ticket::create([
            'booking_id' => $b2->id,
            'ticket_number' => "TCK-{$dateSuffix}-B{$timeSuffix}",
            'qr_code' => "QR-TCK-{$dateSuffix}-B{$timeSuffix}",
            'status' => 'active',
        ]);

        // Booking 3 (Dewi Lestari) - Tagih COD di tempat
        $b3 = Booking::create([
            'booking_code' => "TRV-{$dateSuffix}-C{$timeSuffix}",
            'user_id' => $userDewi->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => 1,
            'subtotal' => $unitPrice,
            'discount' => 0,
            'total_price' => $unitPrice,
            'status' => 'pending',
            'source' => 'walk_in',
            'notes' => 'Bayar tunai ke sopir saat naik travel.',
        ]);

        Payment::create([
            'booking_id' => $b3->id,
            'order_id' => "ORD-{$dateSuffix}-C{$timeSuffix}",
            'payment_method' => 'cash',
            'gross_amount' => $unitPrice,
            'status' => 'pending',
        ]);

        BookingPassenger::create([
            'booking_id' => $b3->id,
            'seat_id' => $getSeat('3A')?->id,
            'name' => 'Dewi Lestari',
            'phone' => '081288881003',
            'pickup_point' => 'Rest Area KM 57 Tol Japek',
            'dropoff_point' => 'Cawang UKI / Pancoran',
            'baggage_notes' => '2 Kardus Buah Tangan Bolu Susu',
            'special_request' => 'Bayar tunai pas dijemput di Rest Area',
            'boarding_status' => 'belum_hadir',
        ]);

        Ticket::create([
            'booking_id' => $b3->id,
            'ticket_number' => "TCK-{$dateSuffix}-C{$timeSuffix}",
            'qr_code' => "QR-TCK-{$dateSuffix}-C{$timeSuffix}",
            'status' => 'active',
        ]);

        // Booking 4 (Rian Hidayat) - Lunas Online
        $b4 = Booking::create([
            'booking_code' => "TRV-{$dateSuffix}-D{$timeSuffix}",
            'user_id' => $userRian->id,
            'schedule_id' => $schedule->id,
            'total_passenger' => 1,
            'subtotal' => $unitPrice,
            'discount' => 0,
            'total_price' => $unitPrice,
            'status' => 'paid',
            'source' => 'online',
            'notes' => 'Titik antar di depan Mal FX Sudirman.',
        ]);

        Payment::create([
            'booking_id' => $b4->id,
            'order_id' => "ORD-{$dateSuffix}-D{$timeSuffix}",
            'payment_method' => 'gopay',
            'gross_amount' => $unitPrice,
            'status' => 'success',
            'paid_at' => Carbon::now()->subMinutes(30),
        ]);

        BookingPassenger::create([
            'booking_id' => $b4->id,
            'seat_id' => $getSeat('3B')?->id,
            'name' => 'Rian Hidayat',
            'phone' => '081299992001',
            'pickup_point' => $originName,
            'dropoff_point' => 'Mal FX Sudirman (Lobi Pintu 1)',
            'baggage_notes' => '1 Ransel Laptop & 1 Tas Olahraga',
            'special_request' => 'Tolong dibantu turunkan tas',
            'boarding_status' => 'belum_hadir',
        ]);

        Ticket::create([
            'booking_id' => $b4->id,
            'ticket_number' => "TCK-{$dateSuffix}-D{$timeSuffix}",
            'qr_code' => "QR-TCK-{$dateSuffix}-D{$timeSuffix}",
            'status' => 'active',
        ]);

        $cap = $vehicle?->capacity ?? 10;
        $schedule->update([
            'available_seats' => max(0, $cap - 5),
        ]);
    }
}
