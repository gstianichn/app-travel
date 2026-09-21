<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'schedule_id',
        'vehicle_id',
        'driver_id',
        'cleanliness_rating',
        'driver_rating',
        'punctuality_rating',
        'overall_rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'cleanliness_rating' => 'integer',
            'driver_rating' => 'integer',
            'punctuality_rating' => 'integer',
            'overall_rating' => 'decimal:2',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
