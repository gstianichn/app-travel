<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'name',
        'type',
        'capacity',
        'photo',
        'facilities',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'capacity' => 'integer',
        ];
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
