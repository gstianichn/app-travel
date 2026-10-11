<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_id',
        'destination_id',
        'price',
        'weekend_price',
        'holiday_price',
        'duration',
        'distance_km',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'weekend_price' => 'decimal:2',
            'holiday_price' => 'decimal:2',
            'distance_km' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function activeSchedules(): HasMany
    {
        return $this->hasMany(Schedule::class)
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['Cancelled', 'cancelled', 'Completed', 'completed']);
    }

    public function routeTitle(): string
    {
        $orig = $this->origin?->name ?? 'Asal';
        $dest = $this->destination?->name ?? 'Tujuan';

        return "{$orig} → {$dest}";
    }

    public function formattedPrice(): string
    {
        return 'Rp '.number_format($this->price ?? 0, 0, ',', '.');
    }

    public function formattedWeekendPrice(): string
    {
        $wk = $this->weekend_price ?: $this->price;

        return 'Rp '.number_format($wk ?? 0, 0, ',', '.');
    }

    public function getPriceForDate($date = null): float
    {
        if (! $date) {
            return (float) ($this->price ?? 100000);
        }

        $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);

        // Jika Sabtu (6) atau Minggu (0) dan ada weekend_price
        if ($carbonDate->isWeekend() && ! empty($this->weekend_price) && $this->weekend_price > 0) {
            return (float) $this->weekend_price;
        }

        return (float) ($this->price ?? 100000);
    }

    /**
     * Active routes with both endpoints, cheapest first, keeping one route per city pair
     * (several pools can serve the same two cities).
     *
     * @return Collection<int, Route>
     */
    public static function cheapestPerCityPair(?int $originId = null): Collection
    {
        return static::query()
            ->with(['origin', 'destination'])
            ->where(fn ($query) => $query->where('is_active', true)->orWhereNull('is_active'))
            ->when($originId, fn ($query) => $query->where('origin_id', $originId))
            ->orderBy('price')
            ->get()
            ->filter(fn (Route $route) => $route->origin && $route->destination)
            ->unique(fn (Route $route) => ($route->origin->city ?: $route->origin->name).'|'.($route->destination->city ?: $route->destination->name))
            ->values();
    }
}
