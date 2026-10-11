<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Route;
use App\Models\TripReview;
use App\Models\Vehicle;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $locations = Location::orderBy('name')->get();

        $activeRouteCount = Route::query()
            ->where(fn ($query) => $query->where('is_active', true)->orWhereNull('is_active'))
            ->whereHas('origin')
            ->whereHas('destination')
            ->count();

        $reviewSummary = TripReview::query()
            ->selectRaw('COUNT(*) as total, AVG(overall_rating) as average')
            ->first();

        return view('home-landingpage.index', [
            'locations' => $locations,
            'routeSigns' => Route::cheapestPerCityPair()->take(10)->values(),
            'activeRouteCount' => $activeRouteCount,
            'servedCityCount' => $locations->where('is_active', true)->pluck('city')->filter()->unique()->count(),
            'fleetTypes' => $this->fleetTypes(),
            'reviews' => TripReview::query()
                ->with(['user', 'schedule.route.origin', 'schedule.route.destination'])
                ->whereNotNull('comment')
                ->where('comment', '!=', '')
                ->latest()
                ->take(3)
                ->get(),
            'reviewCount' => (int) ($reviewSummary->total ?? 0),
            'reviewAverage' => $reviewSummary->average !== null ? round((float) $reviewSummary->average, 1) : null,
        ]);
    }

    /**
     * Summarise the fleet per vehicle type.
     *
     * @return Collection<int, array{type: string, units: int, capacity: int, facilities: array<int, string>}>
     */
    private function fleetTypes(): Collection
    {
        return Vehicle::query()
            ->get(['type', 'capacity', 'facilities'])
            ->groupBy(fn (Vehicle $vehicle) => $vehicle->type ?: 'Minibus')
            ->map(fn (Collection $vehicles, string $type) => [
                'type' => $type,
                'units' => $vehicles->count(),
                'capacity' => (int) $vehicles->max('capacity'),
                'facilities' => $vehicles->pluck('facilities')->flatten()->filter()->unique()->take(5)->values()->all(),
            ])
            ->sortBy('capacity')
            ->values();
    }
}
