<?php

namespace App\Livewire\Concerns;

trait HasActionFeedback
{
    #[\Livewire\Attributes\Locked]
    public array $feedback = [];

    protected function flashFeedback(string $key, string $message): void
    {
        $this->feedback = [$key => $message];
    }
}
