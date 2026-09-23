<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPassenger extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'seat_id',
        'name',
        'phone',
        'pickup_point',
        'dropoff_point',
        'baggage_notes',
        'special_request',
        'boarding_status',
        'boarded_at',
    ];

    protected function casts(): array
    {
        return [
            'boarded_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Titik jemput dengan fallback ke origin route
     */
    public function pickupLabel(): string
    {
        if (! empty($this->pickup_point)) {
            return $this->pickup_point;
        }

        return $this->booking?->schedule?->route?->origin?->name ?? 'Pool Keberangkatan';
    }

    /**
     * Titik antar dengan fallback ke destination route
     */
    public function dropoffLabel(): string
    {
        if (! empty($this->dropoff_point)) {
            return $this->dropoff_point;
        }

        return $this->booking?->schedule?->route?->destination?->name ?? 'Pool Kedatangan';
    }

    /**
     * Catatan bagasi penumpang
     */
    public function baggageLabel(): string
    {
        if (! empty($this->baggage_notes)) {
            return $this->baggage_notes;
        }

        if (! empty($this->special_request)) {
            return $this->special_request;
        }

        return '1 Tas Kabin Standar';
    }

    /**
     * Nomor telepon terformat untuk tautan WhatsApp (62xxx)
     */
    public function waPhone(): string
    {
        $phone = $this->phone ?: ($this->booking?->user?->phone ?? '');
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            $clean = '62'.substr($clean, 1);
        }

        return $clean ?: '6281234567890';
    }

    /**
     * Nomor telepon terformat untuk tel: link
     */
    public function telPhone(): string
    {
        return $this->phone ?: ($this->booking?->user?->phone ?? '-');
    }

    /**
     * Cek apakah status penumpang sudah naik ke kabin
     */
    public function isBoarded(): bool
    {
        return in_array(strtolower($this->boarding_status ?? ''), ['boarded', 'sudah_naik', 'naik', 'hadir'], true);
    }
}
