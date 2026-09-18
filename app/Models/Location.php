<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'city',
        'address',
        'phone',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function originRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'origin_id');
    }

    public function destinationRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_id');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'shuttle' => 'Shuttle Point',
            'drop_point' => 'Drop Point',
            default => 'Pool Keberangkatan',
        };
    }

    public function typeBadgeClass(): string
    {
        return match ($this->type) {
            'shuttle' => 'badge-type-shuttle',
            'drop_point' => 'badge-type-droppoint',
            default => 'badge-type-pool',
        };
    }
}
