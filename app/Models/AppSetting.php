<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    /** @var array<int, string> */
    protected $fillable = ['key', 'value'];

    /**
     * Retrieve a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $record = static::where('key', $key)->first();

        return $record ? $record->value : $default;
    }

    /**
     * Persist a setting value by key (upsert).
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Remove a setting by key.
     */
    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
    }

    /**
     * Company name as set by the admin in Pengaturan.
     */
    public static function companyName(): string
    {
        return (string) (static::get('company_name') ?: 'TravelKu');
    }

    /**
     * WhatsApp link to customer service, or null when the admin has not set a number yet.
     */
    public static function whatsappUrl(string $message = ''): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) static::get('company_whatsapp', ''));

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits.($message !== '' ? '?text='.rawurlencode($message) : '');
    }

    /**
     * Office phone set by the admin, as display text and a tel: link, or null when it is not set.
     *
     * @return array{display: string, tel: string}|null
     */
    public static function companyPhone(): ?array
    {
        $display = trim((string) static::get('company_phone', ''));
        $digits = preg_replace('/[^\d+]/', '', $display);

        if ($digits === '') {
            return null;
        }

        return ['display' => $display, 'tel' => 'tel:'.$digits];
    }
}
