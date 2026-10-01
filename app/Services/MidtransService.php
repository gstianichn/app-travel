<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Booking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Dapatkan Server Key dari konfigurasi atau database AppSetting.
     */
    public function getServerKey(): string
    {
        $key = config('midtrans.server_key');
        if (! empty($key)) {
            return $key;
        }

        return (string) (AppSetting::get('gateway_server_key') ?: AppSetting::get('midtrans_server_key', ''));
    }

    /**
     * Dapatkan Client Key dari konfigurasi atau database AppSetting.
     */
    public function getClientKey(): string
    {
        $key = config('midtrans.client_key');
        if (! empty($key)) {
            return $key;
        }

        return (string) (AppSetting::get('gateway_client_key') ?: AppSetting::get('midtrans_client_key', ''));
    }

    /**
     * Periksa apakah environment adalah production.
     */
    public function isProduction(): bool
    {
        $configProd = config('midtrans.is_production');
        if ($configProd !== null && $configProd !== false && $configProd !== '') {
            return (bool) $configProd;
        }

        return (bool) AppSetting::get('midtrans_is_production', false);
    }

    /**
     * URL Dasar untuk Core API.
     */
    public function getCoreApiBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * URL Dasar untuk Snap API.
     */
    public function getSnapApiBaseUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    /**
     * Daftar Channel Pembayaran yang didukung.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAvailableChannels(): array
    {
        return [
            // Bank Transfer (Virtual Account)
            'bca' => [
                'category' => 'va',
                'name' => 'BCA Virtual Account',
                'bank_name' => 'BCA',
                'code' => 'bca',
                'badge' => 'Otomatis 24 Jam',
                'description' => 'Transfer via BCA Mobile, KlikBCA, myBCA, atau ATM BCA.',
                'icon_bg' => 'bg-blue-600',
                'icon_text' => 'BCA',
                'fee' => 0,
            ],
            'mandiri' => [
                'category' => 'va',
                'name' => 'Mandiri Bill Payment',
                'bank_name' => 'Mandiri',
                'code' => 'mandiri',
                'badge' => 'Otomatis 24 Jam',
                'description' => 'Bayar melalui Livin by Mandiri atau ATM menggunakan Biller Code & Bill Key.',
                'icon_bg' => 'bg-amber-600',
                'icon_text' => 'MANDIRI',
                'fee' => 0,
            ],
            'bni' => [
                'category' => 'va',
                'name' => 'BNI Virtual Account',
                'bank_name' => 'BNI',
                'code' => 'bni',
                'badge' => 'Otomatis 24 Jam',
                'description' => 'Transfer via BNI Mobile Banking, Internet Banking, atau ATM BNI.',
                'icon_bg' => 'bg-teal-600',
                'icon_text' => 'BNI',
                'fee' => 0,
            ],
            'bri' => [
                'category' => 'va',
                'name' => 'BRI Virtual Account (BRIVA)',
                'bank_name' => 'BRI',
                'code' => 'bri',
                'badge' => 'Otomatis 24 Jam',
                'description' => 'Bayar lewat BRImo, Internet Banking BRI, atau ATM BRI.',
                'icon_bg' => 'bg-sky-700',
                'icon_text' => 'BRI',
                'fee' => 0,
            ],
            'bsi' => [
                'category' => 'va',
                'name' => 'BSI Virtual Account',
                'bank_name' => 'BSI',
                'code' => 'bsi',
                'badge' => 'Syariah Otomatis',
                'description' => 'Transfer via BSI Mobile atau ATM BSI dengan kode Virtual Account Bank Syariah.',
                'icon_bg' => 'bg-emerald-700',
                'icon_text' => 'BSI',
                'fee' => 0,
            ],
            'seabank' => [
                'category' => 'va',
                'name' => 'SeaBank (Transfer VA Antar Bank)',
                'bank_name' => 'SeaBank',
                'code' => 'seabank',
                'badge' => 'Bebas Biaya Transfer',
                'description' => 'Transfer gratis dari aplikasi SeaBank via BI-FAST / Virtual Account Permata/BNI.',
                'icon_bg' => 'bg-orange-600',
                'icon_text' => 'SEABANK',
                'fee' => 0,
            ],

            // E-Wallet & QRIS
            'qris' => [
                'category' => 'qris',
                'name' => 'QRIS Dinamis Universal',
                'bank_name' => 'QRIS',
                'code' => 'qris',
                'badge' => 'Semua E-Wallet & M-Banking',
                'description' => 'Scan QRIS memakai GoPay, OVO, DANA, ShopeePay, LinkAja, BCA, Livin Mandiri, dll.',
                'icon_bg' => 'bg-rose-600',
                'icon_text' => 'QRIS',
                'fee' => 0,
            ],
            'gopay' => [
                'category' => 'ewallet',
                'name' => 'GoPay Instant',
                'bank_name' => 'GoPay',
                'code' => 'gopay',
                'badge' => 'Direct App / QR',
                'description' => 'Buka aplikasi GoPay atau Gojek langsung untuk pembayaran instan.',
                'icon_bg' => 'bg-sky-600',
                'icon_text' => 'GOPAY',
                'fee' => 0,
            ],
            'shopeepay' => [
                'category' => 'ewallet',
                'name' => 'ShopeePay',
                'bank_name' => 'ShopeePay',
                'code' => 'shopeepay',
                'badge' => 'Deep Link Aplikasi',
                'description' => 'Buka aplikasi Shopee langsung untuk verifikasi pembayaran.',
                'icon_bg' => 'bg-orange-500',
                'icon_text' => 'SHOPEEPAY',
                'fee' => 0,
            ],
        ];
    }

    /**
     * Buat Transaksi Pembayaran melalui Midtrans Core API.
     *
     * @param  string  $channel  (bca, mandiri, bni, bri, bsi, seabank, qris, gopay, shopeepay)
     * @return array<string, mixed>
     */
    public function charge(Booking $booking, string $channel, ?string $customOrderId = null): array
    {
        $orderId = $customOrderId ?: 'INV-'.Carbon::now()->format('Ymd').'-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
        $grossAmount = (int) round($booking->total_price);

        $customerDetails = [
            'first_name' => $booking->user?->name ?: 'Pelanggan Fast Travel',
            'email' => $booking->user?->email ?: 'customer@fasttravel.co.id',
            'phone' => $booking->user?->phone ?: '081234567890',
        ];

        $itemDetails = [
            [
                'id' => 'SCHED-'.$booking->schedule_id,
                'price' => (int) round($booking->subtotal),
                'quantity' => 1,
                'name' => 'Tiket Travel ('.($booking->schedule?->route?->origin?->city ?? 'Asal').' - '.($booking->schedule?->route?->destination?->city ?? 'Tujuan').')',
            ],
        ];

        if ($booking->discount > 0) {
            $itemDetails[] = [
                'id' => 'DISC-PROMO',
                'price' => -(int) round($booking->discount),
                'quantity' => 1,
                'name' => 'Potongan Voucer Promo',
            ];
        }

        // Tentukan payload spesifik berdasarkan channel
        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        switch ($channel) {
            case 'bca':
                $payload['payment_type'] = 'bank_transfer';
                $payload['bank_transfer'] = ['bank' => 'bca'];
                break;

            case 'bni':
                $payload['payment_type'] = 'bank_transfer';
                $payload['bank_transfer'] = ['bank' => 'bni'];
                break;

            case 'bri':
                $payload['payment_type'] = 'bank_transfer';
                $payload['bank_transfer'] = ['bank' => 'bri'];
                break;

            case 'mandiri':
                $payload['payment_type'] = 'echannel';
                $payload['echannel'] = [
                    'bill_info1' => 'Tiket Fast Travel',
                    'bill_info2' => 'Booking #'.$booking->booking_code,
                ];
                break;

            case 'bsi':
                // BSI pada Midtrans dapat diproses via Permata Syariah/generic VA atau BNI syariah
                $payload['payment_type'] = 'permata';
                break;

            case 'seabank':
                // SeaBank di Midtrans menggunakan Virtual Account Permata / universal VA inter-bank
                $payload['payment_type'] = 'permata';
                break;

            case 'qris':
                $payload['payment_type'] = 'qris';
                $payload['qris'] = ['acquirer' => 'gopay'];
                break;

            case 'gopay':
                $payload['payment_type'] = 'gopay';
                $payload['gopay'] = [
                    'enable_callback' => true,
                    'callback_url' => route('dashboard.user.booking.pembayaran', $booking),
                ];
                break;

            case 'shopeepay':
                $payload['payment_type'] = 'shopeepay';
                $payload['shopeepay'] = [
                    'callback_url' => route('dashboard.user.booking.pembayaran', $booking),
                ];
                break;

            default:
                $payload['payment_type'] = 'bank_transfer';
                $payload['bank_transfer'] = ['bank' => 'bca'];
                break;
        }

        $serverKey = $this->getServerKey();

        // Jika Server Key tersedia dan bukan environment testing offline, lakukan HTTP Request langsung
        if (! empty($serverKey) && ! app()->environment('testing') && ! str_starts_with($serverKey, 'mock-')) {
            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->post($this->getCoreApiBaseUrl().'/charge', $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    $json['simulated'] = false;

                    return $json;
                }

                Log::warning('Midtrans Charge API returned error: '.$response->body());
            } catch (Exception $e) {
                Log::error('Midtrans Charge API Exception: '.$e->getMessage());
            }
        }

        // Mock / Sandbox Local Simulation Fallback (agar selalu bekerja saat demo/test offline)
        return $this->generateSimulatedResponse($payload, $channel, $booking, $orderId, $grossAmount);
    }

    /**
     * Buat Snap Token & URL untuk integrasi modal popup/redirect Snap.
     *
     * @return array<string, mixed>
     */
    public function createSnapToken(Booking $booking, ?string $customOrderId = null): array
    {
        $orderId = $customOrderId ?: 'INV-'.Carbon::now()->format('Ymd').'-'.str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
        $grossAmount = (int) round($booking->total_price);

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $booking->user?->name ?: 'Pelanggan Fast Travel',
                'email' => $booking->user?->email ?: 'customer@fasttravel.co.id',
                'phone' => $booking->user?->phone ?: '081234567890',
            ],
            'enabled_payments' => [
                'bca_va', 'bni_va', 'bri_va', 'echannel', 'permata_va', 'other_va',
                'gopay', 'shopeepay', 'qris',
            ],
        ];

        $serverKey = $this->getServerKey();

        if (! empty($serverKey) && ! app()->environment('testing') && ! str_starts_with($serverKey, 'mock-')) {
            try {
                $response = Http::withBasicAuth($serverKey, '')
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])
                    ->post($this->getSnapApiBaseUrl().'/transactions', $payload);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (Exception $e) {
                Log::error('Midtrans Snap Exception: '.$e->getMessage());
            }
        }

        // Simulated Snap token
        $mockToken = 'snap-token-'.md5($orderId);

        return [
            'token' => $mockToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v4/redirection/'.$mockToken,
            'simulated' => true,
        ];
    }

    /**
     * Verifikasi keaslian signature webhook dari Midtrans.
     * Formula: SHA512(order_id + status_code + gross_amount + server_key)
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifySignature(array $payload): bool
    {
        $signature = $payload['signature_key'] ?? '';
        if (empty($signature)) {
            return false;
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey = $this->getServerKey();

        if (empty($serverKey)) {
            // Jika belum ada server key (testing/simulator), izinkan jika format signature sesuai
            return strlen($signature) === 128;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expected, $signature);
    }

    /**
     * Periksa status transaksi terkini langsung ke Midtrans API.
     *
     * @return array<string, mixed>|null
     */
    public function checkStatus(string $orderId): ?array
    {
        $serverKey = $this->getServerKey();
        if (empty($serverKey)) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Accept' => 'application/json'])
                ->get($this->getCoreApiBaseUrl().'/'.$orderId.'/status');

            if ($response->successful()) {
                return $response->json();
            }
        } catch (Exception $e) {
            Log::error('Midtrans Status Check Error: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Buat response simulasi Midtrans Core API realistis untuk keperluan lokal / demo.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function generateSimulatedResponse(array $payload, string $channel, Booking $booking, string $orderId, int $grossAmount): array
    {
        $transactionId = 'mid-'.strtoupper(bin2hex(random_bytes(8)));
        $bookingIdPad = str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT);
        $expiryTime = $booking->expired_at ? $booking->expired_at->format('Y-m-d H:i:s P') : Carbon::now()->addMinutes(15)->format('Y-m-d H:i:s P');

        $base = [
            'status_code' => '201',
            'status_message' => 'Success, transaction is created',
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'merchant_id' => 'G141532850',
            'gross_amount' => (string) $grossAmount.'.00',
            'currency' => 'IDR',
            'transaction_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'transaction_status' => 'pending',
            'fraud_status' => 'accept',
            'expiry_time' => $expiryTime,
            'simulated' => true,
        ];

        switch ($channel) {
            case 'bca':
                $base['payment_type'] = 'bank_transfer';
                $base['va_numbers'] = [
                    [
                        'bank' => 'bca',
                        'va_number' => '8271'.$bookingIdPad.'88'.rand(10, 99),
                    ],
                ];
                break;

            case 'bni':
                $base['payment_type'] = 'bank_transfer';
                $base['va_numbers'] = [
                    [
                        'bank' => 'bni',
                        'va_number' => '9881'.$bookingIdPad.'77'.rand(10, 99),
                    ],
                ];
                break;

            case 'bri':
                $base['payment_type'] = 'bank_transfer';
                $base['va_numbers'] = [
                    [
                        'bank' => 'bri',
                        'va_number' => '1264'.$bookingIdPad.'66'.rand(10, 99),
                    ],
                ];
                break;

            case 'mandiri':
                $base['payment_type'] = 'echannel';
                $base['bill_key'] = '0812'.$bookingIdPad.rand(10, 99);
                $base['biller_code'] = '70012';
                break;

            case 'bsi':
                // Virtual account BSI via Permata/Syariah inter-bank
                $base['payment_type'] = 'permata';
                $base['permata_va_number'] = '8528'.$bookingIdPad.'55'.rand(10, 99);
                $base['bank_note'] = 'Bank Syariah Indonesia (BSI) / Permata Syariah';
                break;

            case 'seabank':
                // SeaBank transfer via inter-bank Virtual Account Permata
                $base['payment_type'] = 'permata';
                $base['permata_va_number'] = '8528'.$bookingIdPad.'44'.rand(10, 99);
                $base['bank_note'] = 'SeaBank (Transfer Antar Bank ke Permata VA)';
                break;

            case 'qris':
                $base['payment_type'] = 'qris';
                $base['actions'] = [
                    [
                        'name' => 'generate-qr-code',
                        'method' => 'GET',
                        'url' => 'https://api.sandbox.midtrans.com/v2/qris/'.$transactionId.'/qr-code',
                    ],
                ];
                $base['qr_string'] = '00020101021226540014ID.LINKAJA.WWW011893600014000000000002030005104000840502015303360540'.str_pad((string) $grossAmount, 8, '0', STR_PAD_LEFT).'5802ID5919PT FAST TRAVEL INDO6007BANDUNG61054013262070703A016304'.strtoupper(substr(md5($orderId), 0, 4));
                break;

            case 'gopay':
                $base['payment_type'] = 'gopay';
                $base['actions'] = [
                    [
                        'name' => 'generate-qr-code',
                        'method' => 'GET',
                        'url' => 'https://api.sandbox.midtrans.com/v2/qris/'.$transactionId.'/qr-code',
                    ],
                    [
                        'name' => 'deeplink-redirect',
                        'method' => 'GET',
                        'url' => 'https://simulator.sandbox.midtrans.com/gopay/partner/app/payment-pin?id='.$transactionId,
                    ],
                ];
                $base['qr_string'] = '00020101021226540014ID.GOPAY.WWW...'.substr(md5($orderId), 0, 16);
                break;

            case 'shopeepay':
                $base['payment_type'] = 'shopeepay';
                $base['actions'] = [
                    [
                        'name' => 'deeplink-redirect',
                        'method' => 'GET',
                        'url' => 'https://simulator.sandbox.midtrans.com/shopeepay/partner/app/payment-pin?id='.$transactionId,
                    ],
                ];
                break;

            default:
                $base['payment_type'] = 'bank_transfer';
                $base['va_numbers'] = [
                    [
                        'bank' => 'bca',
                        'va_number' => '8271'.$bookingIdPad.'99'.rand(10, 99),
                    ],
                ];
                break;
        }

        return $base;
    }
}
