<?php

namespace App\Livewire\SystemManagement;

use App\Models\Core\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Settings',
    'description' => 'Manage your account settings and preferences',
    'icon' => 'fas fa-cog',
    'active' => 'settings'
])]
class Settings extends Component
{
    use WithFileUploads;

    // Profile fields
    public $name;
    public $email;
    public $bio;
    public $profile_picture;
    public $social_links = [];

    // Notification preferences
    public $receive_course_updates = true;
    public $receive_certificate_notifications = true;

    // Security fields
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Active tab
    public $activeTab = 'profile';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->bio = $user->bio;
        $this->social_links = $user->social_links ?? [];
        // Load notification preferences (assuming stored in user meta or DB)
        $this->receive_course_updates = $user->receive_course_updates ?? true;
        $this->receive_certificate_notifications = $user->receive_certificate_notifications ?? true;
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . Auth::id(),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'], // 2MB max
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_links.github' => ['nullable', 'url', 'max:255'],
            'receive_course_updates' => ['boolean'],
            'receive_certificate_notifications' => ['boolean'],
            'current_password' => ['required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required_with:new_password', 'string', 'min:8'],
        ];
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url', 'max:255'],
            'social_links.github' => ['nullable', 'url', 'max:255'],
        ]);

        $user = Auth::user();
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'bio' => $this->bio,
            'social_links' => array_filter($this->social_links), // Remove empty links
        ];

        if ($this->profile_picture) {
            $path = $this->profile_picture->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        $user->update($data);
        $user->logCustomActivity('Updated profile settings');

        $this->dispatch('notify', 'Profile updated successfully!', 'success');
    }

    public function saveNotificationPreferences()
    {
        $this->validate([
            'receive_course_updates' => ['boolean'],
            'receive_certificate_notifications' => ['boolean'],
        ]);

        $user = Auth::user();
        $user->update([
            'receive_course_updates' => $this->receive_course_updates,
            'receive_certificate_notifications' => $this->receive_certificate_notifications,
        ]);
        $user->logCustomActivity('Updated notification preferences');

        $this->dispatch('notify', 'Notification preferences updated successfully!', 'success');
    }

    public function savePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string', 'min:8'],
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($this->new_password)]);
        $user->logCustomActivity('Changed password');

        // Reset password fields
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('notify', 'Password updated successfully!', 'success');
    }

    public function render()
    {
        return view('livewire.system-management.settings');
    }
}
