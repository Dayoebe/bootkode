<?php

namespace App\Livewire\Concerns;

trait AuthorizesMentoring
{
    public function bootAuthorizesMentoring(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['mentor', 'academy_admin', 'super_admin']), 403);
    }
}
