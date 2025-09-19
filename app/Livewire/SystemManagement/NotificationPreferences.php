<?php

namespace App\Livewire\SystemManagement;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Notification Preferences',
    'description' => 'Manage your notification and communication preferences',
    'icon' => 'fas fa-bell',
    'active' => 'notification_preferences'
])]
class NotificationPreferences extends Component
{
    // Email Notifications
    public $email_course_updates = true;
    public $email_certificate_notifications = true;
    public $email_assignment_reminders = true;
    public $email_announcement_notifications = true;
    public $email_system_updates = false;
    public $email_marketing_updates = false;
    public $email_weekly_digest = true;
    public $email_achievement_notifications = true;
    public $email_forum_replies = true;
    public $email_direct_messages = true;

    // Push Notifications (Web/Mobile)
    public $push_course_updates = true;
    public $push_certificate_notifications = true;
    public $push_assignment_reminders = true;
    public $push_announcement_notifications = false;
    public $push_achievement_notifications = true;
    public $push_forum_replies = false;
    public $push_direct_messages = true;

    // SMS Notifications
    public $sms_enabled = false;
    public $sms_emergency_only = true;
    public $sms_assignment_deadlines = false;
    public $sms_system_maintenance = false;

    // Frequency Settings
    public $digest_frequency = 'weekly'; // daily, weekly, monthly, never
    public $reminder_frequency = '24_hours'; // 1_hour, 6_hours, 24_hours, 3_days, never
    public $quiet_hours_start = '22:00';
    public $quiet_hours_end = '07:00';
    public $weekend_notifications = true;

    // Content Preferences
    public $preferred_language = 'en';
    public $notification_categories = [];
    public $blocked_senders = [];

    public function mount()
    {
        $user = Auth::user();
        $this->loadNotificationPreferences($user);
    }

    private function loadNotificationPreferences($user)
    {
        // Load from user preferences or database
        $preferences = $user->notification_preferences ?? [];

        // Email notifications
        $this->email_course_updates = $preferences['email_course_updates'] ?? $user->receive_course_updates ?? true;
        $this->email_certificate_notifications = $preferences['email_certificate_notifications'] ?? $user->receive_certificate_notifications ?? true;
        $this->email_assignment_reminders = $preferences['email_assignment_reminders'] ?? true;
        $this->email_announcement_notifications = $preferences['email_announcement_notifications'] ?? true;
        $this->email_system_updates = $preferences['email_system_updates'] ?? false;
        $this->email_marketing_updates = $preferences['email_marketing_updates'] ?? false;
        $this->email_weekly_digest = $preferences['email_weekly_digest'] ?? true;
        $this->email_achievement_notifications = $preferences['email_achievement_notifications'] ?? true;
        $this->email_forum_replies = $preferences['email_forum_replies'] ?? true;
        $this->email_direct_messages = $preferences['email_direct_messages'] ?? true;

        // Push notifications
        $this->push_course_updates = $preferences['push_course_updates'] ?? true;
        $this->push_certificate_notifications = $preferences['push_certificate_notifications'] ?? true;
        $this->push_assignment_reminders = $preferences['push_assignment_reminders'] ?? true;
        $this->push_announcement_notifications = $preferences['push_announcement_notifications'] ?? false;
        $this->push_achievement_notifications = $preferences['push_achievement_notifications'] ?? true;
        $this->push_forum_replies = $preferences['push_forum_replies'] ?? false;
        $this->push_direct_messages = $preferences['push_direct_messages'] ?? true;

        // SMS notifications
        $this->sms_enabled = $preferences['sms_enabled'] ?? false;
        $this->sms_emergency_only = $preferences['sms_emergency_only'] ?? true;
        $this->sms_assignment_deadlines = $preferences['sms_assignment_deadlines'] ?? false;
        $this->sms_system_maintenance = $preferences['sms_system_maintenance'] ?? false;

        // Frequency settings
        $this->digest_frequency = $preferences['digest_frequency'] ?? 'weekly';
        $this->reminder_frequency = $preferences['reminder_frequency'] ?? '24_hours';
        $this->quiet_hours_start = $preferences['quiet_hours_start'] ?? '22:00';
        $this->quiet_hours_end = $preferences['quiet_hours_end'] ?? '07:00';
        $this->weekend_notifications = $preferences['weekend_notifications'] ?? true;

        // Content preferences
        $this->preferred_language = $preferences['preferred_language'] ?? 'en';
        $this->notification_categories = $preferences['notification_categories'] ?? [];
        $this->blocked_senders = $preferences['blocked_senders'] ?? [];
    }

    public function saveEmailNotifications()
    {
        $this->savePreferences([
            'email_course_updates' => $this->email_course_updates,
            'email_certificate_notifications' => $this->email_certificate_notifications,
            'email_assignment_reminders' => $this->email_assignment_reminders,
            'email_announcement_notifications' => $this->email_announcement_notifications,
            'email_system_updates' => $this->email_system_updates,
            'email_marketing_updates' => $this->email_marketing_updates,
            'email_weekly_digest' => $this->email_weekly_digest,
            'email_achievement_notifications' => $this->email_achievement_notifications,
            'email_forum_replies' => $this->email_forum_replies,
            'email_direct_messages' => $this->email_direct_messages,
        ]);

        $this->dispatch('notify', 'Email notification preferences updated!', 'success');
    }

    public function savePushNotifications()
    {
        $this->savePreferences([
            'push_course_updates' => $this->push_course_updates,
            'push_certificate_notifications' => $this->push_certificate_notifications,
            'push_assignment_reminders' => $this->push_assignment_reminders,
            'push_announcement_notifications' => $this->push_announcement_notifications,
            'push_achievement_notifications' => $this->push_achievement_notifications,
            'push_forum_replies' => $this->push_forum_replies,
            'push_direct_messages' => $this->push_direct_messages,
        ]);

        $this->dispatch('notify', 'Push notification preferences updated!', 'success');
    }

    public function saveSmsNotifications()
    {
        $this->savePreferences([
            'sms_enabled' => $this->sms_enabled,
            'sms_emergency_only' => $this->sms_emergency_only,
            'sms_assignment_deadlines' => $this->sms_assignment_deadlines,
            'sms_system_maintenance' => $this->sms_system_maintenance,
        ]);

        $this->dispatch('notify', 'SMS notification preferences updated!', 'success');
    }

    public function saveFrequencySettings()
    {
        $this->validateOnly([
            'digest_frequency' => 'required|in:daily,weekly,monthly,never',
            'reminder_frequency' => 'required|in:1_hour,6_hours,24_hours,3_days,never',
            'quiet_hours_start' => 'required|date_format:H:i',
            'quiet_hours_end' => 'required|date_format:H:i',
        ]);

        $this->savePreferences([
            'digest_frequency' => $this->digest_frequency,
            'reminder_frequency' => $this->reminder_frequency,
            'quiet_hours_start' => $this->quiet_hours_start,
            'quiet_hours_end' => $this->quiet_hours_end,
            'weekend_notifications' => $this->weekend_notifications,
        ]);

        $this->dispatch('notify', 'Frequency settings updated!', 'success');
    }

    public function saveContentPreferences()
    {
        $this->validateOnly([
            'preferred_language' => 'required|in:en,es,fr,de,it,pt,ar',
        ]);

        $this->savePreferences([
            'preferred_language' => $this->preferred_language,
            'notification_categories' => $this->notification_categories,
            'blocked_senders' => $this->blocked_senders,
        ]);

        $this->dispatch('notify', 'Content preferences updated!', 'success');
    }

    private function savePreferences($preferences)
    {
        $user = Auth::user();
        $currentPreferences = $user->notification_preferences ?? [];
        $updatedPreferences = array_merge($currentPreferences, $preferences);

        $user->update([
            'notification_preferences' => $updatedPreferences,
            // Update legacy fields for backwards compatibility
            'receive_course_updates' => $preferences['email_course_updates'] ?? $this->email_course_updates,
            'receive_certificate_notifications' => $preferences['email_certificate_notifications'] ?? $this->email_certificate_notifications,
        ]);

        $user->logCustomActivity('Updated notification preferences');
    }

    public function enableAllEmail()
    {
        $this->email_course_updates = true;
        $this->email_certificate_notifications = true;
        $this->email_assignment_reminders = true;
        $this->email_announcement_notifications = true;
        $this->email_system_updates = true;
        $this->email_marketing_updates = true;
        $this->email_weekly_digest = true;
        $this->email_achievement_notifications = true;
        $this->email_forum_replies = true;
        $this->email_direct_messages = true;
    }

    public function disableAllEmail()
    {
        $this->email_course_updates = false;
        $this->email_certificate_notifications = false;
        $this->email_assignment_reminders = false;
        $this->email_announcement_notifications = false;
        $this->email_system_updates = false;
        $this->email_marketing_updates = false;
        $this->email_weekly_digest = false;
        $this->email_achievement_notifications = false;
        $this->email_forum_replies = false;
        $this->email_direct_messages = false;
    }

    public function enableAllPush()
    {
        $this->push_course_updates = true;
        $this->push_certificate_notifications = true;
        $this->push_assignment_reminders = true;
        $this->push_announcement_notifications = true;
        $this->push_achievement_notifications = true;
        $this->push_forum_replies = true;
        $this->push_direct_messages = true;
    }

    public function disableAllPush()
    {
        $this->push_course_updates = false;
        $this->push_certificate_notifications = false;
        $this->push_assignment_reminders = false;
        $this->push_announcement_notifications = false;
        $this->push_achievement_notifications = false;
        $this->push_forum_replies = false;
        $this->push_direct_messages = false;
    }

    public function testNotification($type)
    {
        // Send a test notification
        $user = Auth::user();
        
        switch ($type) {
            case 'email':
                // Send test email notification
                $user->notify(new \App\Notifications\TestNotification('email'));
                $this->dispatch('notify', 'Test email sent!', 'success');
                break;
            case 'push':
                // Send test push notification
                $user->notify(new \App\Notifications\TestNotification('push'));
                $this->dispatch('notify', 'Test push notification sent!', 'success');
                break;
            case 'sms':
                if ($user->phone_number) {
                    // Send test SMS
                    $user->notify(new \App\Notifications\TestNotification('sms'));
                    $this->dispatch('notify', 'Test SMS sent!', 'success');
                } else {
                    $this->dispatch('notify', 'Please add a phone number to receive SMS notifications.', 'error');
                }
                break;
        }
    }

    public function getDigestFrequencyOptions()
    {
        return [
            'never' => 'Never',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }

    public function getReminderFrequencyOptions()
    {
        return [
            'never' => 'Never',
            '1_hour' => '1 hour before',
            '6_hours' => '6 hours before',
            '24_hours' => '24 hours before',
            '3_days' => '3 days before',
        ];
    }

    public function getLanguageOptions()
    {
        return [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ar' => 'Arabic',
        ];
    }

    public function render()
    {
        return view('livewire.system-management.notification-preferences');
    }
}