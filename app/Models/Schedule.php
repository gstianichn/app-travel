<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'departure_date',
        'departure_time',
        'available_seats',
        'travel_route_id',
        'vehicle_id',
        'driver_id',
        'departure_at',
        'arrival_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'departure_time' => 'datetime:H:i',
            'departure_at' => 'datetime',
            'arrival_at' => 'datetime',
        ];
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
