<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'seat_number',
        'seat_row',
        'seat_column',
        'status',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function bookingPassengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }
}
