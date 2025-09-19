<?php

namespace App\Livewire\SystemManagement;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard', [
    'title' => 'Privacy Settings',
    'description' => 'Manage your privacy, security, and data preferences',
    'icon' => 'fas fa-lock',
    'active' => 'privacy_settings'
])]
class PrivacySettings extends Component
{
    // Profile Privacy
    public $profile_visibility = 'public'; // public, private, restricted
    public $show_online_status = true;
    public $show_last_seen = false;
    public $show_learning_progress = true;
    public $show_achievements = true;
    public $show_courses_enrolled = true;
    public $show_contact_info = false;
    public $searchable_profile = true;
    
    // Communication Privacy
    public $allow_direct_messages = 'everyone'; // everyone, connections, none
    public $allow_course_messages = true;
    public $allow_instructor_contact = true;
    public $show_email_to_instructors = false;
    public $allow_peer_collaboration = true;
    
    // Data & Analytics
    public $data_collection_consent = true;
    public $analytics_tracking = true;
    public $personalized_recommendations = true;
    public $marketing_analytics = false;
    public $third_party_integrations = true;
    public $cookie_preferences = [];
    
    // Account Security
    public $two_factor_enabled = false;
    public $login_notifications = true;
    public $suspicious_activity_alerts = true;
    public $password_change_notifications = true;
    public $data_export_notifications = true;
    
    // Session Management
    public $active_sessions = [];
    public $remember_me_enabled = true;
    public $auto_logout_time = 1440; // minutes (24 hours)
    public $session_notifications = false;
    
    // Data Rights
    public $data_retention_period = 'indefinite'; // 1_year, 2_years, 5_years, indefinite
    public $marketing_communications = false;
    public $data_sharing_consent = false;
    public $research_participation = false;

    protected $listeners = ['refreshPrivacySettings' => '$refresh'];

    public function mount()
    {
        $user = Auth::user();
        $this->loadPrivacySettings($user);
        $this->loadActiveSessions();
    }

    private function loadPrivacySettings($user)
    {
        $privacy = $user->privacy_settings ?? [];

        // Profile Privacy
        $this->profile_visibility = $privacy['profile_visibility'] ?? ($user->is_profile_public ? 'public' : 'private');
        $this->show_online_status = $privacy['show_online_status'] ?? true;
        $this->show_last_seen = $privacy['show_last_seen'] ?? false;
        $this->show_learning_progress = $privacy['show_learning_progress'] ?? true;
        $this->show_achievements = $privacy['show_achievements'] ?? true;
        $this->show_courses_enrolled = $privacy['show_courses_enrolled'] ?? true;
        $this->show_contact_info = $privacy['show_contact_info'] ?? ($user->show_email_publicly || $user->show_phone_publicly);
        $this->searchable_profile = $privacy['searchable_profile'] ?? true;
        
        // Communication Privacy
        $this->allow_direct_messages = $privacy['allow_direct_messages'] ?? 'everyone';
        $this->allow_course_messages = $privacy['allow_course_messages'] ?? true;
        $this->allow_instructor_contact = $privacy['allow_instructor_contact'] ?? true;
        $this->show_email_to_instructors = $privacy['show_email_to_instructors'] ?? false;
        $this->allow_peer_collaboration = $privacy['allow_peer_collaboration'] ?? true;
        
        // Data & Analytics
        $this->data_collection_consent = $privacy['data_collection_consent'] ?? true;
        $this->analytics_tracking = $privacy['analytics_tracking'] ?? true;
        $this->personalized_recommendations = $privacy['personalized_recommendations'] ?? true;
        $this->marketing_analytics = $privacy['marketing_analytics'] ?? false;
        $this->third_party_integrations = $privacy['third_party_integrations'] ?? true;
        $this->cookie_preferences = $privacy['cookie_preferences'] ?? [];
        
        // Account Security
        $this->two_factor_enabled = $privacy['two_factor_enabled'] ?? false;
        $this->login_notifications = $privacy['login_notifications'] ?? true;
        $this->suspicious_activity_alerts = $privacy['suspicious_activity_alerts'] ?? true;
        $this->password_change_notifications = $privacy['password_change_notifications'] ?? true;
        $this->data_export_notifications = $privacy['data_export_notifications'] ?? true;
        
        // Session Management
        $this->remember_me_enabled = $privacy['remember_me_enabled'] ?? true;
        $this->auto_logout_time = $privacy['auto_logout_time'] ?? 1440;
        $this->session_notifications = $privacy['session_notifications'] ?? false;
        
        // Data Rights
        $this->data_retention_period = $privacy['data_retention_period'] ?? 'indefinite';
        $this->marketing_communications = $privacy['marketing_communications'] ?? false;
        $this->data_sharing_consent = $privacy['data_sharing_consent'] ?? false;
        $this->research_participation = $privacy['research_participation'] ?? false;
    }

    private function loadActiveSessions()
    {
        // This would load active sessions from database
        // For now, we'll create mock data
        $this->active_sessions = [
            [
                'id' => 'current',
                'device' => 'Current Session',
                'browser' => request()->header('User-Agent'),
                'ip_address' => request()->ip(),
                'location' => 'Current Location',
                'last_activity' => now(),
                'is_current' => true,
            ]
        ];
    }

    public function saveProfilePrivacy()
    {
        $this->savePrivacySettings([
            'profile_visibility' => $this->profile_visibility,
            'show_online_status' => $this->show_online_status,
            'show_last_seen' => $this->show_last_seen,
            'show_learning_progress' => $this->show_learning_progress,
            'show_achievements' => $this->show_achievements,
            'show_courses_enrolled' => $this->show_courses_enrolled,
            'show_contact_info' => $this->show_contact_info,
            'searchable_profile' => $this->searchable_profile,
        ]);

        // Update legacy fields
        Auth::user()->update([
            'is_profile_public' => $this->profile_visibility === 'public',
        ]);

        $this->dispatch('notify', 'Profile privacy settings updated!', 'success');
    }

    public function saveCommunicationPrivacy()
    {
        $this->savePrivacySettings([
            'allow_direct_messages' => $this->allow_direct_messages,
            'allow_course_messages' => $this->allow_course_messages,
            'allow_instructor_contact' => $this->allow_instructor_contact,
            'show_email_to_instructors' => $this->show_email_to_instructors,
            'allow_peer_collaboration' => $this->allow_peer_collaboration,
        ]);

        $this->dispatch('notify', 'Communication privacy settings updated!', 'success');
    }

    public function saveDataPrivacy()
    {
        $this->savePrivacySettings([
            'data_collection_consent' => $this->data_collection_consent,
            'analytics_tracking' => $this->analytics_tracking,
            'personalized_recommendations' => $this->personalized_recommendations,
            'marketing_analytics' => $this->marketing_analytics,
            'third_party_integrations' => $this->third_party_integrations,
            'cookie_preferences' => $this->cookie_preferences,
        ]);

        $this->dispatch('notify', 'Data privacy settings updated!', 'success');
    }

    public function saveSecuritySettings()
    {
        $this->savePrivacySettings([
            'two_factor_enabled' => $this->two_factor_enabled,
            'login_notifications' => $this->login_notifications,
            'suspicious_activity_alerts' => $this->suspicious_activity_alerts,
            'password_change_notifications' => $this->password_change_notifications,
            'data_export_notifications' => $this->data_export_notifications,
        ]);

        $this->dispatch('notify', 'Security settings updated!', 'success');
    }

    public function saveSessionSettings()
    {
        $this->savePrivacySettings([
            'remember_me_enabled' => $this->remember_me_enabled,
            'auto_logout_time' => $this->auto_logout_time,
            'session_notifications' => $this->session_notifications,
        ]);

        $this->dispatch('notify', 'Session settings updated!', 'success');
    }

    public function saveDataRights()
    {
        $this->savePrivacySettings([
            'data_retention_period' => $this->data_retention_period,
            'marketing_communications' => $this->marketing_communications,
            'data_sharing_consent' => $this->data_sharing_consent,
            'research_participation' => $this->research_participation,
        ]);

        $this->dispatch('notify', 'Data rights settings updated!', 'success');
    }

    private function savePrivacySettings($settings)
    {
        $user = Auth::user();
        $currentSettings = $user->privacy_settings ?? [];
        $updatedSettings = array_merge($currentSettings, $settings);

        $user->update(['privacy_settings' => $updatedSettings]);
        $user->logCustomActivity('Updated privacy settings');
    }

    public function enable2FA()
    {
        // This would typically generate a QR code and secret key
        $this->two_factor_enabled = true;
        $this->dispatch('notify', 'Two-factor authentication setup initiated. Please scan the QR code with your authenticator app.', 'info');
    }

    public function disable2FA()
    {
        $this->two_factor_enabled = false;
        $this->saveSecuritySettings();
        $this->dispatch('notify', 'Two-factor authentication disabled.', 'success');
    }

    public function terminateSession($sessionId)
    {
        if ($sessionId === 'current') {
            $this->dispatch('notify', 'Cannot terminate current session.', 'error');
            return;
        }

        // Remove session from active sessions
        $this->active_sessions = array_filter($this->active_sessions, function($session) use ($sessionId) {
            return $session['id'] !== $sessionId;
        });

        $this->dispatch('notify', 'Session terminated successfully.', 'success');
    }

    public function terminateAllOtherSessions()
    {
        // Keep only current session
        $this->active_sessions = array_filter($this->active_sessions, function($session) {
            return $session['is_current'] ?? false;
        });

        $this->dispatch('notify', 'All other sessions terminated.', 'success');
    }

    public function exportData()
    {
        // This would trigger a data export job
        $user = Auth::user();
        $user->logCustomActivity('Requested data export');
        
        $this->dispatch('notify', 'Data export request submitted. You will receive an email when your data is ready for download.', 'info');
    }

    public function deleteAccount()
    {
        // This would be handled with additional confirmation
        $this->dispatch('confirm', 'Are you absolutely sure you want to delete your account? This action cannot be undone and all your data will be permanently removed.');
    }

    public function getProfileVisibilityOptions()
    {
        return [
            'public' => 'Public - Anyone can view your profile',
            'restricted' => 'Restricted - Only registered users can view',
            'private' => 'Private - Only you can view your profile',
        ];
    }

    public function getDirectMessageOptions()
    {
        return [
            'everyone' => 'Everyone can message me',
            'connections' => 'Only my connections can message me',
            'instructors' => 'Only instructors can message me',
            'none' => 'No one can message me',
        ];
    }

    public function getRetentionPeriodOptions()
    {
        return [
            '1_year' => 'Delete my data after 1 year of inactivity',
            '2_years' => 'Delete my data after 2 years of inactivity',
            '5_years' => 'Delete my data after 5 years of inactivity',
            'indefinite' => 'Keep my data indefinitely',
        ];
    }

    public function getAutoLogoutOptions()
    {
        return [
            '60' => '1 hour',
            '240' => '4 hours',
            '480' => '8 hours',
            '1440' => '24 hours',
            '2880' => '2 days',
            '10080' => '7 days',
            '0' => 'Never',
        ];
    }

    public function render()
    {
        return view('livewire.system-management.privacy-settings');
    }
}