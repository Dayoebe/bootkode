<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlogSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('blog_settings');
        });

        static::deleted(function () {
            Cache::forget('blog_settings');
        });
    }

    public static function get($key, $default = null)
    {
        $settings = Cache::rememberForever('blog_settings', function () {
            return static::all()->pluck('value', 'key')->toArray();
        });

        $value = $settings[$key] ?? $default;

        // Cast value based on type
        $setting = static::where('key', $key)->first();
        if ($setting) {
            return match ($setting->type) {
                'boolean' => (bool) $value,
                'integer' => (int) $value,
                'json' => json_decode($value, true),
                default => $value
            };
        }

        return $value;
    }

    public static function set($key, $value, $type = 'text', $group = 'general')
    {
        if (is_array($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        }

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]
        );
    }

    public static function getGroup($group)
    {
        return Cache::rememberForever("blog_settings_{$group}", function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }
}

