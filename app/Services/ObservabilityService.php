<?php

namespace App\Services;

use App\Models\System\ObservabilityEvent;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ObservabilityService
{
    private static ?bool $eventsTableExists = null;

    public function record(array $payload): ?ObservabilityEvent
    {
        if (! $this->eventsTableExists()) {
            return null;
        }

        try {
            $now = now();
            $type = $payload['type'] ?? ObservabilityEvent::TYPE_ERROR;
            $summary = $this->limitText((string) ($payload['summary'] ?? 'Production event'), 255);
            $message = $this->limitText((string) ($payload['message'] ?? ''), 12000);
            $routeName = $payload['route_name'] ?? null;
            $url = $this->limitText((string) ($payload['url'] ?? ''), 2048) ?: null;
            $fingerprint = $payload['fingerprint'] ?? $this->fingerprint($type, $summary, $routeName, $url, $payload['source'] ?? null, $payload['context'] ?? []);

            $attributes = [
                'type' => $type,
                'severity' => $payload['severity'] ?? ObservabilityEvent::SEVERITY_WARNING,
                'status' => $payload['status'] ?? ObservabilityEvent::STATUS_OPEN,
                'source' => $this->limitText((string) ($payload['source'] ?? ''), 120) ?: null,
                'summary' => $summary,
                'message' => $message ?: null,
                'url' => $url,
                'method' => $payload['method'] ?? null,
                'route_name' => $routeName,
                'user_id' => $payload['user_id'] ?? Auth::id(),
                'ip_address' => $payload['ip_address'] ?? null,
                'user_agent' => $this->limitText((string) ($payload['user_agent'] ?? ''), 1000) ?: null,
                'duration_ms' => $payload['duration_ms'] ?? null,
                'fingerprint' => $fingerprint,
                'context' => $this->sanitizeContext($payload['context'] ?? []),
                'first_seen_at' => $payload['occurred_at'] ?? $now,
                'last_seen_at' => $payload['occurred_at'] ?? $now,
            ];

            $existing = ObservabilityEvent::query()
                ->where('fingerprint', $fingerprint)
                ->where('status', ObservabilityEvent::STATUS_OPEN)
                ->where('last_seen_at', '>=', $now->copy()->subDay())
                ->first();

            if ($existing) {
                $existing->fill(Arr::except($attributes, ['first_seen_at', 'status']));
                $existing->occurrences++;
                $existing->last_seen_at = $payload['occurred_at'] ?? $now;
                $existing->save();

                return $existing;
            }

            return ObservabilityEvent::create($attributes);
        } catch (Throwable) {
            return null;
        }
    }

    public function recordException(Throwable $exception, ?Request $request = null): ?ObservabilityEvent
    {
        $request ??= request();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
        $type = $this->isMailException($exception) ? ObservabilityEvent::TYPE_MAIL_FAILURE : ObservabilityEvent::TYPE_ERROR;

        if ($statusCode === 404) {
            $type = ObservabilityEvent::TYPE_BROKEN_ROUTE;
        }

        return $this->record([
            'type' => $type,
            'severity' => $statusCode >= 500 ? ObservabilityEvent::SEVERITY_CRITICAL : ObservabilityEvent::SEVERITY_WARNING,
            'source' => $exception::class,
            'summary' => $this->exceptionSummary($exception),
            'message' => $exception->getMessage(),
            'url' => $this->requestUrl($request),
            'method' => $request?->method(),
            'route_name' => $request?->route()?->getName(),
            'user_id' => Auth::id(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => [
                'exception' => $exception::class,
                'status_code' => $statusCode,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace_preview' => collect($exception->getTrace())->take(8)->map(fn ($frame) => Arr::only($frame, ['file', 'line', 'class', 'function']))->values()->all(),
            ],
        ]);
    }

    public function observeResponse(Request $request, Response $response, int $durationMs): void
    {
        if ($this->shouldSkipRequest($request)) {
            return;
        }

        if ($response->getStatusCode() === 404) {
            $this->recordBrokenRoute($request);
        }

        $threshold = (int) config('observability.slow_request_ms', env('OBSERVABILITY_SLOW_REQUEST_MS', 1500));

        if ($durationMs >= $threshold) {
            $this->recordSlowPage($request, $durationMs, $response->getStatusCode(), $threshold);
        }
    }

    public function recordSlowPage(Request $request, int $durationMs, int $statusCode, int $threshold): ?ObservabilityEvent
    {
        return $this->record([
            'type' => ObservabilityEvent::TYPE_SLOW_PAGE,
            'severity' => $durationMs >= ($threshold * 3) ? ObservabilityEvent::SEVERITY_ERROR : ObservabilityEvent::SEVERITY_WARNING,
            'source' => 'http.request',
            'summary' => 'Slow page: ' . ($request->route()?->getName() ?: $request->path()),
            'message' => "Request completed in {$durationMs}ms.",
            'url' => $this->requestUrl($request),
            'method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'duration_ms' => $durationMs,
            'context' => [
                'path' => $request->path(),
                'status_code' => $statusCode,
                'threshold_ms' => $threshold,
                'query' => $request->query(),
            ],
        ]);
    }

    public function recordBrokenRoute(Request $request): ?ObservabilityEvent
    {
        return $this->record([
            'type' => ObservabilityEvent::TYPE_BROKEN_ROUTE,
            'severity' => ObservabilityEvent::SEVERITY_WARNING,
            'source' => 'http.404',
            'summary' => 'Broken route: ' . $request->path(),
            'message' => 'The requested URL returned a 404 response.',
            'url' => $this->requestUrl($request),
            'method' => $request->method(),
            'route_name' => $request->route()?->getName(),
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'context' => [
                'path' => $request->path(),
                'query' => $request->query(),
                'referer' => $request->headers->get('referer'),
            ],
        ]);
    }

    public function recordFailedJob(JobFailed $event): ?ObservabilityEvent
    {
        $payload = $event->job->payload();

        return $this->record([
            'type' => ObservabilityEvent::TYPE_FAILED_JOB,
            'severity' => ObservabilityEvent::SEVERITY_ERROR,
            'source' => $payload['displayName'] ?? $event->job->resolveName(),
            'summary' => 'Failed job: ' . ($payload['displayName'] ?? $event->job->resolveName()),
            'message' => $event->exception->getMessage(),
            'context' => [
                'connection' => $event->connectionName,
                'queue' => $event->job->getQueue(),
                'job_id' => $event->job->getJobId(),
                'attempts' => $event->job->attempts(),
                'exception' => $event->exception::class,
                'payload' => Arr::only($payload, ['uuid', 'displayName', 'job', 'maxTries', 'timeout']),
            ],
        ]);
    }

    public function recordMailFailure(string $summary, string $message, array $context = []): ?ObservabilityEvent
    {
        return $this->record([
            'type' => ObservabilityEvent::TYPE_MAIL_FAILURE,
            'severity' => ObservabilityEvent::SEVERITY_ERROR,
            'source' => 'mail',
            'summary' => $summary,
            'message' => $message,
            'context' => $context,
        ]);
    }

    public function recordWebhookFailure(string $provider, string $summary, string $message, array $context = [], string $severity = ObservabilityEvent::SEVERITY_ERROR): ?ObservabilityEvent
    {
        return $this->record([
            'type' => ObservabilityEvent::TYPE_WEBHOOK_FAILURE,
            'severity' => $severity,
            'source' => $provider,
            'summary' => $summary,
            'message' => $message,
            'url' => request()?->fullUrl(),
            'method' => request()?->method(),
            'route_name' => request()?->route()?->getName(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'context' => $context,
        ]);
    }

    public function brokenNamedRoutes(): array
    {
        $items = config('menu.items', []);
        $broken = [];

        $walk = function (array $menuItems, array $parents = []) use (&$walk, &$broken): void {
            foreach ($menuItems as $item) {
                $label = $item['label'] ?? 'Unnamed';
                $routeName = $item['route_name'] ?? null;

                if (is_string($routeName) && $routeName !== '#' && $routeName !== '' && ! Route::has($routeName)) {
                    $broken[] = [
                        'label' => $label,
                        'route_name' => $routeName,
                        'section' => implode(' / ', [...$parents, $label]),
                    ];
                }

                if (! empty($item['children']) && is_array($item['children'])) {
                    $walk($item['children'], [...$parents, $label]);
                }
            }
        };

        $walk($items);

        return $broken;
    }

    public function recentLogEntries(int $limit = 20): array
    {
        $logFile = $this->latestLaravelLogFile();

        if (! $logFile || ! is_readable($logFile)) {
            return [];
        }

        clearstatcache(true, $logFile);

        $size = filesize($logFile);

        if (! is_int($size) || $size <= 0) {
            return [];
        }

        $handle = fopen($logFile, 'rb');

        if (! $handle) {
            return [];
        }

        $bytes = min($size, 1024 * 1024);

        try {
            if ($bytes < $size) {
                fseek($handle, -$bytes, SEEK_END);
            } else {
                rewind($handle);
            }

            $chunk = fread($handle, $bytes);
        } finally {
            fclose($handle);
        }

        return collect(explode("\n", (string) $chunk))
            ->filter(fn ($line) => str_contains($line, '.ERROR:') || str_contains($line, '.CRITICAL:') || str_contains($line, '.ALERT:') || str_contains($line, '.EMERGENCY:') || str_contains($line, '.WARNING:'))
            ->reverse()
            ->take($limit)
            ->values()
            ->map(function ($line) {
                preg_match('/^\[(?<date>[^\]]+)\]\s+(?<env>[^.]+)\.(?<level>[A-Z]+):\s+(?<message>.*)$/', $line, $matches);

                return [
                    'date' => $matches['date'] ?? null,
                    'level' => $matches['level'] ?? 'LOG',
                    'message' => $this->limitText($matches['message'] ?? $line, 900),
                ];
            })
            ->all();
    }

    private function eventsTableExists(): bool
    {
        if (self::$eventsTableExists !== null) {
            return self::$eventsTableExists;
        }

        try {
            return self::$eventsTableExists = Schema::hasTable('observability_events');
        } catch (Throwable) {
            return self::$eventsTableExists = false;
        }
    }

    private function fingerprint(string $type, string $summary, ?string $routeName, ?string $url, ?string $source, array $context): string
    {
        return hash('sha256', implode('|', [
            $type,
            $source,
            $routeName,
            parse_url((string) $url, PHP_URL_PATH),
            $summary,
            $context['exception'] ?? '',
            $context['job_id'] ?? '',
        ]));
    }

    private function exceptionSummary(Throwable $exception): string
    {
        $message = trim($exception->getMessage());

        return $this->limitText(class_basename($exception) . ($message ? ': ' . $message : ''), 255);
    }

    private function isMailException(Throwable $exception): bool
    {
        $class = $exception::class;
        $message = strtolower($exception->getMessage());

        return str_contains($class, 'Mailer')
            || str_contains($class, 'Transport')
            || str_contains($class, 'Symfony\\Component\\Mailer')
            || str_contains($message, 'smtp')
            || str_contains($message, 'mail')
            || str_contains($message, 'email');
    }

    private function requestUrl(?Request $request): ?string
    {
        if (! $request) {
            return null;
        }

        return $request->fullUrl();
    }

    private function shouldSkipRequest(Request $request): bool
    {
        $path = $request->path();

        if ($path === 'up' || str_starts_with($path, '_debugbar')) {
            return true;
        }

        return (bool) preg_match('/\.(css|js|map|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf)$/i', $path);
    }

    private function sanitizeContext(mixed $value): mixed
    {
        if (is_array($value)) {
            return collect($value)
                ->take(40)
                ->map(function ($item, $key) {
                    if (is_string($key) && $this->isSensitiveContextKey($key)) {
                        return '[redacted]';
                    }

                    return $this->sanitizeContext($item);
                })
                ->all();
        }

        if (is_object($value)) {
            return $this->limitText(json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR) ?: $value::class, 1000);
        }

        if (is_string($value)) {
            return $this->limitText($value, 2000);
        }

        return $value;
    }

    private function isSensitiveContextKey(string $key): bool
    {
        $key = strtolower($key);

        return str_contains($key, 'password')
            || str_contains($key, 'token')
            || str_contains($key, 'secret')
            || str_contains($key, 'signature')
            || str_contains($key, 'authorization')
            || str_contains($key, 'api_key');
    }

    private function limitText(string $value, int $limit): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit - 3) . '...';
    }

    private function latestLaravelLogFile(): ?string
    {
        $files = glob(storage_path('logs/laravel*.log')) ?: [];

        if ($files === []) {
            return null;
        }

        usort($files, fn ($left, $right) => filemtime($right) <=> filemtime($left));

        return $files[0];
    }
}
