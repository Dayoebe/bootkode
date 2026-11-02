<?php

namespace App\Services\Ai;

use App\Exceptions\AiProviderException;

class AiConnectionService
{
    public function __construct(private readonly AiManager $manager) {}

    /** Explicit probe only: no course, conversation, learner data or credentials in the prompt/result. */
    public function check(): array
    {
        $started = hrtime(true);
        $response = $this->manager->provider()->chat([
            ['role' => 'system', 'content' => 'This is a BootKode connection test. Return only the JSON object {"status":"ok"}.'],
            ['role' => 'user', 'content' => 'Verify a structured reply.'],
        ], [
            'max_tokens' => 512,
            'temperature' => 0,
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'bootkode_connection',
                    'strict' => true,
                    'schema' => [
                        'type' => 'object',
                        'properties' => ['status' => ['type' => 'string', 'enum' => ['ok']]],
                        'required' => ['status'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        ]);

        if (json_decode($response->content, true) !== ['status' => 'ok']) {
            throw new AiProviderException('The provider replied, but its structured output could not be verified.', 'invalid_response');
        }

        return [
            'success' => true,
            'provider' => (string) config('bootkode_ai.provider'),
            'checked_at' => now()->toIso8601String(),
            'duration_ms' => (int) round((hrtime(true) - $started) / 1_000_000),
            'message' => 'Connection verified. The provider returned a valid structured reply.',
        ];
    }
}
