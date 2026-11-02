<?php

namespace App\Logging;

use Monolog\LogRecord;
use Throwable;

/** Defence in depth for legacy log callers. Never serialize exception arguments. */
class ProductionLogProcessor
{
    public function __invoke(LogRecord $record): LogRecord
    {
        if (! app()->environment('production')) {
            return $record;
        }

        return $record->with(
            message: $this->text($record->message),
            context: $this->sanitize($record->context),
            extra: $this->sanitize($record->extra),
        );
    }

    public function sanitize(mixed $value, int $depth = 0): mixed
    {
        if ($depth > 8) {
            return '[truncated]';
        }
        if ($value instanceof Throwable) {
            return ['type' => $value::class, 'file' => basename($value->getFile()), 'line' => $value->getLine()];
        }
        if (is_array($value)) {
            $result = [];
            foreach (array_slice($value, 0, 40, true) as $key => $item) {
                $result[$key] = is_string($key) && preg_match('/password|token|secret|signature|authorization|api.?key|app.?key|credential|email|cookie|payload|body|prompt|content|query|sql|binding|trace|user.?data|proof|reference|account.?number/i', $key)
                    ? '[redacted]' : $this->sanitize($item, $depth + 1);
            }

            return $result;
        }
        if (is_object($value)) {
            return '[object '.$value::class.']';
        }

        return is_string($value) ? $this->text($value) : $value;
    }

    public function text(string $value): string
    {
        foreach (['app.key', 'database.connections.mysql.password', 'services.paystack.secret_key', 'services.adzuna.app_key', 'services.adzuna.app_id', 'services.openai.key', 'bootkode_ai.providers.groq.key', 'mail.mailers.smtp.password', 'services.google.client_secret', 'filesystems.disks.cloudinary.secret'] as $key) {
            $secret = config($key);
            if (is_string($secret) && strlen($secret) >= 4) {
                $value = str_replace([$secret, rawurlencode($secret)], '[redacted]', $value);
            }
        }
        $value = preg_replace('/\bBearer\s+[^\s,;]+/i', 'Bearer [redacted]', $value);
        $value = preg_replace('~(https?://)[^\s/]+:[^\s/@]+@~i', '$1[redacted]@', $value);
        $value = preg_replace('~(https?://[^\s?]+)\?[^\s]+~i', '$1?[redacted]', $value);
        $value = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[email]', $value);
        $value = preg_replace('/\b(password|api_key|app_key|secret|token|signature)\s*[=:]\s*[^\s,;]+/i', '$1=[redacted]', $value);
        $value = preg_replace('/\b(SQLSTATE\[.*|(?:select|insert into|update|delete from)\s+.*)/is', '[database details withheld]', $value);

        return mb_substr($value, 0, 2000);
    }
}
