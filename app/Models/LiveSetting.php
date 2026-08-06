<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Live video settings (LiveKit). Kept table/key compatibility with legacy jitsi_* keys.
 */
class LiveSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    /**
     * Normalize a host: strip protocol and trailing slash.
     */
    public static function normalizeJitsiDomain(string $domain): string
    {
        $domain = trim($domain);
        $domain = preg_replace('#^https?://#i', '', $domain);
        $domain = rtrim($domain, '/');

        return $domain;
    }

    /**
     * @deprecated Use LiveKit public host / config('livekit.url') instead.
     */
    public static function getJitsiDomain(): string
    {
        return static::getLiveKitHost();
    }

    /**
     * Public hostname for LiveKit (e.g. live.sanaedu.com).
     */
    public static function getLiveKitHost(): string
    {
        $fromConfig = (string) config('livekit.public_url', '');
        if ($fromConfig !== '') {
            return static::normalizeJitsiDomain($fromConfig);
        }

        $domain = trim((string) static::get('livekit_domain', ''));
        if ($domain === '') {
            $domain = trim((string) static::get('jitsi_domain', ''));
        }
        if ($domain !== '') {
            return static::normalizeJitsiDomain($domain);
        }

        $server = LiveServer::where('status', 'active')->whereIn('provider', ['livekit', 'jitsi', 'custom'])->first();
        if ($server && trim($server->domain) !== '') {
            return static::normalizeJitsiDomain($server->domain);
        }

        return 'live.sanaedu.com';
    }

    public static function getLiveKitWsUrl(): string
    {
        $url = trim((string) config('livekit.url', ''));
        if ($url !== '') {
            return $url;
        }

        return 'wss://'.static::getLiveKitHost();
    }

    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("live_setting_{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );
        Cache::forget("live_setting_{$key}");
    }

    public static function getByGroup(string $group)
    {
        return static::where('group', $group)->get();
    }
}
