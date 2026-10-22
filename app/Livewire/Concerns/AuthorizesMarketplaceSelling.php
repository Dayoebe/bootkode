<?php

namespace App\Livewire\Concerns;

trait AuthorizesMarketplaceSelling
{
    public function bootAuthorizesMarketplaceSelling(): void
    {
        abort_unless(auth()->user()?->canManageCourses(), 403);
    }
}
