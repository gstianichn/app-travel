<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DriverOperationalNotification extends Notification
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $title,
        public string $message,
        public string $statusType = 'driver_alert',
        public ?string $deepLinkUrl = null,
        public ?string $actionLabel = null,
        public array $meta = []
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => 'operasional',
            'status_type' => $this->statusType,
            'deep_link_url' => $this->deepLinkUrl,
            'action_label' => $this->actionLabel ?? 'Lihat Detail',
            'meta' => $this->meta,
        ];
    }
}
