<?php

namespace App\Models\Credentials;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    public const FONTS = ['DejaVu Serif', 'DejaVu Sans'];

    public const DEFAULT_SETTINGS = [
        'backgroundColor' => '#ffffff',
        'borderColor' => '#0f172a',
        'textColor' => '#0f172a',
        'accentColor' => '#0f766e',
        'headerFont' => 'DejaVu Serif',
        'bodyFont' => 'DejaVu Sans',
    ];

    protected $fillable = ['name', 'description', 'settings', 'created_by'];

    protected $casts = ['settings' => 'array', 'default_slot' => 'integer'];

    public function getIsDefaultAttribute(): bool
    {
        return $this->default_slot === 1;
    }

    /** Only supported fonts and literal hex colors may enter HTML/PDF styles. */
    public static function safeSettings(array $settings): array
    {
        $safe = [];
        foreach (self::DEFAULT_SETTINGS as $key => $fallback) {
            $value = $settings[$key] ?? null;
            $valid = str_ends_with($key, 'Font')
                ? in_array($value, self::FONTS, true)
                : is_string($value) && preg_match('/^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/iD', $value);
            $safe[$key] = $valid ? $value : $fallback;
        }

        return $safe;
    }
}
