<?php

use App\Services\SettingService;

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     */
    function setting(string $key, $default = null)
    {
        return app(SettingService::class)->get($key, $default);
    }
}