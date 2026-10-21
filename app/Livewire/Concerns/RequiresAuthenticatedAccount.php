<?php

namespace App\Livewire\Concerns;

trait RequiresAuthenticatedAccount
{
    public function bootRequiresAuthenticatedAccount(): void
    {
        abort_unless(auth()->check(), 403);
    }
}
