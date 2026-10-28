<?php

use App\Models\User;
use App\Models\UserVoucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => 'user',
        'password' => Hash::make('password'),
    ]);
});

function createWalletVoucher(User $user, string $code, string $status, ?Carbon $expiresAt = null): UserVoucher
{
    return $user->vouchers()->create([
        'code' => $code,
        'title' => 'Voucher '.$code,
        'discount_type' => 'fixed',
        'discount_value' => 20000,
        'max_discount' => null,
        'min_transaction' => 75000,
        'applicable_route' => null,
        'source' => 'claim_promo',
        'status' => $status,
        'expires_at' => $expiresAt,
    ]);
}

test('dompet voucher menampilkan daftar dan jumlah yang benar untuk setiap filter status', function () {
    $activeA = createWalletVoucher($this->user, 'ACTIVE-A', 'active', Carbon::now()->addDays(10));
    $activeB = createWalletVoucher($this->user, 'ACTIVE-B', 'active', Carbon::now()->addDays(20));
    $used = createWalletVoucher($this->user, 'USED-1', 'used', Carbon::now()->addDays(5));
    $expired = createWalletVoucher($this->user, 'EXPIRED-1', 'expired', Carbon::now()->subDays(2));

    $expectedIds = [
        'all' => [$activeA->id, $activeB->id, $used->id, $expired->id],
        'active' => [$activeA->id, $activeB->id],
        'used' => [$used->id],
        'expired' => [$expired->id],
    ];

    foreach ($expectedIds as $filter => $ids) {
        $response = $this->actingAs($this->user)->get(route('dashboard.user.user-promo', [
            'status' => $filter,
        ]));

        $response->assertOk();
        $response->assertViewHas('filterStatus', $filter);
        $response->assertViewHas(
            'vouchers',
            fn ($vouchers) => $vouchers->pluck('id')->sort()->values()->all() === collect($ids)->sort()->values()->all()
        );
    }

    $this->actingAs($this->user)->get(route('dashboard.user.user-promo'))
        ->assertOk()
        ->assertViewHas('totalCount', 4)
        ->assertViewHas('activeCount', 2)
        ->assertViewHas('usedCount', 1)
        ->assertViewHas('expiredCount', 1);
});

test('klaim voucher valid WELCOME2026 masuk dompet dan aktif 30 hari', function () {
    createWalletVoucher($this->user, 'EXISTING-VOUCHER', 'active', Carbon::now()->addDays(5));

    $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'WELCOME2026',
    ])->assertRedirect()->assertSessionHas('success');

    $voucher = $this->user->vouchers()->where('code', 'WELCOME2026')->first();

    expect($voucher)->not->toBeNull();
    expect($voucher->status)->toBe('active');
    expect($voucher->source)->toBe('welcome_gift');
    expect($voucher->expires_at->between(Carbon::now()->addDays(29), Carbon::now()->addDays(31)))->toBeTrue();
});

test('klaim voucher yang masih aktif ditolak karena sudah ada di dompet', function () {
    createWalletVoucher($this->user, 'EXISTING-VOUCHER', 'active', Carbon::now()->addDays(5));

    $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'WELCOME2026',
    ])->assertSessionHas('success');

    $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'WELCOME2026',
    ])->assertSessionHas('error');

    expect(session('error'))
        ->toContain('sudah ada')
        ->toContain('masih aktif');

    expect($this->user->vouchers()->where('code', 'WELCOME2026')->count())->toBe(1);
});

test('klaim kode yang tidak valid ditolak', function () {
    $this->actingAs($this->user)->post(route('dashboard.user.voucher.claim'), [
        'code' => 'ABCDEF',
    ])->assertSessionHas('error');

    expect(session('error'))->toContain('tidak valid atau promo sudah berakhir');
    expect($this->user->vouchers()->where('code', 'ABCDEF')->exists())->toBeFalse();
});
