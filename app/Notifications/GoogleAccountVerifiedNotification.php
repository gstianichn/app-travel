<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoogleAccountVerifiedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $gmailAddress,
        public string $loginTime,
        public string $deviceInfo = 'Browser Web',
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Login Google Berhasil - TravelKu')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Akun TravelKu Anda baru saja masuk dan terverifikasi menggunakan akun Google ('.$this->gmailAddress.').')
            ->line('Waktu Login: '.$this->loginTime)
            ->line('Perangkat: '.$this->deviceInfo)
            ->action('Buka Dashboard Penumpang', route('dashboard.user.index'))
            ->line('Jika ini adalah Anda, tidak ada tindakan lebih lanjut yang diperlukan.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Verifikasi Akun Google ('.$this->gmailAddress.') Berhasil',
            'message' => 'Login menggunakan akun Google '.$this->gmailAddress.' berhasil diverifikasi pada '.$this->loginTime.'. Notifikasi verifikasi juga telah dikirim ke Gmail Anda.',
            'type' => 'google_verified',
            'email' => $this->gmailAddress,
            'action_url' => 'https://mail.google.com/mail/u/?authuser='.urlencode($this->gmailAddress),
        ];
    }
}
