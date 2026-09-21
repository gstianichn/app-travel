<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TripReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public ?string $departureTime = null,
        public ?string $requiredArrivalTime = null,
        public ?string $seatNumbers = null
    ) {}

    /**
     * Tentukan channel pengiriman notifikasi (Database + Push Device).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $pushService = app(PushNotificationService::class);
        $payload = $this->buildPayload();

        $pushService->send(
            $notifiable,
            $payload['title'],
            $payload['message'],
            $payload['deep_link_url'],
            'pengingat',
            $payload['meta']
        );

        return ['database'];
    }

    /**
     * Format data yang disimpan ke database riwayat pengguna.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->buildPayload();
    }

    /**
     * Bangun konten pengingat perjalanan H-2 jam.
     *
     * @return array<string, mixed>
     */
    protected function buildPayload(): array
    {
        $origin = $this->booking->schedule?->route?->origin?->name ?? 'Pool Asal';
        $destination = $this->booking->schedule?->route?->destination?->name ?? 'Kota Tujuan';
        $routeStr = "{$origin} → {$destination}";

        // Tentukan jam keberangkatan
        $departureRaw = $this->departureTime ?? $this->booking->schedule?->departure_time ?? '14:30';
        $departureCarbon = Carbon::createFromTimeString($departureRaw);
        $depTimeStr = $departureCarbon->format('H:i').' WIB';

        // Jam wajib tiba di pool (15 menit sebelum keberangkatan)
        $arrivalTimeStr = $this->requiredArrivalTime
            ?? $departureCarbon->copy()->subMinutes(15)->format('H:i').' WIB';

        // Nomor kursi penumpang
        if ($this->seatNumbers) {
            $seatsStr = $this->seatNumbers;
        } else {
            $seats = $this->booking->passengers
                ->map(fn ($p) => $p->seat?->seat_number ?? $p->seat_number)
                ->filter()
                ->values();
            $seatsStr = $seats->isNotEmpty() ? $seats->join(', ') : '1A';
        }

        // Deep link langsung ke E-Ticket QR
        $deepLinkUrl = route('dashboard.user.user-tiket', ['booking_id' => $this->booking->id, 'action' => 'ticket']);

        return [
            'title' => 'Pengingat Perjalanan (H-2 Jam) ⏰',
            'message' => "Perjalanan Anda rute {$routeStr} berangkat pukul {$depTimeStr}. Harap tiba di pool {$origin} maksimal pukul {$arrivalTimeStr} (15 menit sebelum jalan). Nomor kursi Anda: [Kursi {$seatsStr}].",
            'category' => 'pengingat',
            'status_type' => 'reminder_2h',
            'deep_link_url' => $deepLinkUrl,
            'action_label' => 'Buka E-Ticket & QR',
            'icon' => 'calendar-clock',
            'meta' => [
                'booking_id' => $this->booking->id,
                'booking_code' => $this->booking->booking_code,
                'route' => $routeStr,
                'origin' => $origin,
                'destination' => $destination,
                'departure_time' => $depTimeStr,
                'pool_arrival_time' => $arrivalTimeStr,
                'seats' => $seatsStr,
            ],
        ];
    }
}
