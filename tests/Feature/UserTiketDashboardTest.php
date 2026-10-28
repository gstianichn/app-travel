<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'user',
        'password' => Hash::make('password'),
    ]);

    $origin = Location::create([
        'code' => 'BDO-TKTD',
        'name' => 'Pool Dipatiukur (Bandung)',
        'city' => 'Bandung',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $destination = Location::create([
        'code' => 'JKT-TKTD',
        'name' => 'Semanggi (Jakarta)',
        'city' => 'Jakarta',
        'type' => 'pool',
        'is_active' => true,
    ]);

    $this->route = Route::create([
        'origin_id' => $origin->id,
        'destination_id' => $destination->id,
        'price' => 125000,
        'duration' => '2 Jam 45 Menit',
        'is_active' => true,
    ]);

    $this->vehicle = Vehicle::create([
        'plate_number' => 'B 4242 TKT',
        'name' => 'HiAce Tiket Dashboard',
        'type' => 'Toyota HiAce Commuter',
        'capacity' => 10,
        'status' => 'siap_jalan',
    ]);
    $this->vehicle->ensureSeatsExist();

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => Carbon::tomorrow()->toDateString(),
        'departure_time' => '09:30',
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);
});

function createUserTiketBooking(User $user, Schedule $schedule, string $status, string $bookingCode, int $totalPassenger = 1): Booking
{
    return Booking::create([
        'booking_code' => $bookingCode,
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'total_passenger' => $totalPassenger,
        'subtotal' => 125000 * $totalPassenger,
        'discount' => 0,
        'total_price' => 125000 * $totalPassenger,
        'status' => $status,
        'expired_at' => Carbon::now()->addMinutes(15),
    ]);
}

function createUserTiketTicket(Booking $booking, string $ticketNumber, string $qrCode): Ticket
{
    return Ticket::create([
        'booking_id' => $booking->id,
        'ticket_number' => $ticketNumber,
        'qr_code' => $qrCode,
        'status' => 'Valid',
    ]);
}

test('daftar tiket aktif menampilkan tiket lunas yang akan berangkat beserta QR', function () {
    $booking = createUserTiketBooking($this->user, $this->schedule, 'confirmed', 'TRV-TKT-ACT');
    $ticket = createUserTiketTicket($booking, 'TKT-ACT-0001', 'QR-TKT-ACT-0001');

    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-tiket'));

    $response->assertOk()
        ->assertSee('Akan berangkat')
        ->assertSee($ticket->ticket_number)
        ->assertSee($booking->booking_code)
        ->assertSee('data-qr="'.$ticket->qr_code.'"', false)
        ->assertSee('aria-label="QR e-ticket '.$booking->booking_code.'"', false)
        ->assertViewHas('activeBookings', fn ($items) => $items->contains('id', $booking->id))
        ->assertViewHas('historyBookings', fn ($items) => $items->doesntContain('id', $booking->id));
});

test('riwayat otomatis memindahkan tiket dengan keberangkatan lebih dari satu jam lalu ke completed', function () {
    $departedAt = Carbon::now()->subHours(2);

    $pastSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'departure_date' => $departedAt->toDateString(),
        'departure_time' => $departedAt->format('H:i'),
        'price' => 125000,
        'available_seats' => 10,
        'status' => 'Scheduled',
    ]);

    $booking = createUserTiketBooking($this->user, $pastSchedule, 'paid', 'TRV-TKT-HIS');
    $ticket = createUserTiketTicket($booking, 'TKT-HIS-0001', 'QR-TKT-HIS-0001');

    $response = $this->actingAs($this->user)->get(route('dashboard.user.user-tiket'));

    $response->assertOk()
        ->assertSee('Riwayat');

    expect($booking->fresh()->status)->toBe('completed');
    expect($ticket->fresh()->status)->toBe('completed');

    $response
        ->assertViewHas('activeBookings', fn ($items) => $items->doesntContain('id', $booking->id))
        ->assertViewHas('historyBookings', fn ($items) => $items->contains('id', $booking->id));
});

test('check-in mandiri menandai tiket boarded dan semua penumpang boarded', function () {
    $booking = createUserTiketBooking($this->user, $this->schedule, 'confirmed', 'TRV-TKT-CHK', 2);
    $ticket = createUserTiketTicket($booking, 'TKT-CHK-0001', 'QR-TKT-CHK-0001');

    $passengers = $this->vehicle->seats()->take(2)->get()->values()->map(
        fn ($seat, $index) => BookingPassenger::create([
            'booking_id' => $booking->id,
            'seat_id' => $seat->id,
            'name' => 'Penumpang Check-in '.($index + 1),
            'boarding_status' => 'belum_hadir',
        ])
    );

    $page = $this->actingAs($this->user)->get(route('dashboard.user.user-tiket'));

    $page->assertOk()
        ->assertSee('Mode uji: tandai sudah naik');

    $this->actingAs($this->user)
        ->post(route('dashboard.user.tiket.check-in', $ticket))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($ticket->fresh()->status)->toBe('Boarded');
    expect($ticket->fresh()->boarded_at)->not->toBeNull();
    expect($booking->fresh()->status)->toBe('checked_in');

    $passengers->each(function (BookingPassenger $passenger) {
        expect($passenger->fresh()->boarding_status)->toBe('boarded');
    });
});
