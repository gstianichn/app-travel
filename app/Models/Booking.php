<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
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
        'contact_name',
        'contact_phone',
        'contact_email',
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

    public function review(): HasOne
    {
        return $this->hasOne(TripReview::class);
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
        return $this->contact_name ?: ($this->user?->name ?? ($this->passengers->first()?->name ?? 'Tamu Walk-in'));
    }

    /**
     * Nomor telepon / WhatsApp pemesan utama.
     */
    public function customerPhone(): string
    {
        return $this->contact_phone ?: ($this->user?->phone ?? ($this->passengers->first()?->phone ?? '-'));
    }

    /**
     * Email the e-ticket goes to: the one typed at checkout, else the account email.
     */
    public function customerEmail(): ?string
    {
        return $this->contact_email ?: $this->user?->email;
    }

    /**
     * Booked without an account (guest checkout).
     */
    public function isGuestBooking(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Whether payment has gone through and the e-ticket is issued.
     */
    public function isPaid(): bool
    {
        return in_array(strtolower($this->status ?? ''), ['paid', 'confirmed', 'success', 'lunas', 'checked_in', 'completed'], true);
    }

    /**
     * Dapatkan Carbon instance waktu keberangkatan yang akurat.
     */
    public function getDepartureDateTimeAttribute(): Carbon
    {
        if (! $this->schedule) {
            return Carbon::now();
        }

        $dateStr = $this->schedule->departure_date ? $this->schedule->departure_date->format('Y-m-d') : date('Y-m-d');
        $rawTime = $this->schedule->departure_time;

        if ($rawTime instanceof CarbonInterface) {
            $timeStr = $rawTime->format('H:i:s');
        } elseif (is_string($rawTime)) {
            $parts = explode(' ', trim($rawTime));
            $timeStr = end($parts);
        } else {
            $timeStr = '00:00:00';
        }

        return Carbon::parse($dateStr.' '.$timeStr);
    }

    /**
     * Evaluasi apakah pemesanan masih berada dalam cut-off time pembatalan & refund (maks. H-1 jam).
     */
    public function canRefund(): bool
    {
        if (! in_array(strtolower($this->status ?? ''), ['paid', 'confirmed', 'success', 'lunas'])) {
            return false;
        }

        if (! $this->schedule) {
            return false;
        }

        return Carbon::now()->diffInHours($this->departure_date_time, false) >= 1;
    }

    /**
     * Hitung sisa jam hingga keberangkatan.
     */
    public function hoursUntilDeparture(): int
    {
        if (! $this->schedule) {
            return 0;
        }

        return (int) Carbon::now()->diffInHours($this->departure_date_time, false);
    }
}
