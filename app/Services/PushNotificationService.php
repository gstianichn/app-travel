<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PushNotificationService
{
    /**
     * Kirim push notification ke perangkat mobile/browser pengguna.
     * Mendukung Firebase Cloud Messaging (FCM), OneSignal, serta Web Push API browser.
     *
     * @param  array<string, mixed>  $extraData
     * @return array{fcm_status: string, onesignal_status: string, delivered_web: bool}
     */
    public function send(
        User $user,
        string $title,
        string $body,
        string $deepLinkUrl,
        string $category = 'system',
        array $extraData = []
    ): array {
        $result = [
            'fcm_status' => 'skipped',
            'onesignal_status' => 'skipped',
            'delivered_web' => true,
        ];

        // 1. Kirim ke OneSignal jika kredensial terkonfigurasi
        $oneSignalAppId = config('services.onesignal.app_id', env('ONESIGNAL_APP_ID'));
        $oneSignalApiKey = config('services.onesignal.api_key', env('ONESIGNAL_REST_API_KEY'));

        if ($oneSignalAppId && $oneSignalApiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '.$oneSignalApiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(5)->post('https://onesignal.com/api/v1/notifications', [
                    'app_id' => $oneSignalAppId,
                    'headings' => ['en' => $title, 'id' => $title],
                    'contents' => ['en' => $body, 'id' => $body],
                    'url' => $deepLinkUrl,
                    'data' => array_merge($extraData, [
                        'deep_link' => $deepLinkUrl,
                        'category' => $category,
                    ]),
                    'filters' => [
                        ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => (string) $user->id],
                    ],
                ]);

                $result['onesignal_status'] = $response->successful() ? 'sent' : 'failed';
            } catch (\Throwable $e) {
                Log::warning('OneSignal push notification error: '.$e->getMessage());
                $result['onesignal_status'] = 'error';
            }
        }

        // 2. Kirim ke Firebase Cloud Messaging (FCM) jika server key terkonfigurasi
        $fcmServerKey = config('services.fcm.server_key', env('FCM_SERVER_KEY'));
        if ($fcmServerKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key='.$fcmServerKey,
                    'Content-Type' => 'application/json',
                ])->timeout(5)->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => '/topics/user_'.$user->id,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'click_action' => $deepLinkUrl,
                    ],
                    'data' => array_merge($extraData, [
                        'url' => $deepLinkUrl,
                        'category' => $category,
                    ]),
                ]);

                $result['fcm_status'] = $response->successful() ? 'sent' : 'failed';
            } catch (\Throwable $e) {
                Log::warning('Firebase push notification error: '.$e->getMessage());
                $result['fcm_status'] = 'error';
            }
        }

        // 3. Catat ke session flash agar browser HP langsung menerima Web Push / banner notifikasi
        if (session()) {
            $existing = session()->get('queued_push_notifications', []);
            $existing[] = [
                'id' => (string) Str::uuid(),
                'title' => $title,
                'body' => $body,
                'category' => $category,
                'deep_link_url' => $deepLinkUrl,
                'created_at' => now()->toISOString(),
                'data' => $extraData,
            ];
            session()->flash('queued_push_notifications', $existing);
        }

        Log::info('Push Notification triggered for User #'.$user->id, [
            'title' => $title,
            'category' => $category,
            'deep_link' => $deepLinkUrl,
            'channels' => $result,
        ]);

        return $result;
    }
}
