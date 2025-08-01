<?php

namespace App\Livewire\Component;

use App\Jobs\SendVerificationEmail; // Keep this if you use it elsewhere, but for email verification, User model's method is preferred.
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    // Modal and form properties
    public $showUserModal = false;
    public $editMode = false;
    public $userId;
    public $name = '';
    public $email = '';
    public $role = '';
    public $password = '';
    public $password_confirmation = '';
    public $search = '';
    public $sendVerificationEmail = true;
    public $perPage = 15;
    public $saveProgress = 0; // Progress indicator for saving

    // Cache roles to avoid repeated calls
    protected $roles;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 15]
    ];

    protected $listeners = [
        'refreshUsers' => '$refresh',
    ];

    public function mount()
    {
        $this->roles = $this->getRolesForSelect();
    }

    public function render()
    {
        return view('livewire.component.user-management', [
            'users' => $this->getUsersQuery()->paginate($this->perPage),
            'roles' => $this->roles ?? $this->getRolesForSelect(),
        ]);
    }

    protected function getUsersQuery()
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'created_at', 'email_verified_at'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->where('id', '!=', auth()->id())
            ->latest('created_at');
    }

    protected function getRolesForSelect(): array
    {
        return [
            User::ROLE_SUPER_ADMIN => 'Super Admin',
            User::ROLE_ACADEMY_ADMIN => 'Academy Admin',
            User::ROLE_INSTRUCTOR => 'Instructor',
            User::ROLE_MENTOR => 'Mentor',
            User::ROLE_CONTENT_EDITOR => 'Content Editor',
            User::ROLE_AFFILIATE_AMBASSADOR => 'Affiliate Ambassador',
            User::ROLE_STUDENT => 'Student',
        ];
    }

    public function createUser()
    {
        $this->resetFormFields();
        $this->editMode = false;
        $this->sendVerificationEmail = true;
        $this->showUserModal = true;
    }

    public function editUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            $this->resetFormFields();
            $this->editMode = true;
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->role;
            // If editing, default to not sending verification email unless email is changed
            $this->sendVerificationEmail = false; 
            $this->showUserModal = true;
        } catch (\Exception $e) {
            Log::error('Edit user error: ' . $e->getMessage());
            $this->dispatch('notify', 'Error loading user data', 'error');
        }
    }

    public function saveUser()
    {
        // Reset progress at the start of the save operation
        $this->saveProgress = 0;
        
        try {
            // Step 1: Validate data
            $this->saveProgress = 25;
            $this->validateUser();
            
            // Step 2: Prepare data and begin transaction
            $this->saveProgress = 50;
            DB::beginTransaction();
    
            $user = null; // Initialize user variable
            if ($this->editMode) {
                $user = $this->updateExistingUser($this->prepareUserData());
            } else {
                $user = $this->createNewUser($this->prepareUserData());
            }
            
            // Step 3: Commit transaction
            $this->saveProgress = 90;
            DB::commit();
            
            // Step 4: Handle post-save actions and UI updates
            $this->saveProgress = 100;
            $this->handleSuccessfulSave($user); // Pass the user to the success handler
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Livewire handles validation errors, just reset progress
            $this->saveProgress = 0;
            throw $e; // Re-throw to display validation messages
        } catch (\Exception $e) {
            DB::rollBack();
            $this->saveProgress = 0;
            $this->handleSaveError($e);
        }
    }

    protected function validateUser()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->userId)
            ],
            'role' => [
                'required',
                Rule::in(array_keys($this->getRolesForSelect()))
            ],
            'sendVerificationEmail' => 'boolean'
        ];

        if (!$this->editMode) {
            $rules['password'] = [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ];
        } else {
            // For edit mode, password is nullable if not changing
            $rules['password'] = [
                'nullable',
                'confirmed',
                Rules\Password::defaults()
            ];
        }

        $this->validate($rules, [
            'email.unique' => 'This email is already in use',
            'password.confirmed' => 'Passwords do not match'
        ]);
    }

    protected function prepareUserData(): array
    {
        $userData = [
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'role' => $this->role,
        ];

        // Only add password to data if it's a new user or password field is not empty in edit mode
        if (!$this->editMode || (!empty($this->password) && !empty($this->password_confirmation))) {
            $userData['password'] = Hash::make($this->password);
        }

        return $userData;
    }

    protected function updateExistingUser(array $userData)
    {
        $user = User::findOrFail($this->userId);
        // No need to store original email if we're checking wasChanged later
        
        // Fill only the provided data, excluding password if it's empty
        $user->fill(array_filter($userData, function($value, $key) {
            // Exclude password if it's empty during an update
            return !($key === 'password' && empty($value));
        }, ARRAY_FILTER_USE_BOTH));
        
        // Handle email verification reset if email changed and option is enabled
        if ($user->isDirty('email') && $this->sendVerificationEmail) {
            $user->email_verified_at = null;
        }
        
        $user->save();
        
        // If email changed AND sendVerificationEmail is true, dispatch the notification
        // The User model's sendEmailVerificationNotification method will queue the CustomVerifyEmail
        if ($user->wasChanged('email') && $this->sendVerificationEmail) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                Log::warning('Failed to send verification email for updated user: ' . $e->getMessage());
                // Log the warning but don't stop the save operation
            }
        }
        
        return $user;
    }

    protected function createNewUser(array $userData)
    {
        // Set email as verified if not sending verification email
        if (!$this->sendVerificationEmail) {
            $userData['email_verified_at'] = now();
        }
        
        $user = User::create($userData);

        // If sendVerificationEmail is true, dispatch the notification
        // The User model's sendEmailVerificationNotification method will queue the CustomVerifyEmail
        if ($this->sendVerificationEmail) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                Log::warning('Failed to send verification email for new user: ' . $e->getMessage());
                // Log the warning but don't stop the save operation
            }
        }

        // Dispatch the Registered event regardless of email verification status
        event(new Registered($user));
        
        return $user;
    }

    protected function handleSuccessfulSave(?User $user) // Accept user as a parameter
    {
        $message = $this->editMode 
            ? 'User updated successfully!' 
            : ($this->sendVerificationEmail 
                ? 'User created and verification email dispatched!' // Clarify it's dispatched
                : 'User created successfully!');

        $this->dispatch('notify', $message, 'success');
        $this->closeModalAndReset();
        
        // Force refresh the component to show updated user list
        $this->resetPage();
    }

    protected function handleSaveError(\Exception $e)
    {
        Log::error('User save failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_data' => [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'edit_mode' => $this->editMode
            ]
        ]);

        $this->dispatch('notify', 'Save failed: ' . $e->getMessage(), 'error');
    }

    public function resendVerificationEmail($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if ($user->hasVerifiedEmail()) {
                $this->dispatch('notify', 'User email is already verified!', 'info');
                return;
            }

            // Use the built-in notification method which queues the job
            $user->sendEmailVerificationNotification();
            $this->dispatch('notify', 'Verification email sent successfully!', 'success');
            
        } catch (\Exception $e) {
            Log::error('Failed to resend verification email: ' . $e->getMessage());
            $this->dispatch('notify', 'Failed to send verification email!', 'error');
        }
    }

    public function markAsVerified($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if ($user->hasVerifiedEmail()) {
                $this->dispatch('notify', 'User email is already verified!', 'info');
                return;
            }

            $user->markEmailAsVerified();
            $this->dispatch('notify', 'User email marked as verified!', 'success');
            // No need to dispatch refreshUsers here, Livewire's reactivity should handle it.
            
        } catch (\Exception $e) {
            Log::error('Failed to mark email as verified: ' . $e->getMessage());
            $this->dispatch('notify', 'Failed to verify user email!', 'error');
        }
    }

    public function deleteUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if ($user->id === auth()->id()) {
                $this->dispatch('notify', 'Cannot delete your own account!', 'error');
                return;
            }

            $user->delete();
            $this->dispatch('notify', 'User deleted successfully!', 'success');
            
        } catch (\Exception $e) {
            Log::error('User deletion error: ' . $e->getMessage());
            $this->dispatch('notify', 'Error deleting user: ' . $e->getMessage(), 'error');
        }
    }

    public function resetFormFields()
    {
        $this->reset([
            'editMode',
            'userId',
            'name',
            'email',
            'role',
            'password',
            'password_confirmation',
            'sendVerificationEmail',
            'saveProgress' // Reset progress when form fields are reset
        ]);
        
        $this->resetErrorBag();
    }

    public function closeModalAndReset()
    {
        $this->showUserModal = false;
        $this->resetFormFields();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
