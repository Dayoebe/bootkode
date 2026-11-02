<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/** Metadata-only probe. Course links are untrusted input, never an SSRF destination. */
class PublicMediaProbe
{
    public function check(string $url): array
    {
        $parts = parse_url($url);
        if (! $parts || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || strlen($url) > 2048
            || isset($parts['user']) || isset($parts['pass']) || (isset($parts['port']) && $parts['port'] !== 443)) {
            return ['status' => 'unchecked', 'detail' => 'Only public HTTPS metadata can be checked automatically.'];
        }
        $host = $parts['host'];
        $addresses = $this->addresses($host);
        if ($addresses === []) {
            return ['status' => 'unchecked', 'detail' => 'Public media host could not be resolved.'];
        }
        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                || str_starts_with(strtolower($address), '::ffff:')) {
                return ['status' => 'unchecked', 'detail' => 'Private and reserved network destinations are blocked.'];
            }
        }

        return Cache::remember('public-media-probe:'.hash('sha256', $url), 600, function () use ($url, $host, $addresses): array {
            $address = str_contains($addresses[0], ':') ? '['.$addresses[0].']' : $addresses[0];
            try {
                $response = Http::connectTimeout(2)->timeout(5)->withOptions([
                    'allow_redirects' => false, 'proxy' => '',
                    'curl' => [CURLOPT_RESOLVE => ["{$host}:443:{$address}"]],
                ])->head($url);
                $code = $response->status();
                $response->close();

                return [
                    'status' => $code >= 200 && $code < 300 ? 'ok' : (in_array($code, [404, 410], true) ? 'broken' : 'unchecked'),
                    'detail' => 'HTTP '.$code.($code >= 300 && $code < 400 ? '; redirect target requires manual review.' : ''),
                ];
            } catch (Throwable) {
                return ['status' => 'unchecked', 'detail' => 'Remote metadata could not be verified. Retry later or review the link manually.'];
            }
        });
    }

    public function addresses(string $host): array
    {
        $literal = trim($host, '[]');
        if (filter_var($literal, FILTER_VALIDATE_IP)) {
            return [$literal];
        }
        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        return array_values(array_unique(array_filter(array_map(fn ($record) => $record['ip'] ?? $record['ipv6'] ?? null, $records ?: []))));
    }
}
