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
        $locations = Location::where('is_active', true)->orderBy('city')->orderBy('name')->get();

        $activeRouteCount = Route::query()
            ->where(fn ($query) => $query->where('is_active', true)->orWhereNull('is_active'))
            ->whereHas('origin', fn ($query) => $query->where('is_active', true)->where('type', '!=', 'drop_point'))
            ->whereHas('destination', fn ($query) => $query->where('is_active', true))
            ->count();

        $reviewSummary = TripReview::query()
            ->selectRaw('COUNT(*) as total, AVG(overall_rating) as average')
            ->first();

        $scheduleRoutes = Route::cheapestPerCityPair()->values();
        $routeSigns = $scheduleRoutes->take(10)->values();

        return view('home-landingpage.index', [
            'locations' => $locations,
            'routeSigns' => $routeSigns,
            'scheduleRoutes' => $scheduleRoutes,
            'routesByOriginCity' => $scheduleRoutes->groupBy(fn (Route $route) => $route->origin?->city ?: $route->origin?->name ?: 'Lainnya'),
            'activeRouteCount' => $activeRouteCount,
            'servedCityCount' => $locations->where('is_active', true)->pluck('city')->filter()->unique()->count(),
            'fleetTypes' => $this->fleetTypes(),
            'reviews' => TripReview::query()
                ->with(['user', 'schedule.route.origin', 'schedule.route.destination'])
                ->whereNotNull('comment')
                ->where('comment', '!=', '')
                ->latest()
                ->take(6)
                ->get(),
            'reviewCount' => (int) ($reviewSummary->total ?? 0),
            'reviewAverage' => $reviewSummary->average !== null ? round((float) $reviewSummary->average, 1) : null,
        ]);
    }

    /**
     * Summarise the fleet per vehicle type, restricted to medium (Toyota HiAce Premio, 10 seats)
     * and large (Toyota HiAce Commuter, 14 seats) capacity.
     *
     * @return Collection<int, array{type: string, units: int, capacity: int, category: string, facilities: array<int, string>, seatRows: Collection}>
     */
    private function fleetTypes(): Collection
    {
        return Vehicle::query()
            ->where('capacity', '>=', 10)
            ->whereIn('type', ['Toyota HiAce Premio', 'Toyota HiAce Commuter'])
            ->get()
            ->groupBy(fn (Vehicle $vehicle) => $vehicle->type ?: 'Minibus')
            ->map(function (Collection $vehicles, string $type) {
                /** @var Vehicle $sampleVehicle */
                $sampleVehicle = $vehicles->first();
                $sampleVehicle->ensureSeatsExist();
                $seatRows = $sampleVehicle->seats()->orderBy('seat_row')->orderBy('seat_column')->get()->groupBy('seat_row');

                $capacity = (int) $vehicles->max('capacity');
                $category = $capacity <= 10 ? 'Kapasitas Sedang' : 'Kapasitas Besar';

                return [
                    'type' => $type,
                    'units' => $vehicles->count(),
                    'capacity' => $capacity,
                    'category' => $category,
                    'facilities' => $vehicles->pluck('facilities')->flatten()->filter()->unique()->take(5)->values()->all(),
                    'seatRows' => $seatRows,
                ];
            })
            ->sortBy('capacity')
            ->values();
    }
}
