<?php

namespace App\Livewire\Component\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'All Courses', 'description' => 'Manage all courses including creation, editing, and deletion', 'icon' => 'fas fa-book', 'active' => 'admin.all-courses'])]

class EditProfile extends Component
{
    use WithFileUploads;

    // Personal Info
    public $name;
    public $email;
    public $phone_number;
    public $date_of_birth;
    public $bio;

    // Address
    public $address_street;
    public $address_city;
    public $address_state;
    public $address_country;
    public $address_postal_code;

    // Education & Career
    public $occupation;
    public $education_level;
    public $skills;

    // Profile Picture
    public $profile_picture;
    public $temp_profile_picture;

    // Social Links
    public $social_links = [
        'twitter' => '',
        'facebook' => '',
        'linkedin' => '',
        'github' => '',
        'instagram' => '',
        'website' => ''
    ];

    public $activeTab = 'basic';

    public function mount()
    {
        $user = Auth::user();
        
        // Personal Info
        $this->fill($user->only([
            'name', 'email', 'phone_number', 'bio',
            'occupation', 'education_level', 'address_street',
            'address_city', 'address_state', 'address_country',
            'address_postal_code', 'skills'
        ]));
        
        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d');
        
        if ($user->social_links) {
            $this->social_links = array_merge($this->social_links, $user->social_links);
        }
    }

    protected function rules()
    {
        $userId = Auth::id();

        return [
            // Basic Info
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'phone_number' => 'nullable|string|max:20|regex:/^[\d\s\-+]+$/',
            'date_of_birth' => 'nullable|date|before:-13 years',
            'bio' => 'nullable|string|max:500',

            // Address
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_state' => 'nullable|string|max:100',
            'address_country' => 'nullable|string|max:100',
            'address_postal_code' => 'nullable|string|max:20',

            // Education & Career
            'occupation' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'skills' => 'nullable|string|max:255',

            // Profile Picture
            'profile_picture' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif',

            // Social Links
            'social_links.twitter' => 'nullable|url|starts_with:https://twitter.com,https://x.com',
            'social_links.facebook' => 'nullable|url|starts_with:https://facebook.com',
            'social_links.linkedin' => 'nullable|url|starts_with:https://linkedin.com',
            'social_links.github' => 'nullable|url|starts_with:https://github.com',
            'social_links.instagram' => 'nullable|url|starts_with:https://instagram.com',
            'social_links.website' => 'nullable|url',
        ];
    }

    public function updatedProfilePicture()
    {
        $this->validateOnly('profile_picture');
        $this->temp_profile_picture = $this->profile_picture->temporaryUrl();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $this->validate();

        $user = Auth::user();
        $updateData = $this->getUpdateData();

        // Handle profile picture upload
        if ($this->profile_picture) {
            // Delete old picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            $path = $this->profile_picture->store('profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        $user->update($updateData);

        // Handle email verification if email changed
        if ($user->email !== $this->email) {
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        }

        $this->dispatch('notify', 'Profile updated successfully!');
        return redirect()->route('profile.view');
    }

    protected function getUpdateData()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'bio' => $this->bio,
            'occupation' => $this->occupation,
            'education_level' => $this->education_level,
            'address_street' => $this->address_street,
            'address_city' => $this->address_city,
            'address_state' => $this->address_state,
            'address_country' => $this->address_country,
            'address_postal_code' => $this->address_postal_code,
            'skills' => $this->skills,
            'social_links' => array_filter($this->social_links),
        ];
    }

    public function deleteProfilePicture()
    {
        $user = Auth::user();
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
        }

        $this->reset('temp_profile_picture', 'profile_picture');
        $this->dispatch('notify', 'Profile picture removed successfully!');
    }

    public function render()
    {
        return view('livewire.component.profile.edit', [
            'user' => Auth::user()
        ])->layout('layouts.dashboard', [
            'title' => 'Edit Profile'
        ]);
    }
}