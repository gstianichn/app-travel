<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Decides who may open a booking during checkout.
 *
 * Account bookings belong to their user. Guest bookings (no account) are remembered
 * in the browser session that created them, and move to an account as soon as that
 * session logs in or registers.
 */
class BookingAccess
{
    private const SESSION_KEY = 'guest_booking_ids';

    public function remember(Booking $booking): void
    {
        if ($booking->user_id !== null) {
            return;
        }

        $ids = $this->guestBookingIds();
        $ids[] = $booking->id;

        session()->put(self::SESSION_KEY, array_values(array_unique($ids)));
    }

    public function canAccess(Booking $booking): bool
    {
        if ($booking->user_id !== null) {
            return Auth::id() === $booking->user_id;
        }

        return in_array($booking->id, $this->guestBookingIds(), true);
    }

    /**
     * Limit a booking query to bookings started by the current visitor.
     *
     * @param  Builder<Booking>  $query
     * @return Builder<Booking>
     */
    public function scopeToCurrentVisitor(Builder $query): Builder
    {
        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }

        return $query->whereNull('user_id')->whereIn('id', $this->guestBookingIds());
    }

    /**
     * Attach this session's guest bookings to an account after login or registration.
     */
    public function claimFor(User $user): int
    {
        $ids = $this->guestBookingIds();

        session()->forget(self::SESSION_KEY);

        $query = Booking::query()->whereNull('user_id');

        if ($ids !== []) {
            $query->where(function ($q) use ($ids, $user) {
                $q->whereIn('id', $ids)
                    ->orWhere('contact_email', $user->email);
            });
        } else {
            $query->where('contact_email', $user->email);
        }

        return $query->update(['user_id' => $user->id]);
    }

    /**
     * Attach guest bookings made with an email address the provider has verified (Google login).
     */
    public function claimByVerifiedEmail(User $user): int
    {
        return Booking::query()
            ->whereNull('user_id')
            ->where('contact_email', $user->email)
            ->update(['user_id' => $user->id]);
    }

    /**
     * @return array<int, int>
     */
    private function guestBookingIds(): array
    {
        return array_map('intval', (array) session()->get(self::SESSION_KEY, []));
    }
}
