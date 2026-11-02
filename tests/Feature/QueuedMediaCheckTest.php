<?php

namespace Tests\Feature;

use App\Jobs\CheckCourseMedia;
use App\Livewire\CourseManagement\CourseQualityControl;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Services\CourseQualityService;
use App\Services\PublicMediaProbe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QueuedMediaCheckTest extends TestCase
{
    use RefreshDatabase;

    private function probe(array $addresses): PublicMediaProbe
    {
        return new class($addresses) extends PublicMediaProbe
        {
            public function __construct(private array $resolved) {}

            public function addresses(string $host): array
            {
                return $this->resolved;
            }
        };
    }

    public function test_private_loopback_mapped_ipv6_and_mixed_dns_are_never_requested(): void
    {
        Http::fake();
        foreach ([['127.0.0.1'], ['10.0.0.3'], ['169.254.169.254'], ['::1'], ['::ffff:127.0.0.1'], ['8.8.8.8', '192.168.1.1']] as $addresses) {
            $this->assertSame('unchecked', $this->probe($addresses)->check('https://media.example.test/video')['status']);
        }
        Http::assertNothingSent();
    }

    public function test_metadata_is_cached_and_redirects_never_trigger_a_second_request(): void
    {
        Http::fake(['media.example.test/*' => Http::response('', 302, ['Location' => 'http://127.0.0.1/admin'])]);
        $probe = $this->probe(['8.8.8.8']);
        $this->assertSame('unchecked', $probe->check('https://media.example.test/redirect')['status']);
        $probe->check('https://media.example.test/redirect');
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->method() === 'HEAD');
    }

    public function test_metadata_success_and_gone_status_are_truthful_without_downloading_video(): void
    {
        Http::fake(['media.example.test/video' => Http::response('', 200), 'media.example.test/gone' => Http::response('', 410)]);
        $probe = $this->probe(['8.8.8.8']);
        $this->assertSame('ok', $probe->check('https://media.example.test/video')['status']);
        $this->assertSame('broken', $probe->check('https://media.example.test/gone')['status']);
        Http::assertSentCount(2);
        Http::assertNotSent(fn ($request) => $request->method() !== 'HEAD');
    }

    public function test_failed_metadata_never_exposes_url_tokens_or_provider_exceptions(): void
    {
        Http::fake(fn () => throw new \RuntimeException('private-probe-marker'));
        $result = $this->probe(['8.8.8.8'])->check('https://media.example.test/file?token=private-query');
        $this->assertSame('unchecked', $result['status']);
        $this->assertStringNotContainsString('private-probe-marker', $result['detail']);
        $this->assertStringNotContainsString('private-query', $result['detail']);
    }

    public function test_remote_quality_action_queues_and_rechecks_current_admin_access_at_delivery(): void
    {
        Bus::fake();
        Http::fake();
        Role::findOrCreate('academy_admin', 'web');
        $admin = User::factory()->create(['role' => 'academy_admin']);
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $admin->id, 'title' => 'Synthetic QA course', 'slug' => 'synthetic-qa-course', 'description' => 'Synthetic content']));
        Livewire::actingAs($admin)->test(CourseQualityControl::class)->call('runQualityCheck', $course->id, true)->assertDispatched('notify');
        Bus::assertDispatched(CheckCourseMedia::class, fn ($job) => $job->courseId === $course->id && $job->editorId === $admin->id);
        Http::assertNothingSent();
        $this->assertDatabaseCount('course_quality_checks', 0);
        $admin->syncRoles(['student']);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new CheckCourseMedia($course->id, $admin->id))->handle(app(CourseQualityService::class));
    }
}
