<?php

namespace Tests\Support;

use App\Contracts\AiProvider;
use App\Data\AiResponse;

final class FakeContentProvider implements AiProvider
{
    public array $messages = [];
    public array $options = [];

    public function __construct(private array $responses) {}

    public function chat(array $messages, array $options = []): AiResponse
    {
        $this->messages[] = $messages;
        $this->options[] = $options;
        $response = array_shift($this->responses);
        if ($response instanceof \Throwable) {
            throw $response;
        }
        if (! $response instanceof AiResponse) {
            throw new \RuntimeException('No fake AI response was queued.');
        }

        return $response;
    }

    public function isConfigured(): bool
    {
        return true;
    }
}
