<?php

namespace App\Services\CourseImport;

use App\Exceptions\CourseContentException;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\StreamWrapper;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/** Fixed-host, DNS-pinned, bounded retrieval. Never follows source-controlled URLs. */
class GitHubContentClient
{
    public function get(string $url, string $accept, int $maxBytes): string
    {
        $parts = parse_url($url);
        $host = $parts['host'] ?? '';
        if (($parts['scheme'] ?? '') !== 'https'
            || ! in_array($host, ['api.github.com', 'raw.githubusercontent.com'], true)
            || isset($parts['port'])
            || isset($parts['fragment']) || isset($parts['user']) || isset($parts['pass'])) {
            throw new CourseContentException('The source URL is not supported.');
        }

        $addresses = $this->addresses($host);
        if ($addresses === []) {
            throw new CourseContentException('The source host could not be resolved.');
        }
        foreach ($addresses as $address) {
            if (! filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
                || str_starts_with(strtolower($address), '::ffff:')) {
                throw new CourseContentException('The source host did not resolve to a public address.');
            }
        }

        $address = str_contains($addresses[0], ':') ? '['.$addresses[0].']' : $addresses[0];
        $sink = Utils::streamFor(fopen('php://temp', 'w+'));
        $bytes = 0;
        $boundedSink = FnStream::decorate($sink, ['write' => function (string $chunk) use ($sink, &$bytes, $maxBytes) {
            $bytes += strlen($chunk);
            if ($bytes > $maxBytes) {
                throw new CourseContentException('The source response exceeded the safe import limit.');
            }

            return $sink->write($chunk);
        }]);
        try {
            $response = Http::accept($accept)->withUserAgent('BootKode-CourseImporter/2')
                ->connectTimeout(5)->timeout(12)
                ->withOptions([
                    'allow_redirects' => false,
                    'proxy' => '',
                    'sink' => StreamWrapper::getResource($boundedSink),
                    'on_headers' => function (ResponseInterface $response) use ($maxBytes) {
                        if ((int) $response->getHeaderLine('Content-Length') > $maxBytes) {
                            throw new CourseContentException('The source response exceeded the safe import limit.');
                        }
                    },
                    'curl' => [CURLOPT_RESOLVE => ["{$host}:443:{$address}"]],
                ])->get($url);

            if (! $response->successful()) {
                throw new CourseContentException($response->status() === 404
                    ? 'The selected curriculum path or file no longer exists.'
                    : 'The source repository could not be retrieved. Check the path or try again later.');
            }
            if ((int) $response->header('Content-Length') > $maxBytes) {
                throw new CourseContentException('The source response exceeded the safe import limit.');
            }
            $stream = $response->toPsrResponse()->getBody();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
            $body = '';
            while (! $stream->eof()) {
                $body .= $stream->read(min(8192, $maxBytes + 1 - strlen($body)));
                if (strlen($body) > $maxBytes) {
                    throw new CourseContentException('The source response exceeded the safe import limit.');
                }
            }

            return $body;
        } catch (CourseContentException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new CourseContentException('The source repository could not be reached. Try again later.');
        } finally {
            if (isset($response)) {
                $response->close();
            }
            $boundedSink->close();
        }
    }

    /** Kept separate so HTTP fixtures never depend on external DNS. */
    public function addresses(string $host): array
    {
        $records = dns_get_record($host, DNS_A | DNS_AAAA);

        return array_values(array_unique(array_filter(array_map(
            fn (array $record) => $record['ip'] ?? $record['ipv6'] ?? null,
            $records ?: []
        ))));
    }
}
