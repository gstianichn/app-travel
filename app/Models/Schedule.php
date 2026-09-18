<?php

namespace App\Models;

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
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'departure_time' => 'datetime:H:i',
            'departure_at' => 'datetime',
            'arrival_at' => 'datetime',
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
}
