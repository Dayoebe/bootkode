<?php

namespace App\Notifications;

use App\Models\Credentials\UserAchievement;
use App\Models\Credentials\UserBadge;
use App\Services\LearningNotificationService;
use App\Services\PortalNotificationService;

class CbtAchievementUnlocked extends PortalEventNotification
{
    public function __construct(UserAchievement|UserBadge $achievement)
    {
        parent::__construct(['type' => 'achievement_unlocked', 'title' => 'Learning achievement recorded', 'message' => 'A learning achievement has been recorded in your private rewards history.',
            'action_url' => route('gamification.badges'), 'achievement_kind' => $achievement instanceof UserBadge ? 'badge' : 'achievement', 'achievement_id' => $achievement->id]);
    }

    public function via($notifiable): array
    {
        $user = $notifiable->fresh();

        return $user && $user->is_active && $user->email_verified_at && $user->shouldReceiveEmailNotification('achievement') && app(PortalNotificationService::class)->emailAvailable()
            && app(LearningNotificationService::class)->sourceAvailable($user, $this->payload) ? ['mail'] : [];
    }
}
