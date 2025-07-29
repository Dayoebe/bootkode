<?php

namespace App\Livewire\Component\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EditProfile extends Component
{
    public $user;
    public $name;
    public $email;

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user->id),
            ],
        ];
    }

    public function updateProfile()
    {
        $this->validate();

        $this->user->name = $this->name;
        if ($this->user->email !== $this->email) {
            $this->user->email = $this->email;
            $this->user->email_verified_at = null;
        }
        $this->user->save();

        $this->dispatch('notify', 'Profile updated successfully!', 'success');
        return redirect()->route('profile.view');
    }

    public function render()
    {
        return view('livewire.component.profile.edit')
            ->layout('layouts.dashboard', [
                'title' => 'Edit Profile'
            ]);
    }
}