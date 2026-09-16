<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TravelRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'destination',
        'slug',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
