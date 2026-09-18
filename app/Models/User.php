<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'access_code',
        'acces_code',
        'branch',
        'position',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    /**
     * Daftar hak akses standar untuk staf loket & operasional
     */
    public static function availablePermissions(): array
    {
        return [
            'create_booking' => [
                'label' => 'Input Pesanan / Tiket',
                'description' => 'Membuat transaksi pemesanan tiket perjalanan pelanggan di loket',
                'restricted' => false,
            ],
            'view_routes' => [
                'label' => 'Melihat Rute & Jadwal',
                'description' => 'Melihat daftar rute perjalanan, jam keberangkatan, dan tarif travel',
                'restricted' => false,
            ],
            'view_passengers' => [
                'label' => 'Melihat Data Penumpang & Kursi',
                'description' => 'Melihat daftar manifest penumpang dan status denah kursi armada',
                'restricted' => false,
            ],
            'checkin_ticket' => [
                'label' => 'Check-in / Boarding Penumpang',
                'description' => 'Melakukan pemindaian tiket dan verifikasi penumpang sebelum jalan',
                'restricted' => false,
            ],
            'view_reports' => [
                'label' => 'Melihat Laporan Kasir / Loket',
                'description' => 'Melihat rekap pendapatan dan penjualan tiket pada cabang/loket terkait',
                'restricted' => false,
            ],
            'approve_refund' => [
                'label' => 'Menyetujui Refund',
                'description' => 'Dibatasi: Otorisasi persetujuan pengembalian dana tiket pelanggan',
                'restricted' => true,
            ],
            'delete_data' => [
                'label' => 'Menghapus Data',
                'description' => 'Dibatasi: Menghapus data transaksi, rute, atau akun pengguna',
                'restricted' => true,
            ],
        ];
    }

    /**
     * Daftar cabang / loket penempatan
     */
    public static function defaultBranches(): array
    {
        return [
            'Pool Jakarta (Pusat)',
            'Loket Gambir Jakarta',
            'Pool Bandung (Pasteur)',
            'Agen Bandung (Dipatiukur)',
            'Pool Cianjur',
            'Agen Sukabumi',
            'Agen Cimahi',
        ];
    }

    /**
     * Cek apakah staf memiliki hak akses tertentu
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'admin_utama' || $this->role === 'admin') {
            return true;
        }

        $perms = $this->permissions ?? [];

        return in_array($permission, $perms, true);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin_staff', 'staf']);
    }

    public function isAdminStaff(): bool
    {
        return $this->role === 'admin_staff';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isCustomerService(): bool
    {
        return $this->role === 'customer_service';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
