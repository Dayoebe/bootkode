<?php

namespace App\Livewire\Concerns;

use App\Models\Core\User;

trait AuthorizesFinancialAdministration
{
    public function bootAuthorizesFinancialAdministration(): void
    {
        abort_unless(auth()->user()?->hasAnyRole([
            User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN,
        ]), 403);
    }
}
