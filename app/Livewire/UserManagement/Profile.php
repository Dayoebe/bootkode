<?php

namespace App\Livewire\UserManagement;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use App\Traits\HasCloudinaryUpload;

#[Layout('layouts.dashboard')]
class Profile extends Component
{
    use WithFileUploads, HasCloudinaryUpload;

    public $user;
    public $activeTab = 'personal';
    public $activeSessions = 0;
    public $isEditing = false;

    // Settings Properties
    public $emailNotifications;
    public $certificateNotifications;
    public $profileVisibility;
    public $showEmail;
    public $showPasswordModal = false;
    public $showSessionsModal = false;
    public $showDeactivateModal = false;
    public $showDeleteModal = false;
    
    // Password fields
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    // Deletion fields
    public $delete_password = '';
    public $delete_confirmation = '';

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

    protected $listeners = [
        'refresh' => '$refresh',
        'closeModals' => 'resetModalStates'
    ];

    public function mount($mode = 'view')
    {
        $this->user = Auth::user();
        $this->isEditing = $mode === 'edit';
        $this->loadSettingsData();
        $this->resetValidation();
        
        if ($this->isEditing) {
            $this->loadUserData();
        }
    }

    public function loadSettingsData()
    {
        $this->emailNotifications = $this->user->receive_course_updates ?? true;
        $this->certificateNotifications = $this->user->receive_certificate_notifications ?? true;
        $this->profileVisibility = $this->user->profile_visibility ?? 'public';
        $this->showEmail = $this->user->show_email_publicly ?? false;
        $this->activeSessions = $this->getActiveSessionsCount();
    }

    protected function getActiveSessionsCount()
    {
        return 1;
    }

    public function updateEmailNotifications()
    {
        $this->user->update([
            'receive_course_updates' => $this->emailNotifications
        ]);
        
        $this->dispatch('notify', 'Email notifications updated successfully!');
    }

    public function updateCertificateNotifications()
    {
        $this->user->update([
            'receive_certificate_notifications' => $this->certificateNotifications
        ]);
        
        $this->dispatch('notify', 'Certificate notifications updated successfully!');
    }

    public function updateProfileVisibility()
    {
        $this->user->update([
            'profile_visibility' => $this->profileVisibility
        ]);
        
        $this->dispatch('notify', 'Profile visibility updated successfully!');
    }

    public function updateShowEmail()
    {
        $this->user->update([
            'show_email_publicly' => $this->showEmail
        ]);
        
        $this->dispatch('notify', 'Email visibility updated successfully!');
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $this->user->update([
            'password' => Hash::make($this->new_password)
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation', 'showPasswordModal']);
        $this->dispatch('notify', 'Password changed successfully!');
    }

    public function logoutOtherSessions()
    {
        Auth::logoutOtherDevices($this->current_password);
        
        $this->reset(['showSessionsModal']);
        $this->activeSessions = 1;
        $this->dispatch('notify', 'Other sessions logged out successfully!');
    }

    public function toggle2FA()
    {
        $message = $this->user->two_factor_secret 
            ? 'Two-factor authentication disabled!' 
            : 'Two-factor authentication enabled!';
        
        $this->dispatch('notify', $message);
    }

    public function exportData()
    {
        try {
            $userData = [
                'personal_info' => [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone_number' => $this->user->phone_number,
                    'date_of_birth' => $this->user->date_of_birth,
                    'bio' => $this->user->bio,
                ],
                'address' => [
                    'street' => $this->user->address_street,
                    'city' => $this->user->address_city,
                    'state' => $this->user->address_state,
                    'country' => $this->user->address_country,
                    'postal_code' => $this->user->address_postal_code,
                ],
                'education_career' => [
                    'occupation' => $this->user->occupation,
                    'education_level' => $this->user->education_level,
                    'skills' => $this->user->skills,
                ],
                'learning_data' => [
                    'courses_enrolled' => $this->user->courses()->count(),
                    'completed_lessons' => $this->user->completedLessons()->count(),
                    'certificates' => $this->user->certificates()->count(),
                ],
                'exported_at' => now()->toISOString(),
            ];

            $filename = "user_data_{$this->user->id}_" . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->streamDownload(function () use ($userData) {
                echo json_encode($userData, JSON_PRETTY_PRINT);
            }, $filename, [
                'Content-Type' => 'application/json',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error exporting data: ' . $e->getMessage(), 'error');
        }
    }

    public function clearCache()
    {
        try {
            Cache::tags(["user_{$this->user->id}"])->flush();
            Artisan::call('cache:clear');
            
            $this->dispatch('notify', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error clearing cache: ' . $e->getMessage(), 'error');
        }
    }

    public function deactivateAccount()
    {
        if ($this->user->isSuperAdmin() && $this->user->id === auth()->id()) {
            $this->dispatch('notify', 'Super admin accounts cannot be deactivated for security reasons!', 'error');
            $this->reset(['showDeactivateModal']);
            return;
        }
        
        try {
            $this->user->update([
                'is_active' => false,
                'deactivated_at' => now(),
            ]);

            Auth::logout();
            
            session()->flash('message', 'Your account has been deactivated. You can reactivate by logging in again.');
            return redirect('/');

        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error deactivating account: ' . $e->getMessage(), 'error');
        }
    }

    public function deleteAccount()
    {
        $this->validate([
            'delete_password' => ['required', 'current_password'],
            'delete_confirmation' => ['required', 'in:DELETE'],
        ]);

        try {
            $user = $this->user;
            
            // Delete profile picture from Cloudinary if exists
            if ($user->profile_picture) {
                $publicId = $this->extractPublicId($user->profile_picture);
                if ($publicId) {
                    $this->deleteFromCloudinary($publicId);
                }
            }
            
            Auth::logout();
            $user->delete();
            
            session()->flash('message', 'Your account has been permanently deleted.');
            return redirect('/');

        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error deleting account: ' . $e->getMessage(), 'error');
        }
    }

    public function resetModalStates()
    {
        $this->reset([
            'showPasswordModal',
            'showSessionsModal', 
            'showDeactivateModal',
            'showDeleteModal',
            'current_password',
            'new_password',
            'new_password_confirmation',
            'delete_password',
            'delete_confirmation'
        ]);
    }

    public function updated($property)
    {
        if (in_array($property, [
            'showPasswordModal', 
            'showSessionsModal', 
            'showDeactivateModal', 
            'showDeleteModal'
        ]) && !$this->$property) {
            $this->resetModalStates();
        }
    }

    public function loadUserData()
    {
        $this->fill($this->user->only([
            'name',
            'email',
            'phone_number',
            'bio',
            'occupation',
            'education_level',
            'address_street',
            'address_city',
            'address_state',
            'address_country',
            'address_postal_code',
            'skills'
        ]));

        $this->date_of_birth = $this->user->date_of_birth?->format('Y-m-d');

        if ($this->user->social_links) {
            $this->social_links = array_merge($this->social_links, $this->user->social_links);
        }
    }

    protected function rules()
    {
        if (!$this->isEditing)
            return [];

        $userId = Auth::id();

        return [
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
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_state' => 'nullable|string|max:100',
            'address_country' => 'nullable|string|max:100',
            'address_postal_code' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'skills' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif',
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
        if (!$this->isEditing)
            return;

        $this->validate();

        $updateData = $this->getUpdateData();

        // Handle profile picture upload to Cloudinary
        if ($this->profile_picture) {
            // Delete old picture from Cloudinary if exists
            if ($this->user->profile_picture) {
                $oldPublicId = $this->extractPublicId($this->user->profile_picture);
                if ($oldPublicId) {
                    $this->deleteFromCloudinary($oldPublicId);
                }
            }

            // Upload new picture to Cloudinary
            $uploadResult = $this->uploadProfilePicture($this->profile_picture, $this->user->id);
            
            if ($uploadResult) {
                $updateData['profile_picture'] = $uploadResult['secure_url'];
            } else {
                $this->dispatch('notify', 'Failed to upload profile picture. Please try again.', 'error');
                return;
            }
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
            // Delete from Cloudinary
            $publicId = $this->extractPublicId($this->user->profile_picture);
            if ($publicId) {
                $this->deleteFromCloudinary($publicId);
            }
            
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
            ->with(['section.course'])
            ->orderByDesc('lesson_user.completed_at')
            ->take(5)
            ->get()
            ->map(function ($lesson) {
                return [
                    'title' => 'Completed lesson: ' . $lesson->title,
                    'course' => $lesson->section->course->title ?? 'Unknown Course',
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
            ->with([
                'course' => function ($query) {
                    $query->where('is_published', true)
                        ->where('is_approved', true);
                },
                'course.category'
            ])
            ->whereHas('course', function ($query) {
                $query->where('is_published', true)
                    ->where('is_approved', true);
            })
            ->latest()
            ->take(5)
            ->get();
    }

    public function removeFromWishlist($courseId)
    {
        $this->user->wishlists()->where('course_id', $courseId)->delete();
        $this->dispatch('notify', 'Course removed from wishlist!');
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