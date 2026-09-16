<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'schedule_id',
        'total_passenger',
        'subtotal',
        'discount',
        'total_price',
        'status',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'expired_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
