<?php

namespace App\Exceptions;

use RuntimeException;

class AiProviderException extends RuntimeException
{
    public function __construct(string $message, public readonly string $reason = 'provider_error')
    {
        parent::__construct($message);
    }
}
