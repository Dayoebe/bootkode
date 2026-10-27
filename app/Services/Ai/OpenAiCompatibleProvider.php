<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use App\Data\AiResponse;
use App\Exceptions\AiProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenAiCompatibleProvider implements AiProvider
{
    public function __construct(private readonly array $configuration) {}

    public function isConfigured(): bool
    {
        return filled($this->configuration['key'] ?? null)
            && filled($this->configuration['model'] ?? null)
            && filled($this->configuration['base_url'] ?? null);
    }

    public function chat(array $messages, array $options = []): AiResponse
    {
        if (! $this->isConfigured()) {
            throw new AiProviderException('The AI provider is not configured.', 'not_configured');
        }

        try {
            $payload = [
                'model' => $this->configuration['model'],
                'messages' => $messages,
                'temperature' => $options['temperature'] ?? 0.3,
                'max_tokens' => $options['max_tokens'] ?? 900,
            ];
            if (isset($options['response_format'])) {
                $payload['response_format'] = $options['response_format'];
            }

            $response = Http::withToken($this->configuration['key'])
                ->acceptJson()
                ->timeout(max(1, (int) config('bootkode_ai.timeout', 30)))
                ->retry(max(0, (int) config('bootkode_ai.retries', 1)), 250, throw: false)
                ->post(rtrim($this->configuration['base_url'], '/').'/chat/completions', $payload);
        } catch (ConnectionException) {
            throw new AiProviderException('The AI provider could not be reached. Please try again later.', 'unavailable');
        }

        if ($response->status() === 429) {
            throw new AiProviderException('The AI provider is busy. Please try again shortly.', 'rate_limited');
        }

        if ($response->status() === 401) {
            throw new AiProviderException('AI authentication failed. Ask an administrator to check the server-side API key.', 'authentication_failed');
        }

        if ($response->status() === 403) {
            throw new AiProviderException('The AI provider denied access. Ask an administrator to check the account and model permissions.', 'access_denied');
        }

        if ($response->status() === 404) {
            throw new AiProviderException('The configured AI model or endpoint is unavailable. Ask an administrator to check the server configuration.', 'model_unavailable');
        }

        if ($response->status() === 400) {
            throw new AiProviderException('The AI provider rejected the request. Check that the configured model supports the requested output format.', 'invalid_request');
        }

        if (! $response->successful()) {
            throw new AiProviderException('The AI provider could not complete this request (HTTP '.$response->status().').', 'provider_error');
        }

        if (isset($options['response_format']) && data_get($response->json(), 'choices.0.finish_reason') === 'length') {
            throw new AiProviderException('The AI response reached its output limit. Reduce the requested batch size or adjust the server generation token limit.', 'output_limit');
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content'));

        if ($content === '') {
            throw new AiProviderException('The AI provider returned an invalid response.', 'invalid_response');
        }

        return new AiResponse(
            content: $content,
            promptTokens: data_get($response->json(), 'usage.prompt_tokens'),
            completionTokens: data_get($response->json(), 'usage.completion_tokens'),
            requestId: $response->header('x-request-id'),
        );
    }
}
