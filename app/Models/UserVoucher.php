<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'title',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_transaction',
        'applicable_route',
        'source',
        'status',
        'used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_transaction' => 'decimal:2',
            'used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cek apakah voucher masih aktif dan belum kedaluwarsa.
     */
    public function isActive(): bool
    {
        if ($this->status === 'used') {
            return false;
        }

        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        if ($this->status === 'used') {
            return false;
        }

        return $this->status === 'expired' || ($this->expires_at && Carbon::now()->gt($this->expires_at));
    }

    public function isUsed(): bool
    {
        return $this->status === 'used';
    }

    /**
     * Teks nominal diskon.
     */
    public function discountText(): string
    {
        if ($this->discount_type === 'percent') {
            $text = 'Diskon '.(int) $this->discount_value.'%';
            if ($this->max_discount && $this->max_discount > 0) {
                $text .= ' (Maks. Rp '.number_format($this->max_discount, 0, ',', '.').')';
            }

            return $text;
        }

        return 'Potongan Rp '.number_format($this->discount_value, 0, ',', '.');
    }

    /**
     * Teks syarat minimum transaksi.
     */
    public function minOrderText(): string
    {
        if ($this->min_transaction > 0) {
            return 'Min. order Rp '.number_format($this->min_transaction, 0, ',', '.');
        }

        return 'Tanpa minimum belanja';
    }

    /**
     * Status tag metadata untuk UI.
     */
    public function statusInfo(): array
    {
        if ($this->isUsed()) {
            return [
                'label' => 'Sudah Terpakai',
                'color' => 'slate',
                'badge_class' => 'bg-slate-100 text-slate-600 border-slate-200',
            ];
        }

        if ($this->isExpired()) {
            return [
                'label' => 'Kedaluwarsa',
                'color' => 'rose',
                'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200',
            ];
        }

        return [
            'label' => 'Masih Berlaku',
            'color' => 'emerald',
            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ];
    }
}
