<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\CustomerComplaint;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\TripIncident;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->cs = User::factory()->create([
        'name' => 'CS Operasional',
        'email' => 'cs.ops@travel.test',
        'phone' => '081234560001',
        'role' => 'cs',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin Utama',
        'email' => 'admin.csops@travel.test',
        'phone' => '081234560002',
        'role' => 'admin',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $this->driverUser = User::factory()->create([
        'name' => 'Pak Hendra',
        'email' => 'hendra.driver@travel.test',
        'phone' => '081277778888',
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $this->driver = Driver::create([
        'user_id' => $this->driverUser->id,
        'phone' => '081277778888',
        'license_number' => 'SIM-CS-OPS-01',
        'license_type' => 'SIM B1 Umum',
        'license_expired_at' => now()->addYear(),
        'status' => 'on_duty',
    ]);

    $origin = Location::create(['code' => 'CSO-BDG', 'name' => 'Pool Pasteur', 'city' => 'Bandung', 'is_active' => true]);
    $destination = Location::create(['code' => 'CSO-JKT', 'name' => 'Pool Semanggi', 'city' => 'Jakarta', 'is_active' => true]);
    $this->route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $destination->id, 'price' => 130000, 'duration' => '2 Jam 30 Menit', 'is_active' => true]);
    $this->vehicle = Vehicle::create(['name' => 'HiAce Premio 01', 'type' => 'HiAce Premio', 'plate_number' => 'D 7812 AB', 'capacity' => 10, 'status' => 'beroperasi']);
    $this->seat = Seat::create(['vehicle_id' => $this->vehicle->id, 'seat_number' => '2A', 'seat_row' => '2', 'seat_column' => 1, 'status' => 'Available']);

    $this->schedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->toDateString(),
        'departure_time' => '10:00',
        'available_seats' => 9,
        'status' => 'Dalam Perjalanan',
        'trip_status' => 'departed',
    ]);

    $this->booking = Booking::create([
        'booking_code' => 'TRV-CS-OPS-01',
        'contact_name' => 'Budi Santoso',
        'contact_phone' => '081355556666',
        'schedule_id' => $this->schedule->id,
        'total_passenger' => 1,
        'subtotal' => 130000,
        'discount' => 0,
        'total_price' => 130000,
        'status' => 'paid',
        'source' => 'online',
    ]);

    BookingPassenger::create([
        'booking_id' => $this->booking->id,
        'seat_id' => $this->seat->id,
        'name' => 'Budi Santoso',
        'phone' => '081355556666',
    ]);
});

test('CS dashboard only displays greeting and current month complaint statistics', function () {
    CustomerComplaint::create([
        'ticket_code' => 'KDL-THIS-001',
        'category' => 'barang_tertinggal',
        'description' => 'Kendala bulan ini.',
        'status' => 'baru_masuk',
    ]);

    $oldComplaint = CustomerComplaint::create([
        'ticket_code' => 'KDL-PREV-001',
        'category' => 'sopir_terlambat',
        'description' => 'Kendala bulan lalu.',
        'status' => 'selesai',
    ]);
    $oldComplaint->forceFill(['created_at' => now()->subMonths(2)])->save();

    $response = $this->actingAs($this->cs)->get(route('dashboard.customer-service.index'));

    $response->assertOk();
    $response->assertSee('Halo, CS.');
    $response->assertSee('Statistik Kendala Bulan');
    $response->assertDontSee('Contoh tampilan.');

    $monthlyStats = $response->viewData('monthlyStats');
    expect($monthlyStats['total'])->toBe(1)
        ->and($monthlyStats['baru_masuk'])->toBe(1)
        ->and($monthlyStats['selesai'])->toBe(0);
});

test('CS cek-tiket page displays read-only ticket detail card with route, seat, vehicle plate, and driver', function () {
    $response = $this->actingAs($this->cs)
        ->get(route('dashboard.customer-service.cek-tiket', ['q' => 'TRV-CS-OPS-01']));

    $response->assertOk();
    $response->assertDontSee('Contoh tampilan.');
    $response->assertSee('TRV-CS-OPS-01');
    $response->assertSee('Budi Santoso');
    $response->assertSee('Bandung ke Jakarta');
    $response->assertSee('Kursi 2A');
    $response->assertSee('Lunas');
    $response->assertSee('D 7812 AB');
    $response->assertSee('Pak Hendra');
    $response->assertSee('Read-Only');
});

test('CS laporan-kendala page records complaint, updates status with follow-up notes, and escalates to admin', function () {
    $page = $this->actingAs($this->cs)->get(route('dashboard.customer-service.laporan-kendala', [
        'booking_code' => 'TRV-CS-OPS-01',
    ]));
    $page->assertOk();
    $page->assertDontSee('Contoh tampilan.');
    $page->assertSee('TRV-CS-OPS-01');

    // 1. Store complaint
    $store = $this->actingAs($this->cs)->post(route('dashboard.customer-service.complaints.store'), [
        'booking_code' => 'TRV-CS-OPS-01',
        'category' => 'barang_tertinggal',
        'description' => 'Tas laptop tertinggal di kursi 2A setelah turun di Semanggi.',
    ]);

    $store->assertRedirect(route('dashboard.customer-service.laporan-kendala'));
    $store->assertSessionHas('success');

    $complaint = CustomerComplaint::firstOrFail();
    expect($complaint->booking_id)->toBe($this->booking->id)
        ->and($complaint->customer_name)->toBe('Budi Santoso')
        ->and($complaint->customer_phone)->toBe('081355556666')
        ->and($complaint->status)->toBe('baru_masuk')
        ->and($complaint->is_escalated)->toBeFalse();

    // 2. Update status and follow-up notes
    $update = $this->actingAs($this->cs)->patch(route('dashboard.customer-service.complaints.update', $complaint), [
        'status' => 'sedang_dicek',
        'follow_up_notes' => 'Sudah menghubungi Pak Hendra, tas sedang dicek di kabin.',
    ]);

    $update->assertRedirect();
    $complaint->refresh();
    expect($complaint->status)->toBe('sedang_dicek')
        ->and($complaint->follow_up_notes)->toBe('Sudah menghubungi Pak Hendra, tas sedang dicek di kabin.');

    // 3. Escalate to Admin
    $escalate = $this->actingAs($this->cs)->post(route('dashboard.customer-service.complaints.escalate', $complaint), [
        'escalation_reason' => 'Pelanggan meminta kompensasi / tindak lanjut resmi dari Admin.',
    ]);

    $escalate->assertRedirect();
    $complaint->refresh();
    expect($complaint->is_escalated)->toBeTrue()
        ->and($complaint->escalated_by)->toBe($this->cs->id)
        ->and($complaint->escalated_at)->not->toBeNull();

    // Admin received database notification
    expect($this->admin->fresh()->notifications()->count())->toBeGreaterThanOrEqual(1);

    // 4. Mark as resolved
    $resolve = $this->actingAs($this->cs)->patch(route('dashboard.customer-service.complaints.update', $complaint), [
        'status' => 'selesai',
        'follow_up_notes' => 'Tas sudah dikembalikan ke pelanggan di Pool Semanggi.',
    ]);

    $resolve->assertRedirect();
    $complaint->refresh();
    expect($complaint->status)->toBe('selesai')
        ->and($complaint->resolved_at)->not->toBeNull();
});

test('CS complaint creation validates category and description', function () {
    $response = $this->actingAs($this->cs)->post(route('dashboard.customer-service.complaints.store'), [
        'category' => 'kategori_tidak_valid',
        'description' => 'Hi',
    ]);

    $response->assertSessionHasErrors(['category', 'description']);
});

test('CS pantauan-armada page shows today fleet status, driver incident alert badge, and WhatsApp/phone quick contact', function () {
    TripIncident::create([
        'schedule_id' => $this->schedule->id,
        'driver_id' => $this->driver->id,
        'category' => 'ban_bocor',
        'severity' => 'tinggi',
        'description' => 'Ban belakang bocor di KM 57 Tol Japek.',
        'status' => 'reported',
        'reported_at' => now(),
    ]);

    $response = $this->actingAs($this->cs)->get(route('dashboard.customer-service.pantauan-armada'));

    $response->assertOk();
    $response->assertDontSee('Contoh tampilan.');
    $response->assertSee('Dalam Perjalanan');
    $response->assertSee('D 7812 AB');
    $response->assertSee('Pak Hendra');
    $response->assertSee('Ban Bocor / Insiden');
    $response->assertSee('Ban belakang bocor di KM 57 Tol Japek.');
    $response->assertSee('https://wa.me/6281277778888', false);
    $response->assertSee('tel:081277778888', false);
});

test('non-CS role cannot access CS pages or submit CS complaints', function () {
    $regularUser = User::factory()->create(['role' => 'user', 'status' => 'active']);

    $this->actingAs($regularUser)
        ->get(route('dashboard.customer-service.cek-tiket'))
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->get(route('dashboard.customer-service.profil'))
        ->assertForbidden();

    $this->actingAs($regularUser)
        ->post(route('dashboard.customer-service.complaints.store'), [
            'category' => 'lainnya',
            'description' => 'Coba akses tanpa hak CS.',
        ])
        ->assertForbidden();
});

test('CS can view and update profile information and password', function () {
    $view = $this->actingAs($this->cs)->get(route('dashboard.customer-service.profil'));
    $view->assertOk();
    $view->assertSee('Profil Saya');
    $view->assertSee('Informasi Profil');
    $view->assertSee('Ubah Password');
    $view->assertDontSee('Contoh tampilan.');

    // Update profile
    $update = $this->actingAs($this->cs)->post(route('dashboard.customer-service.profil.update'), [
        'name' => 'Maya Anggraini',
        'email' => 'maya.cs@travel.test',
        'phone' => '081234569999',
    ]);
    $update->assertRedirect(route('dashboard.customer-service.profil'));
    $update->assertSessionHas('success');

    $this->cs->refresh();
    expect($this->cs->name)->toBe('Maya Anggraini')
        ->and($this->cs->email)->toBe('maya.cs@travel.test')
        ->and($this->cs->phone)->toBe('081234569999');

    // Wrong current password fails
    $badPw = $this->actingAs($this->cs)
        ->from(route('dashboard.customer-service.profil'))
        ->post(route('dashboard.customer-service.profil.password'), [
            'current_password' => 'salah-password',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);
    $badPw->assertSessionHasErrors('current_password');

    // Valid password update succeeds
    $goodPw = $this->actingAs($this->cs)->post(route('dashboard.customer-service.profil.password'), [
        'current_password' => 'password',
        'password' => 'passwordbaru123',
        'password_confirmation' => 'passwordbaru123',
    ]);
    $goodPw->assertRedirect(route('dashboard.customer-service.profil'));
    $goodPw->assertSessionHas('success');

    expect(Hash::check('passwordbaru123', $this->cs->fresh()->password))->toBeTrue();
});
