<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'original_amount',
        'amount',
        'cancellation_fee',
        'refund_percentage',
        'reason',
        'bank_name',
        'account_number',
        'account_holder',
        'transfer_proof',
        'admin_note',
        'status',
        'processed_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'original_amount' => 'decimal:2',
            'amount' => 'decimal:2',
            'cancellation_fee' => 'decimal:2',
            'refund_percentage' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Format nominal asli tiket ke mata uang Rupiah.
     */
    public function formattedOriginalAmount(): string
    {
        $val = $this->original_amount ?? $this->booking?->total_price ?? 0;

        return 'Rp '.number_format($val, 0, ',', '.');
    }

    /**
     * Format potongan biaya administrasi (cancellation fee) ke Rupiah.
     */
    public function formattedCancellationFee(): string
    {
        return 'Rp '.number_format($this->cancellation_fee ?? 0, 0, ',', '.');
    }

    /**
     * Format nominal bersih yang wajib ditransfer balik ke Rupiah.
     */
    public function formattedNetAmount(): string
    {
        return 'Rp '.number_format($this->amount ?? 0, 0, ',', '.');
    }

    /**
     * Label status permohonan refund.
     */
    public function statusLabel(): string
    {
        $status = strtolower($this->status ?? 'menunggu');

        return match ($status) {
            'disetujui', 'approved', 'success', 'selesai' => 'Disetujui',
            'ditolak', 'rejected', 'gagal' => 'Ditolak',
            default => 'Menunggu Persetujuan',
        };
    }

    /**
     * CSS class badge untuk status refund.
     */
    public function statusBadgeClass(): string
    {
        $status = strtolower($this->status ?? 'menunggu');

        return match ($status) {
            'disetujui', 'approved', 'success', 'selesai' => 'badge-refund-approved',
            'ditolak', 'rejected', 'gagal' => 'badge-refund-rejected',
            default => 'badge-refund-pending',
        };
    }

    /**
     * URL pratinjau bukti transfer pengembalian dana kantor.
     */
    public function transferProofUrl(): ?string
    {
        if (! $this->transfer_proof) {
            return null;
        }

        if (str_starts_with($this->transfer_proof, 'http')) {
            return $this->transfer_proof;
        }

        return Storage::url($this->transfer_proof);
    }
}
