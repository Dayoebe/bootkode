<?php

namespace App\Notifications;

use App\Models\Assessment\Assessment;
use App\Services\CbtReminderService;
use App\Services\PortalNotificationService;

// Compatibility payload; actual delivery uses durable portal receipts and SendPortalNotificationEmail.
class CbtExamReminder extends PortalEventNotification
{
    public function __construct(Assessment $exam, string $reminderType = 'upcoming')
    {
        parent::__construct(['type' => 'cbt_reminder', 'title' => 'CBT deadline reminder', 'message' => 'An available course quiz has a scheduled deadline.',
            'action_url' => route('cbt.exam.take', ['assessment' => $exam->id]), 'assessment_id' => $exam->id, 'deadline' => app(CbtReminderService::class)->deadline($exam)]);
    }

    public function via($notifiable): array
    {
        $user = $notifiable->fresh();

        return $user && $user->shouldReceiveEmailNotification('cbt_reminder') && app(PortalNotificationService::class)->emailAvailable()
            && app(CbtReminderService::class)->eligible($user, Assessment::find($this->payload['assessment_id']), $this->payload['deadline']) ? ['mail'] : [];
    }
}
