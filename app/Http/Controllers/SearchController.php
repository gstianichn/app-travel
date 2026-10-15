<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        $searchPerformed = $request->hasAny([
            'origin',
            'destination',
            'departure_date',
        ]);

        $schedules = collect();
        $routeConnected = false;
        $originLoc = null;
        $destLoc = null;

        if ($searchPerformed) {
            $validated = $request->validate([
                'origin' => [
                    'required',
                    'integer',
                    'exists:locations,id',
                ],

                'destination' => [
                    'required',
                    'integer',
                    'exists:locations,id',
                    'different:origin',
                ],

                'departure_date' => [
                    'required',
                    'date',
                    'after_or_equal:today',
                ],
            ]);

            $originLoc = Location::find($validated['origin']);
            $destLoc = Location::find($validated['destination']);

            // Cek apakah rute antara kedua kota terhubung ke jaringan shuttle aktif
            $route = Route::with(['origin', 'destination'])
                ->where('origin_id', $validated['origin'])
                ->where('destination_id', $validated['destination'])
                ->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                })
                ->first();

            $routeConnected = ($route !== null);
            $transitHub = null;

            // Jika rute terhubung, pastikan jadwal harian tersedia
            if ($routeConnected) {
                Schedule::ensureSchedulesExistForDateAndRoute($route, $validated['departure_date']);
            } elseif ($originLoc && $destLoc) {
                // Temukan kota transit hub yang menghubungkan origin -> hub dan hub -> dest
                $originConnectedDestIds = Route::where('origin_id', $originLoc->id)
                    ->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    })
                    ->pluck('destination_id');

                $destConnectedOriginIds = Route::where('destination_id', $destLoc->id)
                    ->where(function ($q) {
                        $q->where('is_active', true)->orWhereNull('is_active');
                    })
                    ->pluck('origin_id');

                $hubIds = $originConnectedDestIds->intersect($destConnectedOriginIds);
                if ($hubIds->isNotEmpty()) {
                    $transitHub = Location::find($hubIds->first());
                }
            }

            $schedules = Schedule::query()
                ->with([
                    'route.origin',
                    'route.destination',
                    'vehicle',
                    'driver.user',
                ])
                ->whereDate(
                    'departure_date',
                    $validated['departure_date']
                )
                ->where('available_seats', '>=', 1)
                ->whereNotIn('status', ['cancelled', 'Cancelled', 'completed', 'Completed'])
                // Departures that already left today cannot be booked any more.
                ->when(
                    $validated['departure_date'] === now()->toDateString(),
                    fn ($query) => $query->where('departure_time', '>', now()->format('H:i')),
                )
                ->whereHas('route', function ($query) use ($validated) {
                    $query
                        ->where('origin_id', $validated['origin'])
                        ->where('destination_id', $validated['destination']);
                })
                ->orderBy('departure_time')
                ->get();
        } else {
            $transitHub = null;
        }

        return view('search.index', [
            'locations' => $locations,
            'schedules' => $schedules,
            'searchPerformed' => $searchPerformed,
            'routeConnected' => $routeConnected,
            'originLoc' => $originLoc,
            'destLoc' => $destLoc,
            'transitHub' => $transitHub,
            'routeSuggestions' => Route::cheapestPerCityPair(
                $originLoc && ! $routeConnected ? $originLoc->id : null
            )->take(8),
        ]);
    }
}
