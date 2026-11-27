<?php

use App\Models\Booking;
use App\Models\BookingPassenger;
use App\Models\CsConversation;
use App\Models\CsMessage;
use App\Models\CustomerComplaint;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Route as TravelRoute;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->cs = User::factory()->create([
        'name' => 'Maya Anggraini',
        'email' => 'maya.livechat@travel.test',
        'role' => 'cs',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $this->admin = User::factory()->create([
        'name' => 'Admin Utama',
        'email' => 'admin.livechat@travel.test',
        'role' => 'admin',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $this->driverUser = User::factory()->create([
        'name' => 'Pak Yayan',
        'email' => 'yayan.livechat@travel.test',
        'phone' => '081277771111',
        'role' => 'driver',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $this->driver = Driver::create([
        'user_id' => $this->driverUser->id,
        'phone' => '081277771111',
        'license_number' => 'SIM-LC-01',
        'license_expired_at' => now()->addYear(),
        'status' => 'on_duty',
    ]);

    $origin = Location::create(['code' => 'LC-BDG', 'name' => 'Pool Pasteur', 'city' => 'Bandung', 'is_active' => true]);
    $destination = Location::create(['code' => 'LC-JKT', 'name' => 'Pool Semanggi', 'city' => 'Jakarta', 'is_active' => true]);
    $this->route = TravelRoute::create(['origin_id' => $origin->id, 'destination_id' => $destination->id, 'price' => 130000, 'duration' => '2 Jam 30 Menit', 'is_active' => true]);
    $this->vehicle = Vehicle::create(['name' => 'HiAce Executive', 'type' => 'HiAce Premio', 'plate_number' => 'D 1234 LC', 'capacity' => 10, 'status' => 'beroperasi']);
    $this->seat1 = Seat::create(['vehicle_id' => $this->vehicle->id, 'seat_number' => '1A', 'seat_row' => '1', 'seat_column' => 1, 'status' => 'Available']);
    $this->seat2 = Seat::create(['vehicle_id' => $this->vehicle->id, 'seat_number' => '1B', 'seat_row' => '1', 'seat_column' => 2, 'status' => 'Available']);

    // Urgent schedule (< 2 hours)
    $urgentTime = now()->addMinutes(45);
    $this->urgentSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => $urgentTime->toDateString(),
        'departure_time' => $urgentTime->format('H:i'),
        'available_seats' => 9,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    // Later schedule (tomorrow)
    $this->laterSchedule = Schedule::create([
        'route_id' => $this->route->id,
        'vehicle_id' => $this->vehicle->id,
        'driver_id' => $this->driver->id,
        'departure_date' => now()->addDay()->toDateString(),
        'departure_time' => '10:00',
        'available_seats' => 9,
        'status' => 'Scheduled',
        'trip_status' => 'scheduled',
    ]);

    $this->urgentBooking = Booking::create([
        'booking_code' => 'TRV-LC-URGENT',
        'contact_name' => 'Rina Kusuma',
        'contact_phone' => '081312340001',
        'schedule_id' => $this->urgentSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 130000,
        'discount' => 0,
        'total_price' => 130000,
        'status' => 'paid',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $this->urgentBooking->id,
        'seat_id' => $this->seat1->id,
        'name' => 'Rina Kusuma',
        'phone' => '081312340001',
    ]);
    Ticket::create([
        'booking_id' => $this->urgentBooking->id,
        'ticket_number' => 'TKT-LC-URGENT',
        'qr_code' => 'QR-LC-URGENT',
        'status' => 'active',
    ]);

    $this->laterBooking = Booking::create([
        'booking_code' => 'TRV-LC-LATER',
        'contact_name' => 'Dedi Pratama',
        'contact_phone' => '081312340002',
        'schedule_id' => $this->laterSchedule->id,
        'total_passenger' => 1,
        'subtotal' => 130000,
        'discount' => 0,
        'total_price' => 130000,
        'status' => 'paid',
        'source' => 'online',
    ]);
    BookingPassenger::create([
        'booking_id' => $this->laterBooking->id,
        'seat_id' => $this->seat2->id,
        'name' => 'Dedi Pratama',
        'phone' => '081312340002',
    ]);
});

test('live chat orders queue by departure urgency, displays SLA timer and automatic booking context with driver contact', function () {
    // Create later conversation first, urgent conversation second
    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.store'), [
        'booking_code' => 'TRV-LC-LATER',
        'initial_message' => 'Halo, saya berangkat besok, mau tanya bagasi.',
    ])->assertRedirect();

    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.store'), [
        'booking_code' => 'TRV-LC-URGENT',
        'initial_message' => 'Saya sudah di pool, posisi mobil di mana ya?',
    ])->assertRedirect();

    $response = $this->actingAs($this->cs)->get(route('dashboard.customer-service.live-chat'));
    $response->assertOk();
    $response->assertDontSee('Contoh tampilan.');

    $conversations = $response->viewData('conversations');
    expect($conversations->first()->booking->booking_code)->toBe('TRV-LC-URGENT');

    // Booking context & driver contact + SLA timer + quick templates
    $response->assertSee('TRV-LC-URGENT');
    $response->assertSee('Rina Kusuma');
    $response->assertSee('Berangkat < 2 Jam');
    $response->assertSee('data-sla-anchor', false);
    $response->assertSee('Kursi 1A');
    $response->assertSee('D 1234 LC');
    $response->assertSee('Pak Yayan');
    $response->assertSee('https://wa.me/6281277771111', false);
    $response->assertSee('Konfirmasi Jadwal &amp; Kursi', false);
    $response->assertSee('Kirim E-Tiket (1-Klik)');
});

test('CS can reply, send one-click e-ticket, resolve, and escalate refund/lost item/breakdown cases to admin', function () {
    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.store'), [
        'booking_code' => 'TRV-LC-URGENT',
        'initial_message' => 'Tolong kirim ulang e-tiket dan saya ada barang tertinggal.',
    ]);

    $conversation = CsConversation::firstOrFail();

    // 1. Send 1-click e-ticket
    $this->actingAs($this->cs)
        ->post(route('dashboard.customer-service.live-chat.send-eticket', $conversation))
        ->assertRedirect();

    expect(CsMessage::where('conversation_id', $conversation->id)->where('message_type', 'e_ticket')->exists())->toBeTrue();
    expect($conversation->fresh()->status)->toBe('assigned');

    // 2. Reply to customer
    $this->actingAs($this->cs)
        ->post(route('dashboard.customer-service.live-chat.reply', $conversation), [
            'body' => 'E-tiket sudah saya kirim ya Kak, mohon dicek.',
        ])
        ->assertRedirect();

    // 3. Escalate to admin
    $adminUtama = User::factory()->create([
        'name' => 'Admin Utama Real',
        'email' => 'admin.utama.esc@travel.test',
        'role' => 'admin_utama',
        'status' => 'active',
    ]);

    $this->actingAs($this->cs)
        ->post(route('dashboard.customer-service.live-chat.escalate', $conversation), [
            'escalation_type' => 'barang_hilang',
            'escalation_reason' => 'Dompet pelanggan tertinggal di kursi 1A armada D 1234 LC.',
        ])
        ->assertRedirect();

    $complaint = CustomerComplaint::where('booking_id', $this->urgentBooking->id)->where('is_escalated', true)->firstOrFail();
    expect(CsMessage::where('conversation_id', $conversation->id)->where('message_type', 'escalation')->exists())->toBeTrue();
    expect($this->admin->fresh()->notifications()->count())->toBeGreaterThanOrEqual(1);
    expect($adminUtama->fresh()->notifications()->count())->toBeGreaterThanOrEqual(1);

    // Admin Utama sees escalated ticket in /admin/eskalasi and can resolve it
    $adminPage = $this->actingAs($adminUtama)->get(route('dashboard.admin.eskalasi.index'));
    $adminPage->assertOk();
    $adminPage->assertSee($complaint->ticket_code);
    $adminPage->assertSee('Dompet pelanggan tertinggal di kursi 1A armada D 1234 LC.');

    $this->actingAs($adminUtama)
        ->patch(route('dashboard.admin.eskalasi.update', $complaint), [
            'status' => 'selesai',
            'follow_up_notes' => 'Dompet sudah diamankan di Pool Semanggi.',
        ])
        ->assertRedirect();

    expect($complaint->fresh()->status)->toBe('selesai')
        ->and($complaint->fresh()->follow_up_notes)->toBe('Dompet sudah diamankan di Pool Semanggi.');

    // 4. Resolve conversation
    $this->actingAs($this->cs)
        ->patch(route('dashboard.customer-service.live-chat.status', $conversation), ['action' => 'resolve'])
        ->assertRedirect();

    expect($conversation->fresh()->status)->toBe('resolved');
});

test('regular user sees CS icon in dashboard, starts chat linked to booking, receives CS reply and e-ticket without seeing internal notes', function () {
    $passenger = User::factory()->create([
        'name' => 'Andi Penumpang',
        'email' => 'andi.user@travel.test',
        'phone' => '081299997777',
        'role' => 'user',
        'status' => 'active',
        'password' => Hash::make('password'),
    ]);

    $otherUser = User::factory()->create([
        'role' => 'user',
        'status' => 'active',
    ]);

    $this->urgentBooking->update(['user_id' => $passenger->id]);

    // 1. User sees CS icon & link on dashboard
    $dash = $this->actingAs($passenger)->get(route('dashboard.user.index'));
    $dash->assertOk();
    $dash->assertSee(route('dashboard.user.bantuan-cs'), false);
    $dash->assertSee('Customer Service');

    // 2. User starts a conversation linked to their booking
    $start = $this->actingAs($passenger)->post(route('dashboard.user.bantuan-cs.store'), [
        'booking_id' => $this->urgentBooking->id,
        'subject' => 'Minta kirim ulang e-tiket',
        'message' => 'Halo CS, tolong bantu kirim e-tiket saya ya.',
    ]);
    $start->assertRedirect();

    $conversation = CsConversation::where('user_id', $passenger->id)->firstOrFail();
    expect($conversation->booking_id)->toBe($this->urgentBooking->id);

    // 3. CS sends an internal note, a public reply, and a 1-click e-ticket
    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.reply', $conversation), [
        'body' => 'RAHASIA INTERNAL CS: cek status pembayaran dulu.',
        'is_internal_note' => '1',
    ])->assertRedirect();

    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.reply', $conversation), [
        'body' => 'Siap Kak Andi, ini e-tiket resminya ya.',
    ])->assertRedirect();

    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.send-eticket', $conversation))
        ->assertRedirect();

    // 4. User sees CS reply and E-Ticket card, but NOT internal note
    $userChat = $this->actingAs($passenger)->get(route('dashboard.user.bantuan-cs', ['conversation' => $conversation->id]));
    $userChat->assertOk();
    $userChat->assertSee('Siap Kak Andi, ini e-tiket resminya ya.');
    $userChat->assertSee('E-Tiket Resmi TravelKu');
    $userChat->assertSee('TKT-LC-URGENT');
    $userChat->assertDontSee('RAHASIA INTERNAL CS');

    // 5. User can send follow-up reply, while other users are forbidden
    $this->actingAs($passenger)->post(route('dashboard.user.bantuan-cs.reply', $conversation), [
        'message' => 'Terima kasih banyak CS!',
    ])->assertRedirect();

    $this->actingAs($otherUser)->post(route('dashboard.user.bantuan-cs.reply', $conversation), [
        'message' => 'Coba bajak chat orang lain.',
    ])->assertForbidden();

    // 6. CS escalates to Admin -> Admin marks resolved -> User gets notification, closing CS message, and chat is locked
    $adminUtama = User::factory()->create([
        'role' => 'admin_utama',
        'status' => 'active',
    ]);

    $this->actingAs($this->cs)->post(route('dashboard.customer-service.live-chat.escalate', $conversation), [
        'escalation_type' => 'refund',
        'escalation_reason' => 'Refund tiket disetujui karena jadwal batal.',
    ])->assertRedirect();

    $complaint = CustomerComplaint::where('booking_id', $this->urgentBooking->id)->latest('id')->firstOrFail();

    $this->actingAs($adminUtama)->patch(route('dashboard.admin.eskalasi.update', $complaint), [
        'status' => 'selesai',
        'follow_up_notes' => 'Dana refund sudah ditransfer ke rekening pelanggan.',
    ])->assertRedirect();

    // User receives notification
    expect($passenger->fresh()->notifications()->count())->toBeGreaterThanOrEqual(1);

    // Conversation is automatically closed with final CS message
    $conversation->refresh();
    expect($conversation->status)->toBe('resolved');

    $closedView = $this->actingAs($passenger)->get(route('dashboard.user.bantuan-cs', ['conversation' => $conversation->id]));
    $closedView->assertOk();
    $closedView->assertSee('telah selesai diatasi oleh tim Admin TravelKu');
    $closedView->assertSee('Dana refund sudah ditransfer ke rekening pelanggan.');
    $closedView->assertSee('percakapan telah ditutup');
    $closedView->assertDontSee('user-reply-input');

    // User cannot reply to the closed conversation anymore
    $countBefore = $conversation->messages()->count();
    $blockedReply = $this->actingAs($passenger)->post(route('dashboard.user.bantuan-cs.reply', $conversation), [
        'message' => 'Halo masih bisa balas?',
    ]);
    $blockedReply->assertRedirect();
    $blockedReply->assertSessionHas('error');
    expect($conversation->messages()->count())->toBe($countBefore);
});
