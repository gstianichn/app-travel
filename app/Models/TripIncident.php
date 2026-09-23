<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'driver_id',
        'category',
        'severity',
        'description',
        'location_name',
        'latitude',
        'longitude',
        'status',
        'emergency_dispatched',
        'reported_at',
        'resolved_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'emergency_dispatched' => 'boolean',
            'reported_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Label kategori insiden / kendala jalan
     */
    public function categoryLabel(): string
    {
        return match ($this->category) {
            'ban_bocor' => 'Ban Bocor / Kempes',
            'macet_parah' => 'Macet Total / Parah',
            'mesin_mogok' => 'Kendala Mesin / Mogok',
            'penumpang_batal' => 'Penumpang Batal Mendadak',
            'kecelakaan' => 'Insiden Tabrakan / Kecelakaan',
            'darurat_medis' => 'Darurat Medis / Sakit',
            'sos_emergency' => 'EMERGENCY SOS',
            default => 'Kendala Lainnya',
        };
    }

    /**
     * Icon kategori insiden
     */
    public function categoryIcon(): string
    {
        return match ($this->category) {
            'ban_bocor' => '🛞',
            'macet_parah' => '🚦',
            'mesin_mogok' => '⚙️',
            'penumpang_batal' => '👤',
            'kecelakaan' => '💥',
            'darurat_medis' => '🩺',
            'sos_emergency' => '🚨',
            default => '⚠️',
        };
    }

    /**
     * Label tingkat keparahan
     */
    public function severityLabel(): string
    {
        return match ($this->severity) {
            'rendah' => 'Rendah (Pemberitahuan)',
            'tinggi' => 'Tinggi (Butuh Bantuan)',
            'darurat' => 'Darurat (SOS)',
            default => 'Sedang (Perjalanan Terhambat)',
        };
    }

    /**
     * Badge CSS class untuk tingkat keparahan
     */
    public function severityBadgeClass(): string
    {
        return match ($this->severity) {
            'rendah' => 'badge-severity-low',
            'tinggi' => 'badge-severity-high',
            'darurat' => 'badge-severity-emergency',
            default => 'badge-severity-medium',
        };
    }

    /**
     * Label status penanganan
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'in_progress', 'diproses' => 'Sedang Ditangani',
            'resolved', 'selesai' => 'Selesai Ditangani',
            default => 'Telah Dilaporkan',
        };
    }
}
