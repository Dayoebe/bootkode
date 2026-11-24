<?php

namespace App\Livewire\Concerns;

use App\Services\Authorization\PublishingAccess;

trait AuthorizesPublishingAdministration
{
    public function bootAuthorizesPublishingAdministration(): void
    {
        app(PublishingAccess::class)->authorize(auth()->user(), $this->publishingAbility());
    }

    abstract protected function publishingAbility(): string;
}
