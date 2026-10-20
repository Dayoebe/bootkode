<?php

namespace App\Contracts;

use App\Data\AiResponse;

interface AiProvider
{
    public function chat(array $messages, array $options = []): AiResponse;

    public function isConfigured(): bool;
}
