<?php

namespace App\Livewire\SystemManagement;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Profile Settings',
    'description' => 'Manage your personal profile information and preferences',
    'icon' => 'fas fa-user-cog',
    'active' => 'profile_settings'
])]
class ProfileSettings extends Component
{
    use WithFileUploads;

    // Basic Information
    public $name;
    public $email;
    public $phone_number;
    public $date_of_birth;
    public $bio;
    public $occupation;
    public $education_level;
    
    // Profile Image
    public $profile_picture;
    public $new_profile_picture;
    
    // Address Information
    public $address_street;
    public $address_city;
    public $address_state;
    public $address_country;
    public $address_postal_code;
    
    // Professional Information
    public $skills = [];
    public $website;
    public $linkedin;
    public $github;
    public $twitter;
    
    // Settings
    public $timezone;
    public $language = 'en';
    public $is_profile_public = true;
    public $show_email_publicly = false;
    public $show_phone_publicly = false;
    
    // Tab management
    public $activeTab = 'basic';

    protected $listeners = ['refreshProfile' => '$refresh'];

    public function mount()
    {
        $user = Auth::user();
        $this->loadUserData($user);
    }

    private function loadUserData($user)
    {
        // Basic Information
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d');
        $this->bio = $user->bio;
        $this->occupation = $user->occupation;
        $this->education_level = $user->education_level;
        
        // Profile Image
        $this->profile_picture = $user->profile_picture;
        
        // Address
        $this->address_street = $user->address_street;
        $this->address_city = $user->address_city;
        $this->address_state = $user->address_state;
        $this->address_country = $user->address_country;
        $this->address_postal_code = $user->address_postal_code;
        
        // Professional
        $this->skills = is_string($user->skills) ? explode(',', $user->skills) : ($user->skills ?? []);
        
        // Social Links
        $socialLinks = $user->social_links ?? [];
        $this->website = $socialLinks['website'] ?? '';
        $this->linkedin = $socialLinks['linkedin'] ?? '';
        $this->github = $socialLinks['github'] ?? '';
        $this->twitter = $socialLinks['twitter'] ?? '';
        
        // Settings
        $this->timezone = $user->timezone ?? 'UTC';
        $this->language = $user->language ?? 'en';
        $this->is_profile_public = $user->is_profile_public ?? true;
        $this->show_email_publicly = $user->show_email_publicly ?? false;
        $this->show_phone_publicly = $user->show_phone_publicly ?? false;
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
                Rule::unique('users')->ignore(Auth::id()),
            ],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'occupation' => ['nullable', 'string', 'max:100'],
            'education_level' => ['nullable', 'string', 'max:100'],
            'new_profile_picture' => ['nullable', 'image', 'max:2048'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:100'],
            'address_state' => ['nullable', 'string', 'max:100'],
            'address_country' => ['nullable', 'string', 'max:100'],
            'address_postal_code' => ['nullable', 'string', 'max:20'],
            'skills.*' => ['string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
            'timezone' => ['required', 'string', 'in:' . implode(',', timezone_identifiers_list())],
            'language' => ['required', 'string', 'in:en,es,fr,de,it,pt,ar'],
            'is_profile_public' => ['boolean'],
            'show_email_publicly' => ['boolean'],
            'show_phone_publicly' => ['boolean'],
        ];
    }

    public function saveBasicInfo()
    {
        $this->validateOnly([
            'name', 'email', 'phone_number', 'date_of_birth', 
            'bio', 'occupation', 'education_level'
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'date_of_birth' => $this->date_of_birth,
            'bio' => $this->bio,
            'occupation' => $this->occupation,
            'education_level' => $this->education_level,
        ]);

        $user->logCustomActivity('Updated basic profile information');
        $this->dispatch('notify', 'Basic information updated successfully!', 'success');
    }

    public function saveAddress()
    {
        $this->validateOnly([
            'address_street', 'address_city', 'address_state', 
            'address_country', 'address_postal_code'
        ]);

        $user = Auth::user();
        $user->update([
            'address_street' => $this->address_street,
            'address_city' => $this->address_city,
            'address_state' => $this->address_state,
            'address_country' => $this->address_country,
            'address_postal_code' => $this->address_postal_code,
        ]);

        $user->logCustomActivity('Updated address information');
        $this->dispatch('notify', 'Address information updated successfully!', 'success');
    }

    public function saveProfessionalInfo()
    {
        $this->validateOnly([
            'skills.*', 'website', 'linkedin', 'github', 'twitter'
        ]);

        $user = Auth::user();
        
        // Prepare skills data
        $skillsString = is_array($this->skills) ? implode(',', array_filter($this->skills)) : '';
        
        // Prepare social links
        $socialLinks = array_filter([
            'website' => $this->website,
            'linkedin' => $this->linkedin,
            'github' => $this->github,
            'twitter' => $this->twitter,
        ]);

        $user->update([
            'skills' => $skillsString,
            'social_links' => $socialLinks,
        ]);

        $user->logCustomActivity('Updated professional information');
        $this->dispatch('notify', 'Professional information updated successfully!', 'success');
    }

    public function saveProfileSettings()
    {
        $this->validateOnly([
            'timezone', 'language', 'is_profile_public', 
            'show_email_publicly', 'show_phone_publicly'
        ]);

        $user = Auth::user();
        $user->update([
            'timezone' => $this->timezone,
            'language' => $this->language,
            'is_profile_public' => $this->is_profile_public,
            'show_email_publicly' => $this->show_email_publicly,
            'show_phone_publicly' => $this->show_phone_publicly,
        ]);

        $user->logCustomActivity('Updated profile settings');
        $this->dispatch('notify', 'Profile settings updated successfully!', 'success');
    }

    public function updateProfilePicture()
    {
        $this->validateOnly(['new_profile_picture']);

        if ($this->new_profile_picture) {
            $user = Auth::user();
            
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $path = $this->new_profile_picture->store('profile_pictures', 'public');
            
            $user->update(['profile_picture' => $path]);
            $this->profile_picture = $path;
            $this->new_profile_picture = null;

            $user->logCustomActivity('Updated profile picture');
            $this->dispatch('notify', 'Profile picture updated successfully!', 'success');
        }
    }

    public function removeProfilePicture()
    {
        $user = Auth::user();
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->update(['profile_picture' => null]);
            $this->profile_picture = null;

            $user->logCustomActivity('Removed profile picture');
            $this->dispatch('notify', 'Profile picture removed successfully!', 'success');
        }
    }

    public function addSkill()
    {
        if (count($this->skills) < 20) {
            $this->skills[] = '';
        }
    }

    public function removeSkill($index)
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    public function getEducationLevels()
    {
        return [
            'high_school' => 'High School',
            'bachelor' => "Bachelor's Degree",
            'master' => "Master's Degree",
            'phd' => 'PhD/Doctorate',
            'professional' => 'Professional Certificate',
            'other' => 'Other',
        ];
    }

    public function getTimezones()
    {
        $timezones = [];
        foreach (timezone_identifiers_list() as $timezone) {
            $timezones[$timezone] = $timezone;
        }
        return $timezones;
    }

    public function getLanguages()
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

    public function getCountries()
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'AU' => 'Australia',
            'DE' => 'Germany',
            'FR' => 'France',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'NG' => 'Nigeria',
            'ZA' => 'South Africa',
            'KE' => 'Kenya',
            'GH' => 'Ghana',
            'EG' => 'Egypt',
            'IN' => 'India',
            'JP' => 'Japan',
            'CN' => 'China',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.system-management.profile-settings');
    }
}