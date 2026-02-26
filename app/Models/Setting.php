<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return $default;
        }
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return false;
        }
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
