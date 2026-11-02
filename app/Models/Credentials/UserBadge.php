<?php

namespace App\Models\Credentials;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'badge_type', 'badge_key', 'badge_name', 'badge_description',
        'badge_icon', 'badge_color', 'rarity', 'points_reward', 'is_featured', 'unlock_criteria',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'unlock_criteria' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function awardBadge($userId, $badgeKey, $badgeData)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($userId, $badgeKey, $badgeData) {
            $user = User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $badge = self::firstOrCreate(
                ['user_id' => $userId, 'badge_key' => $badgeKey],
                array_merge($badgeData, ['user_id' => $userId, 'badge_key' => $badgeKey])
            );
            if ($badge->wasRecentlyCreated) {
                app(\App\Services\LearningNotificationService::class)->achievement($user, $badge);
            }

            return $badge;
        });
    }

    public function getRarityColorAttribute()
    {
        return match ($this->rarity) {
            'common' => '#64748B',
            'rare' => '#3B82F6',
            'epic' => '#8B5CF6',
            'legendary' => '#F59E0B',
            default => '#64748B'
        };
    }
}
