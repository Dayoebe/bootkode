<?php

namespace App\Livewire\Concerns;

trait AuthorizesPlatformAdministration
{
    public function bootAuthorizesPlatformAdministration(): void
    {
        $this->authorizePlatformAdministration();
    }

    protected function authorizePlatformAdministration(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin']), 403);
    }
}
