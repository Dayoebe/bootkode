<?php

namespace App\Livewire\Component;

use Livewire\Component;

class EmailVerificationAlert extends Component
{
    public $showAlert = true;
    public $email;
    public $isResending = false;

    public function mount()
    {
        if (auth()->check() && !auth()->user()->hasVerifiedEmail()) {
            $this->email = auth()->user()->email;
        }
    }

    public function resendVerificationEmail()
    {
        if (!auth()->check()) {
            return;
        }

        $this->isResending = true;

        try {
            auth()->user()->sendEmailVerificationNotification();
            session()->flash('status', 'verification-link-sent');
            $this->dispatch('email-sent-successfully');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send verification email. Please try again.');
        } finally {
            $this->isResending = false;
        }
    }

    public function dismissAlert()
    {
        $this->showAlert = false;
    }

    public function render()
    {
        return view('livewire.component.email-verification-alert');
    }
}