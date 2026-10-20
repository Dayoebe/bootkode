<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use App\Exceptions\AiProviderException;

class AiManager
{
    public function provider(): AiProvider
    {
        if (! config('bootkode_ai.enabled')) {
            throw new AiProviderException('BootKode AI is currently disabled.', 'disabled');
        }

        $name = (string) config('bootkode_ai.provider', 'groq');
        $configuration = config("bootkode_ai.providers.{$name}");

        if (! is_array($configuration)) {
            throw new AiProviderException('The selected AI provider is not supported.', 'unsupported_provider');
        }

        return new OpenAiCompatibleProvider($configuration);
    }

    public function status(): array
    {
        if (! config('bootkode_ai.enabled')) {
            return ['ready' => false, 'reason' => 'disabled'];
        }

        try {
            return ['ready' => $this->provider()->isConfigured(), 'reason' => $this->provider()->isConfigured() ? null : 'not_configured'];
        } catch (AiProviderException $exception) {
            return ['ready' => false, 'reason' => $exception->reason];
        }
    }
}
