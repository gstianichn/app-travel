<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'photo',
        'license_number',
        'license_type',
        'license_expired_at',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'address',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'license_expired_at' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Pilihan status kerja pengemudi
     */
    public static function availableStatuses(): array
    {
        return [
            'standby' => 'Siap Jalan (Standby)',
            'on_duty' => 'Sedang Bertugas di Jalan',
            'on_leave' => 'Sedang Libur / Cuti',
        ];
    }

    /**
     * Label teks status kerja bahasa Indonesia
     */
    public function statusLabel(): string
    {
        $status = strtolower($this->status ?? 'standby');
        if ($status === 'available' || $status === 'standby') {
            return 'Siap Jalan (Standby)';
        } elseif ($status === 'on_duty') {
            return 'Sedang Bertugas di Jalan';
        } elseif ($status === 'on_leave' || $status === 'leave') {
            return 'Sedang Libur / Cuti';
        }

        return ucfirst($this->status);
    }

    /**
     * Kelas CSS badge status kerja
     */
    public function statusBadgeClass(): string
    {
        $status = strtolower($this->status ?? 'standby');
        if ($status === 'available' || $status === 'standby') {
            return 'standby';
        } elseif ($status === 'on_duty') {
            return 'onduty';
        } elseif ($status === 'on_leave' || $status === 'leave') {
            return 'onleave';
        }

        return 'standby';
    }

    /**
     * Cek status masa berlaku SIM
     * Mengembalikan status: valid, expiring_soon (<= 30 hari), atau expired
     */
    public function licenseStatus(): array
    {
        if (! $this->license_expired_at) {
            return [
                'status' => 'unknown',
                'label' => 'Belum Ditentukan',
                'badge_class' => 'badge-unknown',
                'days_remaining' => null,
            ];
        }

        $today = Carbon::today();
        $expiredDate = Carbon::parse($this->license_expired_at)->startOfDay();

        if ($expiredDate->isPast()) {
            $daysPast = $expiredDate->diffInDays($today);

            return [
                'status' => 'expired',
                'label' => 'Kedaluwarsa'.($daysPast > 0 ? " ({$daysPast} hari lalu)" : ''),
                'badge_class' => 'badge-expired',
                'days_remaining' => -$daysPast,
            ];
        }

        $daysRemaining = $today->diffInDays($expiredDate);

        if ($daysRemaining <= 30) {
            return [
                'status' => 'expiring_soon',
                'label' => $daysRemaining === 0 ? 'Kedaluwarsa Hari Ini' : "Masa Berlaku Tinggal {$daysRemaining} Hari",
                'badge_class' => 'badge-expiring',
                'days_remaining' => $daysRemaining,
            ];
        }

        return [
            'status' => 'valid',
            'label' => 'Berlaku Aktif',
            'badge_class' => 'badge-valid',
            'days_remaining' => $daysRemaining,
        ];
    }

    /**
     * Total riwayat perjalanan yang pernah dikemudikan
     */
    public function totalTripsCount(): int
    {
        return $this->schedules()->count();
    }

    /**
     * Total perjalanan selesai
     */
    public function completedTripsCount(): int
    {
        return $this->schedules()
            ->whereIn('status', ['completed', 'selesai'])
            ->count();
    }
}
