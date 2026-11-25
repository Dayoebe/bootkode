<?php

namespace App\Livewire\SystemManagement;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', [
    'title' => 'Privacy Settings',
    'description' => 'Manage privacy controls that are enforced by BootKode',
    'icon' => 'fas fa-lock',
    'active' => 'privacy_settings',
])]
class PrivacySettings extends Component
{
    public string $profile_visibility = 'private';

    public bool $allow_course_messages = true;

    public bool $data_collection_consent = false;

    public bool $analytics_tracking = false;

    public bool $marketing_analytics = false;

    public bool $third_party_integrations = false;

    public function boot(): void
    {
        abort_unless(Auth::check(), 403);
    }

    public function mount(): void
    {
        $user = Auth::user();
        $privacy = is_array($user->privacy_settings) ? $user->privacy_settings : [];

        $this->profile_visibility = $privacy['profile_visibility'] ?? ($user->is_profile_public ? 'public' : 'private');
        $this->allow_course_messages = (bool) ($privacy['allow_course_messages'] ?? true);
        $this->data_collection_consent = (bool) ($privacy['data_collection_consent'] ?? false);
        $this->analytics_tracking = (bool) ($privacy['analytics_tracking'] ?? false);
        $this->marketing_analytics = (bool) ($privacy['marketing_analytics'] ?? false);
        $this->third_party_integrations = (bool) ($privacy['third_party_integrations'] ?? false);
    }

    public function saveProfilePrivacy(): void
    {
        $this->validate([
            'profile_visibility' => ['required', Rule::in(['public', 'private'])],
        ]);

        $this->savePrivacySettings(['profile_visibility' => $this->profile_visibility]);
        Auth::user()->update(['is_profile_public' => $this->profile_visibility === 'public']);

        $this->dispatch('notify', 'Profile visibility updated.', 'success');
    }

    public function saveCommunicationPrivacy(): void
    {
        $this->validate(['allow_course_messages' => ['boolean']]);
        $this->savePrivacySettings(['allow_course_messages' => $this->allow_course_messages]);

        $this->dispatch('notify', 'Course message preference updated.', 'success');
    }

    public function saveDataPrivacy(): void
    {
        $this->validate([
            'data_collection_consent' => ['boolean'],
            'analytics_tracking' => ['boolean'],
            'marketing_analytics' => ['boolean'],
            'third_party_integrations' => ['boolean'],
        ]);

        $this->savePrivacySettings([
            'data_collection_consent' => $this->data_collection_consent,
            'analytics_tracking' => $this->analytics_tracking,
            'marketing_analytics' => $this->marketing_analytics,
            'third_party_integrations' => $this->third_party_integrations,
        ]);

        $this->dispatch('notify', 'Analytics privacy choices updated.', 'success');
    }

    private function savePrivacySettings(array $settings): void
    {
        $user = Auth::user();
        $current = is_array($user->privacy_settings) ? $user->privacy_settings : [];

        $user->update(['privacy_settings' => array_merge($current, $settings)]);
        $user->logCustomActivity('Updated enforced privacy settings');
    }

    public function render()
    {
        return view('livewire.system-management.privacy-settings');
    }
}
