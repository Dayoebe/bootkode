<?php

namespace App\Livewire\UserManagement;

use App\Jobs\SendVerificationEmail;
use App\Models\Core\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use App\Notifications\UserCreatedNotification;

#[Layout('layouts.dashboard', ['title' => 'User Dashboard', 'description' => 'Manage users, roles, and permissions', 'icon' => 'fas fa-users', 'active' => 'admin.user-management'])]

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
    public $sendVerificationEmail = false; // Changed default to false
    public $perPage = 15;
    public $statusFilter = 'all';
    public $createAnother = false;
    public $saveProgress = 0;
    public $autoGeneratePassword = true; // New property
    public $markAsVerified = true; // New property - auto verify admin-created users
    public $sendWelcomeEmail = true; // New property

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

    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email' . ($this->editMode ? ',' . $this->userId : ''),
            ],
            'role' => ['required', 'string', Rule::in(array_keys($this->getRolesForSelect()))],
            'sendVerificationEmail' => ['boolean'],
            'markAsVerified' => ['boolean'],
            'sendWelcomeEmail' => ['boolean'],
        ];

        // Password rules
        if (!$this->editMode) {
            // Creating new user
            if (!$this->autoGeneratePassword) {
                $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
                $rules['password_confirmation'] = ['required_with:password', 'string', 'min:8'];
            }
        } else {
            // Editing existing user - password optional
            if ($this->password) {
                $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
                $rules['password_confirmation'] = ['required_with:password', 'string', 'min:8'];
            }
        }

        return $rules;
    }

    public function updatedAutoGeneratePassword()
    {
        if ($this->autoGeneratePassword) {
            $this->password = '';
            $this->password_confirmation = '';
            $this->resetValidation(['password', 'password_confirmation']);
        }
    }

    public function generateRandomPassword()
    {
        // Generate a strong random password
        $password = Str::random(12);
        $this->password = $password;
        $this->password_confirmation = $password;
        $this->autoGeneratePassword = false;
        
        $this->dispatch('notify', 'Password generated. Make sure to copy it!', 'success');
        $this->dispatch('password-generated', $password);
    }

    public function render()
    {
        return view('livewire.user-management.user-management', [
            'users' => $this->getUsersQuery()->paginate($this->perPage),
            'roles' => $this->roles ?? $this->getRolesForSelect(),
        ]);
    }

    public function activateUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if ($user->id === auth()->id()) {
                $this->dispatch('notify', 'Cannot activate your own account!', 'error');
                return;
            }

            $user->update([
                'is_active' => true,
                'deactivated_at' => null
            ]);

            $this->dispatch('notify', 'User activated successfully!', 'success');
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('User activation error: ' . $e->getMessage());
            $this->dispatch('notify', 'Error activating user: ' . $e->getMessage(), 'error');
        }
    }

    public function deactivateUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            if ($user->id === auth()->id()) {
                $this->dispatch('notify', 'Cannot deactivate your own account!', 'error');
                return;
            }

            if ($user->isSuperAdmin()) {
                $this->dispatch('notify', 'Super admin accounts cannot be deactivated!', 'error');
                return;
            }

            $user->update([
                'is_active' => false,
                'deactivated_at' => now()
            ]);

            $this->dispatch('notify', 'User deactivated successfully!', 'success');
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('User deactivation error: ' . $e->getMessage());
            $this->dispatch('notify', 'Error deactivating user: ' . $e->getMessage(), 'error');
        }
    }

    protected function getUsersQuery()
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'is_active', 'deactivated_at', 'created_at', 'email_verified_at'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($this->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
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
        $this->sendVerificationEmail = false;
        $this->markAsVerified = true;
        $this->sendWelcomeEmail = true;
        $this->autoGeneratePassword = true;
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
            $this->password = '';
            $this->password_confirmation = '';
            $this->sendVerificationEmail = false;
            $this->autoGeneratePassword = false;
            $this->showUserModal = true;
        } catch (\Exception $e) {
            Log::error('Edit user error: ' . $e->getMessage());
            $this->dispatch('notify', 'Error loading user data', 'error');
        }
    }

    public function saveUser()
    {
        try {
            $this->validate();

            // Progress simulation
            for ($i = 20; $i <= 100; $i += 20) {
                $this->saveProgress = $i;
                usleep(200000);
            }

            if ($this->editMode) {
                $user = User::findOrFail($this->userId);
                
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'role' => $this->role,
                ];

                // Only update password if provided
                if ($this->password) {
                    $userData['password'] = Hash::make($this->password);
                }

                $user->update($userData);
                
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($user)
                    ->event('updated')
                    ->log('Updated user');

                $message = 'User updated successfully!';
                
            } else {
                // Creating new user
                $generatedPassword = null;
                
                if ($this->autoGeneratePassword) {
                    $generatedPassword = Str::random(12);
                    $finalPassword = $generatedPassword;
                } else {
                    $finalPassword = $this->password;
                }

                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($finalPassword),
                    'role' => $this->role,
                    'is_active' => true, // Always active when admin creates
                ];

                // Auto-verify if markAsVerified is true
                if ($this->markAsVerified) {
                    $userData['email_verified_at'] = now();
                }

                $user = User::create($userData);
                
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($user)
                    ->withProperties([
                        'auto_verified' => $this->markAsVerified,
                        'welcome_email_sent' => $this->sendWelcomeEmail,
                    ])
                    ->log('Created user');

                // Send welcome email with credentials
                if ($this->sendWelcomeEmail) {
                    try {
                        $user->notify(new UserCreatedNotification(
                            $generatedPassword ?? $finalPassword,
                            auth()->user()->name,
                            $this->sendVerificationEmail
                        ));
                    } catch (\Exception $e) {
                        Log::warning('Failed to send welcome email: ' . $e->getMessage());
                    }
                }

                // Send verification email if requested and not auto-verified
                if ($this->sendVerificationEmail && !$this->markAsVerified) {
                    try {
                        $user->sendEmailVerificationNotification();
                    } catch (\Exception $e) {
                        Log::warning('Failed to send verification email: ' . $e->getMessage());
                    }
                }

                $message = 'User created successfully!';
                
                if ($generatedPassword) {
                    $message .= ' Temporary password sent via email.';
                }
            }

            $this->dispatch('notify', $message, 'success');
            $this->dispatch('refreshUsers');

            if ($this->createAnother && !$this->editMode) {
                $this->resetFormFields();
                $this->createUser(); // Reset to create mode
            } else {
                $this->closeModalAndReset();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->saveProgress = 0;
            throw $e;
        } catch (\Exception $e) {
            $this->saveProgress = 0;
            $this->dispatch('notify', 'Error: ' . $e->getMessage(), 'error');
            Log::error('User save failed: ' . $e->getMessage());
        }
    }

    public function resendVerificationEmail($userId)
    {
        try {
            $user = User::findOrFail($userId);

            if ($user->hasVerifiedEmail()) {
                $this->dispatch('notify', 'User email is already verified!', 'info');
                return;
            }

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
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($user)
                ->log('Manually verified user email');
                
            $this->dispatch('notify', 'User email marked as verified!', 'success');

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

            if ($user->isSuperAdmin()) {
                $this->dispatch('notify', 'Cannot delete super admin accounts!', 'error');
                return;
            }

            $user->delete();
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['deleted_user_email' => $user->email])
                ->log('Deleted user: ' . $user->name);
                
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
            'markAsVerified',
            'sendWelcomeEmail',
            'autoGeneratePassword',
            'saveProgress'
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