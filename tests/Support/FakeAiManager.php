<?php

namespace Tests\Support;

use App\Contracts\AiProvider;
use App\Services\Ai\AiManager;

final class FakeAiManager extends AiManager
{
    public function __construct(private readonly AiProvider $fakeProvider) {}

    public function provider(): AiProvider
    {
        return $this->fakeProvider;
    }

    public function status(): array
    {
        return ['ready' => true, 'reason' => null];
    }
}
