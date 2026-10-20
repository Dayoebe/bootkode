<?php

namespace Tests\Unit\Ai;

use App\Exceptions\AiProviderException;
use App\Services\Ai\OpenAiCompatibleProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAiCompatibleProviderTest extends TestCase
{
    public function test_it_rejects_missing_configuration_without_an_http_request(): void
    {
        Http::fake();
        $this->expectException(AiProviderException::class);
        (new OpenAiCompatibleProvider(['key' => null, 'model' => null, 'base_url' => 'https://example.test']))->chat([]);
        Http::assertNothingSent();
    }

    public function test_it_normalizes_a_successful_openai_compatible_response(): void
    {
        Http::fake(['https://api.groq.test/*' => Http::response([
            'choices' => [['message' => ['content' => 'Start with the failing input.']]],
            'usage' => ['prompt_tokens' => 12, 'completion_tokens' => 8],
        ], 200, ['x-request-id' => 'request-123'])]);

        $response = (new OpenAiCompatibleProvider(['key' => 'secret', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))
            ->chat([['role' => 'user', 'content' => 'Help']]);

        $this->assertSame('Start with the failing input.', $response->content);
        $this->assertSame(12, $response->promptTokens);
        $this->assertSame('request-123', $response->requestId);
        Http::assertSent(fn ($request) => $request['model'] === 'configured-model' && $request->hasHeader('Authorization', 'Bearer secret'));
    }

    public function test_it_rejects_an_empty_provider_response(): void
    {
        Http::fake(['*' => Http::response(['choices' => []])]);
        $this->expectException(AiProviderException::class);
        (new OpenAiCompatibleProvider(['key' => 'secret', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))->chat([]);
    }
}
