<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'acces_code',
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
        ];
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