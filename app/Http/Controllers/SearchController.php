<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Schedule;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::orderBy('name')->get();

        $searchPerformed = $request->hasAny([
            'origin',
            'destination',
            'departure_date',
            'passengers',
        ]);

        $schedules = collect();

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

                'passengers' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:10',
                ],
            ]);

            $schedules = Schedule::query()
                ->with([
                    'route.origin',
                    'route.destination',
                ])
                ->whereDate(
                    'departure_date',
                    $validated['departure_date']
                )
                ->where(
                    'available_seats',
                    '>=',
                    $validated['passengers']
                )
                ->whereHas('route', function ($query) use ($validated) {
                    $query
                        ->where('origin_id', $validated['origin'])
                        ->where('destination_id', $validated['destination']);
                })
                ->orderBy('departure_time')
                ->get();
        }

        return view('search.index', [
            'locations' => $locations,
            'schedules' => $schedules,
            'searchPerformed' => $searchPerformed,
        ]);
    }
}
