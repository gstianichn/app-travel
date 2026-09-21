<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OperationalInfoNotification extends Notification
{
    use Queueable;

    /**
     * @param  'vehicle_assigned'|'pickup_location'|'delay_traffic'  $infoType
     */
    public function __construct(
        public Booking $booking,
        public string $infoType = 'vehicle_assigned',
        public ?string $plateNumber = null,
        public ?string $driverName = null,
        public ?string $pickupLocation = null,
        public ?string $delayReason = null,
        public ?int $delayMinutes = null
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
            'operasional',
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
     * Bangun konten notifikasi info operasional hari-H.
     *
     * @return array<string, mixed>
     */
    protected function buildPayload(): array
    {
        $vehicle = $this->booking->schedule?->vehicle;
        $driver = $this->booking->schedule?->driver;

        $plate = $this->plateNumber ?? $vehicle?->plate_number ?? 'D 7890 AB';
        $vehicleModel = $vehicle?->model ?? 'Toyota HiAce Premio Luxury';
        $driverName = $this->driverName ?? $driver?->user?->name ?? 'Pak Budi Santoso';
        $pickup = $this->pickupLocation
            ?? $this->booking->schedule?->route?->origin?->name
            ?? 'Pool Dipatiukur (Gate 2)';

        // Deep link ke tab tiket / tracking perjalanan
        $deepLinkUrl = route('dashboard.user.user-tiket', [
            'booking_id' => $this->booking->id,
            'action' => 'operational',
        ]);

        if ($this->infoType === 'delay_traffic') {
            $minutes = $this->delayMinutes ?? 15;
            $reason = $this->delayReason ?? 'Kepadatan lalu lintas di Tol Cipularang KM 90';

            return [
                'title' => 'Update Operasional: Estimasi Keterlambatan ⚠️',
                'message' => "Armada minibus {$plate} ({$vehicleModel}) mengalami sedikit penyesuaian waktu sekitar {$minutes} menit dikarenakan {$reason}. Pengemudi ({$driverName}) terus mengupayakan perjalanan seaman mungkin.",
                'category' => 'operasional',
                'status_type' => 'delay_traffic',
                'deep_link_url' => $deepLinkUrl,
                'action_label' => 'Pantau Status Perjalanan',
                'icon' => 'alert-triangle',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $this->booking->booking_code,
                    'plate_number' => $plate,
                    'vehicle_model' => $vehicleModel,
                    'driver_name' => $driverName,
                    'delay_minutes' => $minutes,
                    'delay_reason' => $reason,
                ],
            ];
        }

        if ($this->infoType === 'pickup_location') {
            return [
                'title' => 'Posisi Penjemputan Armada 📍',
                'message' => "Armada {$plate} siap di {$pickup}. Silakan merapat ke titik penjemputan dan siapkan QR Tiket Anda untuk verifikasi boarding.",
                'category' => 'operasional',
                'status_type' => 'pickup_location',
                'deep_link_url' => $deepLinkUrl,
                'action_label' => 'Buka Tiket & Panduan Pool',
                'icon' => 'map-pin',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $this->booking->booking_code,
                    'plate_number' => $plate,
                    'pickup_location' => $pickup,
                    'driver_name' => $driverName,
                ],
            ];
        }

        // vehicle_assigned / ready
        return [
            'title' => 'Info Armada & Pengemudi Hari-H 🚐',
            'message' => "Armada travel Anda hari ini adalah {$vehicleModel} dengan nomor pelat {$plate}, dikemudikan oleh {$driverName}. Armada bersiap di {$pickup}.",
            'category' => 'operasional',
            'status_type' => 'vehicle_assigned',
            'deep_link_url' => $deepLinkUrl,
            'action_label' => 'Lihat Detail Armada',
            'icon' => 'truck',
            'meta' => [
                'booking_id' => $this->booking->id,
                'booking_code' => $this->booking->booking_code,
                'plate_number' => $plate,
                'vehicle_model' => $vehicleModel,
                'driver_name' => $driverName,
                'pickup_location' => $pickup,
            ],
        ];
    }
}
