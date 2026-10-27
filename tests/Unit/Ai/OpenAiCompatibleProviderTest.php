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
            ->chat([['role' => 'user', 'content' => 'Help']], ['response_format' => ['type' => 'json_object']]);

        $this->assertSame('Start with the failing input.', $response->content);
        $this->assertSame(12, $response->promptTokens);
        $this->assertSame('request-123', $response->requestId);
        Http::assertSent(fn ($request) => $request['model'] === 'configured-model' && $request->hasHeader('Authorization', 'Bearer secret'));
    }

    public function test_it_passes_through_an_optional_json_response_format(): void
    {
        Http::fake(['*' => Http::response([
            'choices' => [['message' => ['content' => '{"valid":true}']]],
        ])]);

        (new OpenAiCompatibleProvider(['key' => 'secret', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))
            ->chat([['role' => 'user', 'content' => 'Return JSON']], ['response_format' => ['type' => 'json_object']]);

        Http::assertSent(fn ($request) => $request['response_format']['type'] === 'json_object');
    }

    public function test_it_rejects_an_empty_provider_response(): void
    {
        Http::fake(['*' => Http::response(['choices' => []])]);
        $this->expectException(AiProviderException::class);
        (new OpenAiCompatibleProvider(['key' => 'secret', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))->chat([]);
    }

    public function test_rate_limits_and_provider_errors_do_not_expose_response_bodies(): void
    {
        foreach ([400 => 'invalid_request', 401 => 'authentication_failed', 403 => 'access_denied', 404 => 'model_unavailable', 429 => 'rate_limited', 500 => 'provider_error'] as $status => $reason) {
            Http::swap(new \Illuminate\Http\Client\Factory);
            Http::fake(fn () => Http::response(['error' => 'private-provider-response-secret'], $status));
            try {
                (new OpenAiCompatibleProvider(['key' => 'private-api-key', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))->chat([]);
                $this->fail('A provider error was accepted.');
            } catch (AiProviderException $exception) {
                $this->assertSame($reason, $exception->reason);
                $this->assertStringNotContainsString('private', $exception->getMessage());
            }
        }
    }

    public function test_connection_timeout_has_a_safe_message(): void
    {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('private transport data'));
        try {
            (new OpenAiCompatibleProvider(['key' => 'private-api-key', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))->chat([]);
            $this->fail('A timeout was accepted.');
        } catch (AiProviderException $exception) {
            $this->assertSame('unavailable', $exception->reason);
            $this->assertStringNotContainsString('private', $exception->getMessage());
        }
    }

    public function test_structured_output_truncation_is_reported_without_returning_partial_json(): void
    {
        Http::fake(fn () => Http::response(['choices' => [['finish_reason' => 'length', 'message' => ['content' => '{"partial":']]]]));
        try {
            (new OpenAiCompatibleProvider(['key' => 'test-key', 'model' => 'configured-model', 'base_url' => 'https://api.groq.test']))
                ->chat([], ['response_format' => ['type' => 'json_object']]);
            $this->fail('Truncated JSON was returned as a complete response.');
        } catch (AiProviderException $exception) {
            $this->assertSame('output_limit', $exception->reason);
            $this->assertStringNotContainsString('partial', $exception->getMessage());
        }
    }
}
