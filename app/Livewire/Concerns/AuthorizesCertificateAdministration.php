<?php

namespace App\Livewire\Concerns;

trait AuthorizesCertificateAdministration
{
    public function bootAuthorizesCertificateAdministration(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
    }
}
