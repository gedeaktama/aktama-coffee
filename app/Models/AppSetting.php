<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Ambil nilai konfigurasi berdasarkan key
     */
    public static function get(string $key, $default = null)
    {
        // Ambil dari cache jika ada, jika tidak query database
        return Cache::remember("app_setting_{$key}", 60, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            if ($setting->type === 'boolean') {
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            }

            if ($setting->type === 'integer') {
                return (int)$setting->value;
            }

            if ($setting->type === 'json') {
                return json_decode($setting->value, true);
            }

            return $setting->value;
        });
    }

    /**
     * Simpan / Perbarui nilai konfigurasi
     */
    public static function set(string $key, $value, ?string $description = null, string $type = 'string', string $group = 'system'): self
    {
        $stringValue = is_bool($value) ? ($value ? '1' : '0') : (is_array($value) ? json_encode($value) : (string)$value);

        $existing = static::where('key', $key)->first();

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
                'group' => $group,
                'description' => $description ?? ($existing ? $existing->description : null),
            ]
        );

        Cache::forget("app_setting_{$key}");

        return $setting;
    }

    /**
     * Cek apakah Mode Pengembang sedang Aktif (ON)
     */
    public static function isDevMode(): bool
    {
        return (bool)static::get('developer_mode', false);
    }

    /**
     * Aktifkan atau Nonaktifkan Mode Pengembang
     */
    public static function setDevMode(bool $enabled): void
    {
        static::updateOrCreate(
            ['key' => 'developer_mode'],
            [
                'value' => $enabled ? '1' : '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Mode Pengembang: Jika aktif (ON), seluruh produk tidak dapat dibeli dan transaksi pembelian dinonaktifkan sementara.',
            ]
        );

        Cache::forget('app_setting_developer_mode');
    }
}
