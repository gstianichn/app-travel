<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'order_id',
        'transaction_id',
        'payment_method',
        'proof_image',
        'bank_name',
        'account_name',
        'account_number',
        'notes',
        'verified_by',
        'verified_at',
        'gateway_provider',
        'gateway_status',
        'gross_amount',
        'status',
        'paid_at',
        'expired_at',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'verified_at' => 'datetime',
            'raw_response' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function statusLabel(): string
    {
        $st = strtolower($this->status ?? 'pending');

        if (in_array($st, ['success', 'paid', 'lunas'])) {
            return 'Sudah Lunas';
        }

        if (in_array($st, ['pending', 'menunggu'])) {
            if ($this->isManualTransfer() && $this->hasProof()) {
                return 'Menunggu Verifikasi';
            }

            return 'Menunggu Pembayaran';
        }

        if (in_array($st, ['expired', 'kedaluwarsa'])) {
            return 'Kedaluwarsa';
        }

        if (in_array($st, ['failed', 'gagal', 'ditolak', 'cancel', 'cancelled'])) {
            return 'Gagal / Ditolak';
        }

        return ucfirst($this->status ?? 'Pending');
    }

    public function statusBadgeClass(): string
    {
        $st = strtolower($this->status ?? 'pending');

        if (in_array($st, ['success', 'paid', 'lunas'])) {
            return 'badge-status-paid';
        }

        if (in_array($st, ['pending', 'menunggu'])) {
            return 'badge-status-pending';
        }

        if (in_array($st, ['expired', 'kedaluwarsa'])) {
            return 'badge-status-expired';
        }

        return 'badge-status-failed';
    }

    public function methodLabel(): string
    {
        $method = $this->payment_method;

        if (stripos($method, 'cash') !== false || stripos($method, 'tunai') !== false) {
            return 'Tunai di Loket';
        }

        if (stripos($method, 'qris') !== false) {
            return 'QRIS Dinamis';
        }

        if (stripos($method, 'transfer') !== false || stripos($method, 'bank') !== false) {
            return $this->bank_name ? "Transfer {$this->bank_name}" : 'Transfer Bank';
        }

        if (stripos($method, 'midtrans') !== false || $this->gateway_provider === 'midtrans') {
            return 'Midtrans Gateway';
        }

        if (stripos($method, 'xendit') !== false || $this->gateway_provider === 'xendit') {
            return 'Xendit Gateway';
        }

        return $method ?: 'Transfer Bank';
    }

    public function methodBadgeClass(): string
    {
        $method = strtolower($this->payment_method ?? '');

        if (str_contains($method, 'cash') || str_contains($method, 'tunai')) {
            return 'badge-method-cash';
        }

        if (str_contains($method, 'qris')) {
            return 'badge-method-qris';
        }

        if (str_contains($method, 'transfer') || str_contains($method, 'bank')) {
            return 'badge-method-transfer';
        }

        return 'badge-method-gateway';
    }

    public function formattedAmount(): string
    {
        return 'Rp '.number_format($this->gross_amount ?? 0, 0, ',', '.');
    }

    public function isManualTransfer(): bool
    {
        $method = strtolower($this->payment_method ?? '');

        return str_contains($method, 'transfer')
            || str_contains($method, 'bank')
            || str_contains($method, 'rekening')
            || $this->gateway_provider === 'manual';
    }

    public function hasProof(): bool
    {
        return ! empty($this->proof_image);
    }

    public function hasGatewayLog(): bool
    {
        return ! empty($this->raw_response) || ! empty($this->gateway_status);
    }
}
