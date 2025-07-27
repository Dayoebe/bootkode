<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class SuperAdminDashboard extends Component
{
    use WithPagination;

    public $showUserModal = false;
    public $editMode = false;
    public $userId;
    public $name;
    public $email;
    public $role;
    public $password;
    public $password_confirmation;
    public $search = '';

    protected $queryString = ['search'];

    // Add this method to get all available roles
    public static function getRoles()
    {
        return [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ACADEMY_ADMIN,
            User::ROLE_INSTRUCTOR,
            User::ROLE_MENTOR,
            User::ROLE_CONTENT_EDITOR,
            User::ROLE_AFFILIATE_AMBASSADOR,
            User::ROLE_STUDENT,
        ];
    }

    public function render()
    {
        $users = User::when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
        })
        ->allExcept(auth()->user())
        ->latest()
        ->paginate(10);

        return view('livewire.dashboard.super-admin-dashboard', [
            'users' => $users,
            'roles' => [
                User::ROLE_SUPER_ADMIN => 'Super Admin',
                User::ROLE_ACADEMY_ADMIN => 'Academy Admin',
                User::ROLE_INSTRUCTOR => 'Instructor',
                User::ROLE_MENTOR => 'Mentor',
                User::ROLE_CONTENT_EDITOR => 'Content Editor',
                User::ROLE_AFFILIATE_AMBASSADOR => 'Affiliate Ambassador',
                User::ROLE_STUDENT => 'Student',
            ]
        ])
        ->layout('layouts.dashboard', ['title' => 'Student Dashboard']);
    }

    public function createUser()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showUserModal = true;
    }

    public function editUser(User $user)
    {
        $this->editMode = true;
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->showUserModal = true;
    }

    public function saveUser()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->userId,
            'role' => 'required|in:'.implode(',', self::getRoles()),
        ];

        if (!$this->editMode) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editMode) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            $this->dispatch('notify', 'User updated successfully!');
        } else {
            User::create($data);
            $this->dispatch('notify', 'User created successfully!');
        }

        $this->resetForm();
    }

    public function deleteUser(User $user)
    {
        if (!$user->canBeDeleted()) {
            $this->dispatch('notify', 'Cannot delete this user!', 'error');
            return;
        }

        $user->delete();
        $this->dispatch('notify', 'User deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset([
            'showUserModal',
            'editMode',
            'userId',
            'name',
            'email',
            'role',
            'password',
            'password_confirmation'
        ]);
    }
}