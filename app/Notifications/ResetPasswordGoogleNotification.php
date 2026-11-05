<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordGoogleNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $email = $notifiable->getEmailForPasswordReset();
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ], false));

        return (new MailMessage)
            ->subject('Reset Password Akun Google / TravelKu ('.$email.')')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Kami menerima permintaan pengaturan ulang (reset) password untuk akun TravelKu yang terhubung ke email Google Anda: '.$email.'.')
            ->action('Atur Ulang Password Sekarang', $resetUrl)
            ->line('Tautan reset password ini berlaku selama 60 menit.')
            ->line('Jika Anda tidak merasa meminta reset password, abaikan pesan ini.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $email = $notifiable->getEmailForPasswordReset();
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ], false));

        return [
            'title' => 'Permintaan Reset Password Dikirim ke '.$email,
            'message' => 'Tautan pemulihan password telah dikirim ke akun Google/email '.$email.'.',
            'type' => 'password_reset',
            'email' => $email,
            'action_url' => $resetUrl,
        ];
    }
}
