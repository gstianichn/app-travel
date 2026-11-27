<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CsConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_code',
        'user_id',
        'booking_id',
        'assigned_to',
        'customer_name',
        'customer_phone',
        'subject',
        'status',
        'last_customer_message_at',
        'last_agent_reply_at',
        'last_message_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'last_customer_message_at' => 'datetime',
            'last_agent_reply_at' => 'datetime',
            'last_message_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CsMessage::class, 'conversation_id')->orderBy('created_at')->orderBy('id');
    }

    public static function generateCode(): string
    {
        $prefix = 'CHT-'.now()->format('Ymd').'-';

        do {
            $code = $prefix.strtoupper(Str::random(4));
        } while (self::where('conversation_code', $code)->exists());

        return $code;
    }

    /**
     * Hitung tingkat urgensi berdasarkan waktu keberangkatan jadwal pada booking terkait.
     *
     * @return array{level: string, score: int, label: string, badge_class: string}
     */
    public function departureUrgency(): array
    {
        $schedule = $this->booking?->schedule;
        if (! $schedule) {
            return [
                'level' => 'normal',
                'score' => 4,
                'label' => 'Reguler',
                'badge_class' => 'bg-slate-100 text-slate-700 border-slate-200',
            ];
        }

        $departure = $schedule->departureCarbon();
        $minutesUntil = (int) now()->diffInMinutes($departure, false);

        if ($minutesUntil >= -60 && $minutesUntil <= 120) {
            $label = $minutesUntil <= 0 ? 'Sedang Berangkat' : 'Berangkat < 2 Jam';

            return [
                'level' => 'critical',
                'score' => 1,
                'label' => $label,
                'badge_class' => 'bg-rose-100 text-rose-800 border-rose-300',
            ];
        }

        if ($departure->isToday() && $minutesUntil > 120) {
            return [
                'level' => 'today',
                'score' => 2,
                'label' => 'Berangkat Hari Ini',
                'badge_class' => 'bg-amber-100 text-amber-900 border-amber-300',
            ];
        }

        if ($departure->isFuture()) {
            return [
                'level' => 'upcoming',
                'score' => 3,
                'label' => 'Jadwal Mendatang',
                'badge_class' => 'bg-sky-50 text-sky-800 border-sky-200',
            ];
        }

        return [
            'level' => 'normal',
            'score' => 4,
            'label' => 'Pasca Perjalanan',
            'badge_class' => 'bg-slate-100 text-slate-600 border-slate-200',
        ];
    }

    /**
     * Apakah pelanggan sedang menunggu balasan CS (SLA timer aktif).
     */
    public function isWaitingForReply(): bool
    {
        if ($this->status === 'resolved' || ! $this->last_customer_message_at) {
            return false;
        }

        if (! $this->last_agent_reply_at) {
            return true;
        }

        return $this->last_customer_message_at->greaterThan($this->last_agent_reply_at);
    }

    /**
     * Timestamp acuan SLA timer (ISO-8601) ketika menunggu balasan CS.
     */
    public function slaAnchorIso(): ?string
    {
        if (! $this->isWaitingForReply()) {
            return null;
        }

        return $this->last_customer_message_at?->toIso8601String();
    }
}
