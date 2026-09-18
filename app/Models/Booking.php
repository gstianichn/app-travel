<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'schedule_id',
        'total_passenger',
        'subtotal',
        'discount',
        'total_price',
        'status',
        'source',
        'notes',
        'rescheduled_at',
        'reschedule_reason',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'expired_at' => 'datetime',
            'rescheduled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }

    /**
     * Label ramah tampilan untuk asal / saluran pemesanan.
     */
    public function sourceLabel(): string
    {
        return match ($this->source) {
            'walk_in', 'loket' => 'Loket / Walk-in',
            'whatsapp', 'wa' => 'WhatsApp CS',
            'phone' => 'Telepon',
            default => 'Website Online',
        };
    }

    /**
     * CSS class badge untuk asal pemesanan.
     */
    public function sourceBadgeClass(): string
    {
        return match ($this->source) {
            'walk_in', 'loket' => 'badge-source-walkin',
            'whatsapp', 'wa' => 'badge-source-wa',
            'phone' => 'badge-source-phone',
            default => 'badge-source-online',
        };
    }

    /**
     * Label status pembayaran booking.
     */
    public function statusLabel(): string
    {
        $status = strtolower($this->status ?? 'pending');

        return match ($status) {
            'paid', 'confirmed', 'success', 'lunas' => 'Sudah Lunas',
            'cancelled', 'batal' => 'Dibatalkan',
            default => 'Dipesan Sementara (Pending)',
        };
    }

    /**
     * CSS class badge untuk status pembayaran booking.
     */
    public function statusBadgeClass(): string
    {
        $status = strtolower($this->status ?? 'pending');

        return match ($status) {
            'paid', 'confirmed', 'success', 'lunas' => 'badge-status-paid',
            'cancelled', 'batal' => 'badge-status-cancelled',
            default => 'badge-status-pending',
        };
    }

    /**
     * Mendapatkan nomor kursi yang dipesan dalam bentuk string (contoh: 1A, 2B).
     */
    public function passengerSeatNumbers(): string
    {
        $seats = $this->passengers
            ->map(function ($p) {
                return $p->seat?->seat_number;
            })
            ->filter()
            ->implode(', ');

        return $seats ?: '-';
    }

    /**
     * Nama pemesan / kontak utama.
     */
    public function customerName(): string
    {
        return $this->user?->name ?? ($this->passengers->first()?->name ?? 'Tamu Walk-in');
    }

    /**
     * Nomor telepon / WhatsApp pemesan utama.
     */
    public function customerPhone(): string
    {
        return $this->user?->phone ?? ($this->passengers->first()?->phone ?? '-');
    }
}
