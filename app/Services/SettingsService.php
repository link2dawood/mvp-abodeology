<?php

namespace App\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache duration in minutes
     */
    protected const CACHE_DURATION = 60;

    /**
     * Get a setting value with caching
     */
    public function get(string $key, $default = null)
    {
        return Cache::remember(
            "setting.{$key}",
            now()->addMinutes(self::CACHE_DURATION),
            function () use ($key, $default) {
                return Settings::get($key, $default);
            }
        );
    }

    /**
     * Set a setting value and clear cache
     */
    public function set(string $key, $value, string $type = 'string', string $group = 'general', string $description = null): Settings
    {
        Cache::forget("setting.{$key}");
        Cache::forget("settings.group.{$group}");
        
        return Settings::set($key, $value, $type, $group, $description);
    }

    /**
     * Get all settings by group with caching
     */
    public function getByGroup(string $group): array
    {
        return Cache::remember(
            "settings.group.{$group}",
            now()->addMinutes(self::CACHE_DURATION),
            function () use ($group) {
                return Settings::getByGroup($group);
            }
        );
    }

    /**
     * Clear all settings cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Get logo URL
     */
    public function getLogoUrl(): string
    {
        return $this->get('logo_url', asset('media/abodeology-logo.png'));
    }

    /**
     * Set logo URL
     */
    public function setLogoUrl(string $url): Settings
    {
        return $this->set('logo_url', $url, 'string', 'branding', 'Company logo URL for email templates');
    }
}


