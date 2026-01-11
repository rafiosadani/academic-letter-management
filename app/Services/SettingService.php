<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    /**
     * Cache key prefix
     */
    private const CACHE_PREFIX = 'setting.';

    /**
     * Cache duration (forever until manually cleared)
     */
    private const CACHE_TTL = null;

    /**
     * Get setting value by key
     */
    public function get(string $key, $default = null)
    {
        return Cache::rememberForever(self::CACHE_PREFIX . $key, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     */
    public function set(string $key, $value): bool
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        $setting->update(['value' => $value]);
        $this->clearCache($key);

        return true;
    }

    /**
     * Update multiple settings at once
     */
    public function updateMany(array $data): void
    {
        foreach ($data as $key => $value) {
            if ($value !== null) {
                Setting::where('key', $key)->update(['value' => $value]);
                $this->clearCache($key);
            }
        }
    }

    /**
     * Handle image upload for setting
     */
    public function uploadImage(string $key, $file): ?string
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting || $setting->type !== 'image') {
            return null;
        }

        // Delete old image if exists
        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        // Store new image
        $path = $file->store('settings', 'public');

        // Update setting
        $setting->update(['value' => $path]);
        $this->clearCache($key);

        return $path;
    }

    /**
     * Get all settings grouped by group
     */
    public function getAllGrouped()
    {
        return Cache::rememberForever('settings.all.grouped', function () {
            return Setting::orderBy('order')->get()->groupBy('group');
        });
    }

    /**
     * Clear cache for specific key
     */
    public function clearCache(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX . $key);
        Cache::forget('settings.all.grouped');
    }

    /**
     * Clear all settings cache
     */
    public function clearAllCache(): void
    {
        $keys = Setting::pluck('key');

        foreach ($keys as $key) {
            Cache::forget(self::CACHE_PREFIX . $key);
        }

        Cache::forget('settings.all.grouped');
    }
}