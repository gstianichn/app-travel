<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'departure_date',
        'departure_time',
        'available_seats',
        'price',
        'notes',
        'travel_route_id',
        'vehicle_id',
        'driver_id',
        'departure_at',
        'arrival_at',
        'status',
        'trip_status',
        'pickup_started_at',
        'departed_at',
        'arrived_at',
        'completed_at',
        'current_latitude',
        'current_longitude',
        'last_gps_at',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'departure_time' => 'datetime:H:i',
            'departure_at' => 'datetime',
            'arrival_at' => 'datetime',
            'pickup_started_at' => 'datetime',
            'departed_at' => 'datetime',
            'arrived_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_gps_at' => 'datetime',
            'current_latitude' => 'decimal:7',
            'current_longitude' => 'decimal:7',
            'price' => 'decimal:2',
        ];
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function travelRoute()
    {
        return $this->belongsTo(TravelRoute::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function incidents()
    {
        return $this->hasMany(TripIncident::class);
    }

    /**
     * Mendapatkan denah kursi dan status okupansi untuk jadwal ini:
     * - available: Kosong / Tersedia (Hijau)
     * - pending: Dipesan Sementara (Kuning)
     * - paid: Sudah Lunas (Biru)
     */
    public function getSeatOccupancyMap(): array
    {
        $vehicle = $this->vehicle;
        if (! $vehicle) {
            return [];
        }

        $vehicle->ensureSeatsExist();
        $seats = $vehicle->seats()->orderBy('seat_row')->orderBy('seat_column')->get();

        // Cari semua passenger yang memesan jadwal ini di booking yang aktif (tidak dibatalkan)
        $activeBookings = $this->bookings()
            ->with(['passengers.seat', 'user'])
            ->whereNotIn('status', ['Cancelled', 'cancelled'])
            ->get();

        $occupiedSeats = [];
        foreach ($activeBookings as $booking) {
            $isPaid = in_array(strtolower($booking->status), ['paid', 'confirmed', 'success', 'lunas']);
            $statusType = $isPaid ? 'paid' : 'pending';

            foreach ($booking->passengers as $passenger) {
                if ($passenger->seat_id) {
                    $occupiedSeats[$passenger->seat_id] = [
                        'status' => $statusType,
                        'passenger_name' => $passenger->name,
                        'passenger_phone' => $passenger->phone ?? $booking->customerPhone(),
                        'special_request' => $passenger->special_request,
                        'booking_code' => $booking->booking_code,
                        'booking_id' => $booking->id,
                        'booking_status' => $booking->statusLabel(),
                        'booking_source' => $booking->sourceLabel(),
                    ];
                }
            }
        }

        $rows = [];
        foreach ($seats as $seat) {
            $occupancy = $occupiedSeats[$seat->id] ?? [
                'status' => 'available',
                'passenger_name' => null,
                'passenger_phone' => null,
                'special_request' => null,
                'booking_code' => null,
                'booking_id' => null,
                'booking_status' => null,
                'booking_source' => null,
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
     * Menghitung ulang dan memperbarui sisa kursi yang tersedia pada jadwal ini.
     */
    public function recalculateAvailableSeats(): int
    {
        $vehicle = $this->vehicle;
        $totalCapacity = $vehicle ? $vehicle->capacity : 10;

        $occupiedCount = BookingPassenger::whereHas('booking', function ($q) {
            $q->where('schedule_id', $this->id)
                ->whereNotIn('status', ['Cancelled', 'cancelled']);
        })->count();

        $available = max(0, $totalCapacity - $occupiedCount);
        $this->available_seats = $available;
        $this->save();

        return $available;
    }

    /**
     * Menghitung tarif tiket yang berlaku untuk jadwal keberangkatan ini.
     */
    public function effectivePrice(): float
    {
        if (! empty($this->price) && $this->price > 0) {
            return (float) $this->price;
        }

        if ($this->route) {
            return (float) $this->route->getPriceForDate($this->departure_date);
        }

        return 100000.0;
    }

    public function formattedEffectivePrice(): string
    {
        return 'Rp '.number_format($this->effectivePrice(), 0, ',', '.');
    }

    public function isWeekendDeparture(): bool
    {
        if (! $this->departure_date) {
            return false;
        }

        return $this->departure_date->isWeekend();
    }

    public function loadFactorPercentage(): int
    {
        $cap = $this->vehicle ? $this->vehicle->capacity : 10;
        if ($cap <= 0) {
            return 0;
        }

        $occupied = max(0, $cap - ($this->available_seats ?? $cap));

        return (int) min(100, round(($occupied / $cap) * 100));
    }

    /**
     * Kode / nomor trip perjalanan (contoh: TRP-20260923-0101)
     */
    public function tripCode(): string
    {
        $dateStr = $this->departure_date ? $this->departure_date->format('Ymd') : Carbon::today()->format('Ymd');

        return 'TRP-'.$dateStr.'-'.str_pad((string) ($this->id ?? 1), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Tanggal perjalanan bahasa Indonesia
     */
    public function formattedDepartureDate(): string
    {
        if ($this->departure_date) {
            return $this->departure_date->translatedFormat('l, d F Y');
        }

        return Carbon::today()->translatedFormat('l, d F Y');
    }

    /**
     * Waktu keberangkatan sebagai Carbon instance
     */
    public function departureCarbon(): Carbon
    {
        $dateStr = $this->departure_date ? $this->departure_date->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $timeStr = '13:00:00';

        if ($this->departure_time instanceof Carbon) {
            $timeStr = $this->departure_time->format('H:i:s');
        } elseif (is_string($this->departure_time)) {
            $parts = explode(' ', trim($this->departure_time));
            $timeStr = end($parts);
        }

        return Carbon::parse($dateStr.' '.$timeStr);
    }

    /**
     * Jam keberangkatan format string (13:00 WIB)
     */
    public function formattedDepartureTime(): string
    {
        return $this->departureCarbon()->format('H:i').' WIB';
    }

    /**
     * Estimasi waktu tiba di tujuan
     */
    public function estimatedArrivalTime(): string
    {
        $hours = 2;
        $minutes = 45;

        if ($this->route && ! empty($this->route->duration)) {
            if (preg_match('/(\d+)\s*jam/i', $this->route->duration, $mH)) {
                $hours = (int) $mH[1];
            }
            if (preg_match('/(\d+)\s*menit/i', $this->route->duration, $mM)) {
                $minutes = (int) $mM[1];
            }
        }

        return $this->departureCarbon()->copy()->addHours($hours)->addMinutes($minutes)->format('H:i').' WIB';
    }

    /**
     * Waktu persiapan armada di garasi (H-45 Menit)
     */
    public function prepTime(): string
    {
        return $this->departureCarbon()->copy()->subMinutes(45)->format('H:i').' WIB';
    }

    /**
     * Waktu sandar di bay pool / area muat (H-30 Menit)
     */
    public function gatheringTime(): string
    {
        return $this->departureCarbon()->copy()->subMinutes(30)->format('H:i').' WIB';
    }

    /**
     * Waktu boarding gate dibuka (H-15 Menit)
     */
    public function boardingTime(): string
    {
        return $this->departureCarbon()->copy()->subMinutes(15)->format('H:i').' WIB';
    }

    /**
     * Label status perjalanan pengemudi
     */
    public function tripStatusKey(): string
    {
        $status = strtolower($this->trip_status ?? 'scheduled');
        if (in_array($status, ['pickup', 'mulai_jemput', 'jemput'])) {
            return 'pickup';
        }
        if (in_array($status, ['departed', 'berangkat', 'in_progress', 'jalan'])) {
            return 'departed';
        }
        if (in_array($status, ['arrived', 'tiba', 'sampai'])) {
            return 'arrived';
        }
        if (in_array($status, ['completed', 'selesai', 'finish'])) {
            return 'completed';
        }

        return 'scheduled';
    }

    /**
     * Label teks status perjalanan bahasa Indonesia
     */
    public function tripStatusLabel(): string
    {
        return match ($this->tripStatusKey()) {
            'pickup' => 'Proses Penjemputan',
            'departed' => 'Dalam Perjalanan',
            'arrived' => 'Tiba di Tujuan',
            'completed' => 'Perjalanan Selesai',
            default => 'Menunggu Keberangkatan',
        };
    }

    /**
     * CSS badge class untuk status perjalanan
     */
    public function tripStatusBadgeClass(): string
    {
        return match ($this->tripStatusKey()) {
            'pickup' => 'badge-status-pickup',
            'departed' => 'badge-status-departed',
            'arrived' => 'badge-status-arrived',
            'completed' => 'badge-status-completed',
            default => 'badge-status-scheduled',
        };
    }

    /**
     * Aksi status berikutnya dalam alur perjalanan
     *
     * @return array{next_key: string, label: string, btn_class: string, icon: string, prompt: string}|null
     */
    public function nextStatusAction(): ?array
    {
        return match ($this->tripStatusKey()) {
            'scheduled' => [
                'next_key' => 'pickup',
                'label' => 'Mulai Jemput',
                'btn_class' => 'btn-action-pickup',
                'icon' => '▶️',
                'prompt' => 'Mulai penjemputan penumpang? Notifikasi akan dikirimkan kepada penumpang bahwa armada sedang merapat.',
            ],
            'pickup' => [
                'next_key' => 'departed',
                'label' => 'Berangkat Sekarang',
                'btn_class' => 'btn-action-departed',
                'icon' => '🚀',
                'prompt' => 'Apakah seluruh penumpang telah siap dan armada siap meluncur ke kota tujuan?',
            ],
            'departed' => [
                'next_key' => 'arrived',
                'label' => 'Tiba di Pool Tujuan',
                'btn_class' => 'btn-action-arrived',
                'icon' => '🏁',
                'prompt' => 'Konfirmasi armada telah tiba dengan selamat di titik pool kedatangan?',
            ],
            'arrived' => [
                'next_key' => 'completed',
                'label' => 'Selesaikan Ritase',
                'btn_class' => 'btn-action-complete',
                'icon' => '✓',
                'prompt' => 'Tutup perjalanan dan simpan rekap ritase operasional harian Anda?',
            ],
            default => null,
        };
    }

    /**
     * Menghasilkan urutan titik rute / waypoint navigasi (Pool Asal -> Titik Jemput Penumpang -> Pool Tujuan)
     *
     * @return array<int, array{type: string, title: string, subtitle: string, address: string, eta: string, gmaps_url: string, waze_url: string, status: string}>
     */
    public function navigationWaypoints(): array
    {
        $origin = $this->route?->origin;
        $destination = $this->route?->destination;

        $originName = $origin?->name ?? 'Pool Keberangkatan';
        $originAddress = $origin?->address ?? ($originName.', '.$origin?->city);

        $destName = $destination?->name ?? 'Pool Tujuan';
        $destAddress = $destination?->address ?? ($destName.', '.$destination?->city);

        $waypoints = [];

        // 1. Titik Awal (Pool Asal)
        $waypoints[] = [
            'type' => 'origin',
            'badge' => 'TITIK AWAL',
            'title' => $originName,
            'subtitle' => 'Pool Keberangkatan & Parkir Armada',
            'address' => $originAddress,
            'eta' => '07:30 WIB',
            'gmaps_url' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($originAddress),
            'waze_url' => 'https://waze.com/ul?q='.urlencode($originAddress).'&navigate=yes',
            'status' => in_array($this->tripStatusKey(), ['pickup', 'departed', 'arrived', 'completed']) ? 'passed' : 'active',
        ];

        // 2. Titik Jemput Penumpang (Dihimpun dari manifes penumpang aktif)
        $passengers = BookingPassenger::whereHas('booking', function ($q) {
            $q->where('schedule_id', $this->id)
                ->whereNotIn('status', ['Cancelled', 'cancelled']);
        })->with(['booking.user', 'seat'])->get();

        $pickupGroups = [];
        foreach ($passengers as $p) {
            $point = $p->pickup_point ?: 'Pool Pasteur (Gate Tol)';
            if (! isset($pickupGroups[$point])) {
                $pickupGroups[$point] = [];
            }
            $pickupGroups[$point][] = ($p->seat?->seat_number ? "Kursi {$p->seat->seat_number}: " : '').$p->name;
        }

        if (empty($pickupGroups)) {
            $pickupGroups['Halte Pasteur (Titik Jemput)'] = ['2 Penumpang Dijemput'];
        }

        $stepIdx = 1;
        foreach ($pickupGroups as $pointName => $pNames) {
            $pSummary = implode(', ', array_slice($pNames, 0, 3));
            if (count($pNames) > 3) {
                $pSummary .= ' +'.(count($pNames) - 3).' lainnya';
            }

            $waypoints[] = [
                'type' => 'pickup',
                'badge' => "JEMPUT #{$stepIdx}",
                'title' => $pointName,
                'subtitle' => $pSummary,
                'address' => $pointName.', '.$origin?->city,
                'eta' => '+'.(15 * $stepIdx).' Menit',
                'gmaps_url' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($pointName.' '.$origin?->city),
                'waze_url' => 'https://waze.com/ul?q='.urlencode($pointName.' '.$origin?->city).'&navigate=yes',
                'status' => in_array($this->tripStatusKey(), ['departed', 'arrived', 'completed']) ? 'passed' : 'active',
            ];
            $stepIdx++;
        }

        // 3. Rest Area (Titik Istirahat Jalan Tol)
        $restAreaName = 'Rest Area KM 57 Tol Japek';
        $waypoints[] = [
            'type' => 'transit',
            'badge' => 'REST AREA',
            'title' => $restAreaName,
            'subtitle' => 'Istirahat & Toilet Break (15 Menit)',
            'address' => 'Tol Jakarta-Cikampek KM 57',
            'eta' => '+1 Jam 30 Menit',
            'gmaps_url' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($restAreaName),
            'waze_url' => 'https://waze.com/ul?q='.urlencode($restAreaName).'&navigate=yes',
            'status' => in_array($this->tripStatusKey(), ['arrived', 'completed']) ? 'passed' : 'pending',
        ];

        // 4. Titik Akhir (Pool Tujuan)
        $waypoints[] = [
            'type' => 'destination',
            'badge' => 'TUJUAN AKHIR',
            'title' => $destName,
            'subtitle' => 'Pool Kedatangan & Penurunan Penumpang',
            'address' => $destAddress,
            'eta' => 'Estimasi Tiba: 2 Jam 45 Mnt',
            'gmaps_url' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($destAddress),
            'waze_url' => 'https://waze.com/ul?q='.urlencode($destAddress).'&navigate=yes',
            'status' => $this->tripStatusKey() === 'completed' ? 'passed' : 'pending',
        ];

        return $waypoints;
    }

    /**
     * Memastikan jadwal reguler tersedia untuk rute dan tanggal keberangkatan tertentu.
     * Jika rute aktif namun belum memiliki jadwal pada tanggal tersebut, otomatis buat jadwal standar harian.
     */
    public static function ensureSchedulesExistForDateAndRoute(Route $route, string $departureDate): void
    {
        $existingCount = static::where('route_id', $route->id)
            ->whereDate('departure_date', $departureDate)
            ->count();

        if ($existingCount > 0) {
            return;
        }

        $vehicles = Vehicle::whereIn('status', ['siap_jalan', 'beroperasi', 'ready', 'active'])->get();
        if ($vehicles->isEmpty()) {
            return;
        }

        $driver = Driver::where('status', 'standby')->first() ?? Driver::first();

        $standardTimes = ['07:00', '10:00', '14:00', '18:30'];
        foreach ($standardTimes as $idx => $time) {
            $vehicle = $vehicles[$idx % $vehicles->count()];
            $seatCap = $vehicle->capacity ?: 10;
            $vehicle->ensureSeatsExist();

            static::create([
                'route_id' => $route->id,
                'departure_date' => $departureDate,
                'departure_time' => $time,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver?->id,
                'available_seats' => max(4, $seatCap - 2),
                'price' => $route->price ?: 125000,
                'status' => 'Scheduled',
                'trip_status' => 'scheduled',
                'notes' => 'Keberangkatan reguler armada '.$vehicle->name.'.',
            ]);
        }
    }
}
