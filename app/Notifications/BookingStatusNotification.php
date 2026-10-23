<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    /**
     * @param  'pending'|'confirmed'|'expired'|'cancelled'  $statusType
     */
    public function __construct(
        public Booking $booking,
        public string $statusType = 'pending',
        public ?string $customMessage = null
    ) {}

    /**
     * Tentukan channel pengiriman notifikasi (Database + Push Device).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Jalankan penyampaian ganda via PushNotificationService
        $pushService = app(PushNotificationService::class);
        $payload = $this->buildPayload();

        $pushService->send(
            $notifiable,
            $payload['title'],
            $payload['message'],
            $payload['deep_link_url'],
            'transaksi',
            $payload['meta']
        );

        return ['database'];
    }

    /**
     * Format data yang disimpan di tabel riwayat notifikasi database pengguna.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->buildPayload();
    }

    /**
     * Bangun konten payload notifikasi sesuai status transaksi.
     *
     * @return array<string, mixed>
     */
    protected function buildPayload(): array
    {
        $origin = $this->booking->schedule?->route?->origin?->name ?? 'Kota Asal';
        $destination = $this->booking->schedule?->route?->destination?->name ?? 'Kota Tujuan';
        $routeStr = "{$origin} → {$destination}";
        $bookingCode = $this->booking->booking_code ?? ('TRV-'.$this->booking->id);

        if ($this->statusType === 'pending') {
            $expiredAt = $this->booking->expired_at
                ? Carbon::parse($this->booking->expired_at)->format('H:i').' WIB'
                : '15 menit lagi';

            return [
                'title' => 'Menunggu Pembayaran Tiket ⏳',
                'message' => "Pesanan {$bookingCode} ({$routeStr}) menunggu pembayaran. Selesaikan pembayaran sebelum pukul {$expiredAt} agar kursi tidak dibatalkan otomatis.",
                'category' => 'transaksi',
                'status_type' => 'pending',
                'deep_link_url' => route('dashboard.user.booking.pembayaran', $this->booking),
                'action_label' => 'Bayar Sekarang',
                'icon' => 'clock',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $bookingCode,
                    'route' => $routeStr,
                    'total_price' => $this->booking->total_price,
                    'expired_at' => $this->booking->expired_at?->toISOString(),
                ],
            ];
        }

        if ($this->statusType === 'confirmed' || $this->statusType === 'paid') {
            return [
                'title' => 'Pembayaran Berhasil! E-Ticket Terbit ✅',
                'message' => "Pembayaran pesanan {$bookingCode} ({$routeStr}) telah terverifikasi. E-Ticket & Boarding Pass QR Anda sudah aktif dan siap digunakan.",
                'category' => 'transaksi',
                'status_type' => 'confirmed',
                'deep_link_url' => route('dashboard.user.booking.sukses', $this->booking),
                'action_label' => 'Buka E-Ticket',
                'icon' => 'check-circle',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $bookingCode,
                    'route' => $routeStr,
                    'ticket_number' => $this->booking->ticket?->ticket_number,
                ],
            ];
        }

        if ($this->statusType === 'admin_cancelled' || $this->statusType === 'cancelled') {
            return [
                'title' => 'Tiket Dibatalkan oleh Admin ⚠️',
                'message' => $this->customMessage ?: "Pesanan {$bookingCode} ({$routeStr}) telah dibatalkan oleh pihak admin. Dana Anda telah otomatis dikembalikan (refund).",
                'category' => 'transaksi',
                'status_type' => 'cancelled',
                'deep_link_url' => route('dashboard.user.user-tiket'),
                'action_label' => 'Lihat Tiket',
                'icon' => 'x-circle',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $bookingCode,
                    'route' => $routeStr,
                ],
            ];
        }

        if ($this->statusType === 'refund_approved') {
            return [
                'title' => 'Pengajuan Refund Disetujui ✅',
                'message' => $this->customMessage ?: "Pengajuan refund untuk pesanan {$bookingCode} ({$routeStr}) telah disetujui admin dan dana otomatis dikembalikan.",
                'category' => 'transaksi',
                'status_type' => 'refund_approved',
                'deep_link_url' => route('dashboard.user.user-tiket'),
                'action_label' => 'Lihat Rincian',
                'icon' => 'check-circle',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $bookingCode,
                    'route' => $routeStr,
                ],
            ];
        }

        if ($this->statusType === 'refund_rejected') {
            return [
                'title' => 'Pengajuan Refund Ditolak ⚠️',
                'message' => $this->customMessage ?: "Pengajuan refund untuk pesanan {$bookingCode} ({$routeStr}) ditolak oleh pihak admin.",
                'category' => 'transaksi',
                'status_type' => 'refund_rejected',
                'deep_link_url' => route('dashboard.user.user-tiket'),
                'action_label' => 'Lihat Rincian',
                'icon' => 'alert-triangle',
                'meta' => [
                    'booking_id' => $this->booking->id,
                    'booking_code' => $bookingCode,
                    'route' => $routeStr,
                ],
            ];
        }

        // expired
        return [
            'title' => 'Transaksi Dibatalkan (Waktu Habis) ⚠️',
            'message' => $this->customMessage ?: "Pesanan {$bookingCode} ({$routeStr}) otomatis dibatalkan karena batas waktu pembayaran telah habis. Kursi telah dibebaskan kembali.",
            'category' => 'transaksi',
            'status_type' => 'expired',
            'deep_link_url' => route('dashboard.user.search'),
            'action_label' => 'Cari Jadwal Baru',
            'icon' => 'x-circle',
            'meta' => [
                'booking_id' => $this->booking->id,
                'booking_code' => $bookingCode,
                'route' => $routeStr,
            ],
        ];
    }
}
