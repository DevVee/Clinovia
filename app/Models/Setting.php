<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    private const CACHE_KEY = 'sscms_settings_all';
    private const CACHE_TTL = 600; // 10 minutes

    // ─── Cache Helpers ────────────────────────────────────────────────────────

    /** Load all settings into a keyed collection, cached. */
    private static function all_cached(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::all()->keyBy('key');
        });
    }

    /** Bust the settings cache — called on every write. */
    public static function bustCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ─── Static API ───────────────────────────────────────────────────────────

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::all_cached()->get($key);

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );

        static::bustCache();
    }

    public static function group(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('group', $group)->orderBy('key')->get();
    }
}
