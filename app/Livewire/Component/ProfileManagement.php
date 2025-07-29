<?php

namespace App\Livewire\Component;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;

#[Title('Profile Management')]
class ProfileManagement extends Component
{
    public $user;
    public $name;
    public $email;
    public $currentTab = 'view';
    public $showPasswordFields = false;
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id),
            ],
        ];

        if ($this->showPasswordFields) {
            $rules += [
                'current_password' => ['required', 'current_password'],
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
            ];
        }

        return $rules;
    }

    public function updateProfile()
    {
        $this->validate();

        try {
            $this->user->name = $this->name;
            
            if ($this->user->email !== $this->email) {
                $this->user->email = $this->email;
                $this->user->email_verified_at = null;
            }

            if ($this->showPasswordFields && $this->password) {
                $this->user->password = bcrypt($this->password);
            }

            $this->user->save();

            $this->resetPasswordFields();
            $this->dispatch('notify', 'Profile updated successfully!', 'success');
            $this->currentTab = 'view';
        } catch (\Exception $e) {
            $this->dispatch('notify', 'Error updating profile: ' . $e->getMessage(), 'error');
        }
    }

    public function togglePasswordFields()
    {
        $this->showPasswordFields = !$this->showPasswordFields;
        $this->resetPasswordFields();
    }

    private function resetPasswordFields()
    {
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag(['current_password', 'password', 'password_confirmation']);
    }

    public function editProfile()
    {
        $this->currentTab = 'edit';
        $this->showPasswordFields = false;
    }

    public function viewProfile()
    {
        $this->currentTab = 'view';
        $this->mount(); // Refresh user data
    }

    public function render()
    {
        return view('livewire.component.profile-management')
            ->layout('layouts.dashboard');
    }
}