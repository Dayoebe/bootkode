<?php

namespace Tests\Unit;

use App\Models\Career\JobPortal;
use Tests\TestCase;

class CareerApplicationDestinationTest extends TestCase
{
    public function test_external_application_destinations_allow_supported_schemes_only(): void
    {
        foreach (['javascript:alert(1)', 'data:text/html,example', 'file:///etc/passwd', '//example.test/apply'] as $url) {
            $job = new JobPortal(['application_method' => 'external_link', 'application_url' => $url]);
            $this->assertNull($job->application_destination);
        }
        $job = new JobPortal(['application_method' => 'external_link', 'application_url' => 'https://example.test/apply']);
        $this->assertSame('https://example.test/apply', $job->application_destination);
        $job->fill(['application_method' => 'email', 'application_email' => 'hiring@example.test']);
        $this->assertSame('mailto:hiring@example.test', $job->application_destination);
        $job->fill(['application_method' => 'phone', 'application_phone' => '+234 800 123 4567']);
        $this->assertSame('tel:+2348001234567', $job->application_destination);
    }
}
