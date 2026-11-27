<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CustomerComplaint extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'barang_tertinggal' => 'Barang Tertinggal',
        'sopir_terlambat' => 'Sopir Terlambat',
        'keluhan_armada_ac' => 'Keluhan Armada/AC',
        'kendala_aplikasi' => 'Kendala Aplikasi',
        'lainnya' => 'Lainnya',
    ];

    /**
     * @var array<string, string>
     */
    public const STATUSES = [
        'baru_masuk' => 'Baru Masuk',
        'sedang_dicek' => 'Sedang Dicek',
        'selesai' => 'Selesai',
    ];

    protected $fillable = [
        'ticket_code',
        'booking_id',
        'booking_code',
        'reported_by',
        'customer_name',
        'customer_phone',
        'category',
        'description',
        'status',
        'follow_up_notes',
        'is_escalated',
        'escalation_reason',
        'escalated_at',
        'escalated_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_escalated' => 'boolean',
            'escalated_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function escalatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Lainnya';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? 'Baru Masuk';
    }

    public static function generateTicketCode(): string
    {
        $prefix = 'KDL-'.now()->format('Ymd').'-';

        do {
            $code = $prefix.strtoupper(Str::random(4));
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }
}
