<?php

namespace App\Data;

final readonly class AiResponse
{
    public function __construct(
        public string $content,
        public ?int $promptTokens = null,
        public ?int $completionTokens = null,
        public ?string $requestId = null,
        public array $metadata = [],
    ) {}
}
