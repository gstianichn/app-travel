<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Refund;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CustomerServiceDashboardController extends Controller
{
    /**
     * Dashboard Customer Service: booking lookup plus today's numbers, all read from the database.
     */
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $today = now()->toDateString();

        $todaysSchedules = Schedule::query()
            ->with(['route.origin', 'route.destination', 'vehicle', 'driver.user'])
            ->whereDate('departure_date', $today)
            ->whereNotIn('status', ['cancelled', 'Cancelled'])
            ->orderBy('departure_time')
            ->get();

        return view('dashboard.customer-service.index', [
            'query' => $query,
            'searchResults' => $query !== '' ? $this->searchBookings($query) : collect(),
            'stats' => [
                'departures_today' => $todaysSchedules->count(),
                'departures_left' => $todaysSchedules->filter(fn (Schedule $schedule) => $schedule->departureCarbon()->isFuture())->count(),
                'pending_payments' => Booking::where('status', 'pending')->where('expired_at', '>', now())->count(),
                'pending_refunds' => Refund::whereIn('status', ['Pending', 'pending', 'Menunggu', 'menunggu', 'Diajukan', 'diajukan'])->count(),
            ],
            'nextDepartures' => $todaysSchedules
                ->filter(fn (Schedule $schedule) => $schedule->departureCarbon()->greaterThanOrEqualTo(now()->subMinutes(30)))
                ->take(8)
                ->values(),
            'pendingBookings' => Booking::query()
                ->with(['user', 'schedule.route.origin', 'schedule.route.destination'])
                ->where('status', 'pending')
                ->where('expired_at', '>', now())
                ->orderBy('expired_at')
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * Find bookings by booking code, ticket number, or the customer's / passenger's name, email or phone.
     *
     * @return Collection<int, Booking>
     */
    private function searchBookings(string $query): Collection
    {
        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return collect();
        }

        $like = '%'.$cleanQuery.'%';

        return Booking::query()
            ->with(['user', 'ticket', 'passengers.seat', 'schedule.route.origin', 'schedule.route.destination', 'schedule.vehicle'])
            ->where(function (Builder $builder) use ($like) {
                $builder->where('booking_code', 'like', $like)
                    ->orWhere('contact_name', 'like', $like)
                    ->orWhere('contact_phone', 'like', $like)
                    ->orWhere('contact_email', 'like', $like)
                    ->orWhereHas('ticket', fn (Builder $ticket) => $ticket->where('ticket_number', 'like', $like))
                    ->orWhereHas('user', fn (Builder $user) => $user->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like))
                    ->orWhereHas('passengers', fn (Builder $passenger) => $passenger->where('name', 'like', $like)
                        ->orWhere('phone', 'like', $like));
            })
            ->latest()
            ->take(20)
            ->get();
    }

    /**
     * Halaman Live Chat
     */
    public function liveChat()
    {
        return view('dashboard.customer-service.live-chat');
    }

    /**
     * Halaman Melihat Konteks Booking
     */
    public function bookingContext()
    {
        return view('dashboard.customer-service.booking-context');
    }

    /**
     * Halaman Melihat Riwayat Chat
     */
    public function chatHistory()
    {
        return view('dashboard.customer-service.chat-history');
    }

    /**
     * Halaman Mengirim Gambar / File & Generator Tiket Instan
     */
    public function sendFile()
    {
        return view('dashboard.customer-service.send-file');
    }

    /**
     * Halaman Melakukan Eskalasi ke Admin & Pelacakan Status
     */
    public function escalation()
    {
        return view('dashboard.customer-service.escalation');
    }
}
