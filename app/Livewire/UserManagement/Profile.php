<?php

namespace App\Livewire\UserManagement;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use App\Models\Course;
use App\Models\Lesson;
use Carbon\Carbon;

#[Layout('layouts.dashboard')]
class Profile extends Component
{
    use WithFileUploads;

    public $user;
    public $activeTab = 'personal';
    public $isEditing = false;
    
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

    protected $listeners = ['refresh' => '$refresh'];

    public function mount($mode = 'view')
    {
        $this->user = Auth::user();
        $this->isEditing = $mode === 'edit';
        
        if ($this->isEditing) {
            $this->loadUserData();
        }
    }

    public function loadUserData()
    {
        // Personal Info
        $this->fill($this->user->only([
            'name', 'email', 'phone_number', 'bio',
            'occupation', 'education_level', 'address_street',
            'address_city', 'address_state', 'address_country',
            'address_postal_code', 'skills'
        ]));
        
        $this->date_of_birth = $this->user->date_of_birth?->format('Y-m-d');
        
        if ($this->user->social_links) {
            $this->social_links = array_merge($this->social_links, $this->user->social_links);
        }
    }

    protected function rules()
    {
        if (!$this->isEditing) return [];

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
        if ($this->isEditing) {
            $this->validateOnly('profile_picture');
            $this->temp_profile_picture = $this->profile_picture->temporaryUrl();
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function toggleEditMode()
    {
        $this->isEditing = !$this->isEditing;
        
        if ($this->isEditing) {
            $this->loadUserData();
            $this->activeTab = 'basic';
        } else {
            $this->reset(['profile_picture', 'temp_profile_picture']);
        }
    }

    public function updateProfile()
    {
        if (!$this->isEditing) return;

        $this->validate();

        $updateData = $this->getUpdateData();

        // Handle profile picture upload
        if ($this->profile_picture) {
            // Delete old picture if exists
            if ($this->user->profile_picture) {
                Storage::disk('public')->delete($this->user->profile_picture);
            }
            
            $path = $this->profile_picture->store('profile-pictures', 'public');
            $updateData['profile_picture'] = $path;
        }

        $this->user->update($updateData);

        // Handle email verification if email changed
        if ($this->user->email !== $this->email) {
            $this->user->email_verified_at = null;
            $this->user->save();
            $this->user->sendEmailVerificationNotification();
        }

        $this->dispatch('notify', 'Profile updated successfully!');
        $this->isEditing = false;
        $this->activeTab = 'personal';
        $this->reset(['profile_picture', 'temp_profile_picture']);
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
        if ($this->user->profile_picture) {
            Storage::disk('public')->delete($this->user->profile_picture);
            $this->user->update(['profile_picture' => null]);
        }

        $this->reset('temp_profile_picture', 'profile_picture');
        $this->dispatch('notify', 'Profile picture removed successfully!');
    }

    public function getLearningProgressProperty()
    {
        return [
            'total_courses' => $this->user->courses()->count(),
            'completed_lessons' => $this->user->completedLessons()->count(),
            'wishlist_items' => $this->user->wishlists()->count(),
            'saved_resources' => $this->user->savedResources()->count(),
            'average_weekly_progress' => $this->calculateWeeklyAverage(),
            'downloaded_content' => $this->user->downloadedContent()->count(),
            'recent_activities' => $this->getRecentActivitiesProperty(),
            'wishlist' => $this->getWishlistProperty(),
            'activity_stats' => $this->getActivityStatsProperty(),
            'completed_assignments' => $this->user->completedLessons()->count(),
        ];
    }

    public function getActivityStatsProperty()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        return [
            'courses_enrolled' => [
                'total' => $this->user->courses()->count(),
                'this_month' => $this->user->courses()
                    ->where('course_user.created_at', '>=', $startOfMonth)
                    ->count(),
            ],
            'lessons_completed' => [
                'total' => $this->user->completedLessons()->count(),
                'this_month' => $this->user->completedLessons()
                    ->where('lesson_user.completed_at', '>=', $startOfMonth)
                    ->count(),
            ],
        ];
    }

    public function getRecentActivitiesProperty()
    {
        return $this->user->completedLessons()
            ->with(['section.course']) // Fixed: use section instead of module
            ->orderByDesc('lesson_user.completed_at')
            ->take(5)
            ->get()
            ->map(function ($lesson) {
                return [
                    'title' => 'Completed lesson: ' . $lesson->title,
                    'course' => $lesson->section->course->title ?? 'Unknown Course', // Fixed relationship
                    'date' => $lesson->pivot->completed_at,
                    'icon' => 'check-circle',
                    'color' => 'green-400',
                ];
            });
    }

    protected function calculateWeeklyAverage()
    {
        $completedLessons = $this->user->completedLessons()
            ->withPivot('completed_at')
            ->get();

        if ($completedLessons->isEmpty()) {
            return 0;
        }

        $firstCompletion = $completedLessons->min('pivot.completed_at');
        $weeks = max(1, Carbon::parse($firstCompletion)->diffInWeeks(now()));

        // Assuming duration_minutes exists on lessons
        return round($completedLessons->sum('duration_minutes') / 60 / $weeks, 1);
    }

    public function getSavedResourcesProperty()
    {
        return $this->user->savedResources()
            ->with(['resourceable', 'course'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function getWishlistProperty()
    {
        return $this->user->wishlists()
            ->with('course.category')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        $title = $this->isEditing ? 'Edit Profile' : 'View Profile';
        
        return view('livewire.user-management.profile', [
            'activityStats' => $this->activityStats,
            'recentActivities' => $this->recentActivities,
            'learningProgress' => $this->learningProgress,
            'savedResources' => $this->savedResources,
            'wishlist' => $this->wishlist,
        ])->layout('layouts.dashboard', [
            'title' => $title,
            'description' => $this->isEditing ? 'Update your personal information and settings' : 'View your profile information and learning progress',
            'icon' => 'fas fa-user-circle',
            'active' => 'profile'
        ]);
    }
}