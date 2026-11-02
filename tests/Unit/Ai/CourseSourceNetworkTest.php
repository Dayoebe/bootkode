<?php

namespace Tests\Unit\Ai;

use App\Exceptions\CourseContentException;
use App\Services\CourseImport\GitHubContentClient;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CourseSourceNetworkTest extends TestCase
{
    public static function unsafeUrls(): array
    {
        return array_map(fn ($url) => [$url], [
            'http://api.github.com/test', 'https://api.github.com.evil.test/test',
            'file:///etc/passwd', 'https://169.254.169.254/latest/meta-data',
            'https://localhost/test', 'https://user:password@api.github.com/test',
            'https://api.github.com:8443/test', 'https://raw.githubusercontent.com@127.0.0.1/test',
        ]);
    }

    #[DataProvider('unsafeUrls')]
    public function test_unapproved_urls_are_refused_before_network_access(string $url): void
    {
        Http::fake();
        try {
            app(GitHubContentClient::class)->get($url, 'text/plain', 100);
            $this->fail('Unsafe URL accepted.');
        } catch (CourseContentException) {
            Http::assertNothingSent();
        }
    }

    public static function unsafeAddresses(): array
    {
        return array_map(fn ($address) => [$address], [
            '127.0.0.1', '10.0.0.2', '172.16.0.2', '192.168.1.1', '169.254.169.254',
            '0.0.0.0', '::1', 'fe80::1', 'fc00::1', '::ffff:127.0.0.1',
        ]);
    }

    #[DataProvider('unsafeAddresses')]
    public function test_private_dns_answers_are_refused(string $address): void
    {
        Http::fake();
        $client = $this->partialMock(GitHubContentClient::class, fn ($mock) => $mock->shouldReceive('addresses')->andReturn(['140.82.112.6', $address]));
        try {
            $client->get('https://api.github.com/test', 'text/plain', 100);
            $this->fail('Private DNS answer accepted.');
        } catch (CourseContentException) {
            Http::assertNothingSent();
        }
    }

    public function test_redirects_are_refused_and_public_address_is_pinned(): void
    {
        $client = $this->partialMock(GitHubContentClient::class, fn ($mock) => $mock->shouldReceive('addresses')->andReturn(['140.82.112.6']));
        Http::fake(function ($request, $options) {
            $this->assertFalse($options['allow_redirects']);
            $this->assertSame('', $options['proxy']);
            $this->assertSame(['api.github.com:443:140.82.112.6'], $options['curl'][CURLOPT_RESOLVE]);

            return Http::response('', 302, ['Location' => 'http://169.254.169.254/']);
        });
        $this->expectException(CourseContentException::class);
        $client->get('https://api.github.com/test', 'text/plain', 100);
    }

    public function test_download_bytes_are_bounded_even_without_a_content_length_header(): void
    {
        $client = $this->partialMock(GitHubContentClient::class, fn ($mock) => $mock->shouldReceive('addresses')->andReturn(['140.82.112.6']));
        Http::fake(fn () => Http::response(str_repeat('x', 101)));
        $this->expectException(CourseContentException::class);
        $client->get('https://api.github.com/test', 'text/plain', 100);
    }

    public function test_response_at_byte_limit_is_read_correctly(): void
    {
        $client = $this->partialMock(GitHubContentClient::class, fn ($mock) => $mock->shouldReceive('addresses')->andReturn(['140.82.112.6']));
        Http::fake(fn () => Http::response(str_repeat('x', 100)));
        $this->assertSame(str_repeat('x', 100), $client->get('https://api.github.com/test', 'text/plain', 100));
    }
}
