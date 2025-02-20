<?php

namespace App\Helpers;

use App\Models\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use App\Helpers\CacheConfig;
use Illuminate\Database\Eloquent\Model;

trait Settings
{

    private static function getSettings(): Model //Not show of Model as function return type
    {
        if (Cache::has(CacheConfig::GENERAL_SETTINGS_CACHE_KEY)) {
            return Cache::get(CacheConfig::GENERAL_SETTINGS_CACHE_KEY);
        }
        $settings = GeneralSettings::first();
        Cache::put(CacheConfig::GENERAL_SETTINGS_CACHE_KEY, $settings, CacheConfig::CACHE_TTL);
        return $settings;
    }

    public function getAdminFromEmail(): String
    {
        return self::getSettings()->admin_from_email;
    }

    public function getAdminFromName(): String
    {
        return self::getSettings()->admin_from_name;
    }
}
