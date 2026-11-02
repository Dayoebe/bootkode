<?php

namespace Tests\Feature;

use App\Jobs\ExpandImportedCourse;
use App\Jobs\GenerateCourseWithAi;
use App\Jobs\ImportCourseContent;
use App\Logging\ProductionLogProcessor;
use App\Logging\SanitizeProductionLogs;
use App\Models\Core\User;
use App\Services\Career\ResumeAiService;
use App\Services\PaystackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_config_cannot_enable_debug_with_a_stale_environment_value(): void
    {
        $process = new \Symfony\Component\Process\Process([PHP_BINARY, '-r',
            'require "vendor/autoload.php"; $config = require "config/app.php"; echo json_encode($config["debug"]);',
        ], base_path(), ['APP_ENV' => 'production', 'APP_DEBUG' => 'true']);
        $process->mustRun();
        $this->assertSame('false', $process->getOutput());
    }

    public function test_production_logs_redact_credentials_nested_payloads_urls_and_exception_arguments(): void
    {
        $this->app['env'] = 'production';
        config(['services.adzuna.app_key' => 'private-adzuna-marker']);
        $record = new LogRecord(new \DateTimeImmutable, 'test', Level::Error,
            'Failure private-adzuna-marker https://example.test/callback?token=private-url-marker learner@example.test cloudinary://api-user:private-cloudinary-marker@asset-cloud',
            ['nested' => ['api_key' => 'hidden-key', 'email' => 'hidden@example.test', 'payload' => ['private' => 'hidden-prompt']],
                'exception' => new \RuntimeException('hidden-exception-marker'), 'record_id' => 42]);
        $safe = (new ProductionLogProcessor)($record);
        $serialized = json_encode([$safe->message, $safe->context]);
        foreach (['private-adzuna-marker', 'private-url-marker', 'learner@example.test', 'hidden-key', 'hidden@example.test', 'hidden-prompt', 'hidden-exception-marker', 'private-cloudinary-marker'] as $marker) {
            $this->assertStringNotContainsString($marker, $serialized);
        }
        $this->assertSame(42, $safe->context['record_id']);
        $this->assertSame(\RuntimeException::class, $safe->context['exception']['type']);
    }

    public function test_production_exception_reporting_keeps_code_location_without_raw_exception_text(): void
    {
        $this->app['env'] = 'production';
        $path = tempnam(sys_get_temp_dir(), 'bootkode-log-test-');
        config(['logging.default' => 'production-probe', 'logging.channels.production-probe' => [
            'driver' => 'single', 'path' => $path, 'level' => 'debug', 'tap' => [SanitizeProductionLogs::class],
        ]]);
        try {
            report(new \RuntimeException('private-database-binding-marker'));
            $text = file_get_contents($path);
            $this->assertStringContainsString('Unhandled application exception', $text);
            $this->assertStringNotContainsString('private-database-binding-marker', $text);
            $this->assertDatabaseMissing('observability_events', ['message' => 'private-database-binding-marker']);
        } finally {
            Log::forgetChannel('production-probe');
            unlink($path);
        }
    }

    public function test_course_job_timeout_and_overlap_lock_fit_the_reservation_for_crash_recovery(): void
    {
        foreach ([new GenerateCourseWithAi(1), new ExpandImportedCourse(1), new ImportCourseContent(1)] as $job) {
            $reservation = config('queue.connections.'.$job->connection.'.retry_after');
            $lock = $job->middleware()[0];
            $this->assertGreaterThan($job->timeout, $reservation);
            $this->assertGreaterThan($job->timeout, $lock->expiresAfter);
            $this->assertLessThan($reservation, $lock->expiresAfter);
        }
    }

    public function test_heavy_connections_support_redis_without_inheriting_the_sync_queue(): void
    {
        $repository = Env::getRepository();
        $oldCourse = env('COURSE_QUEUE_DRIVER');
        $oldBulk = env('BULK_QUEUE_DRIVER');
        try {
            $repository->set('COURSE_QUEUE_DRIVER', 'redis');
            $repository->set('BULK_QUEUE_DRIVER', 'redis');
            $queue = require config_path('queue.php');
            foreach (['course-content', 'bulk-enrollment'] as $name) {
                $this->assertSame('redis', $queue['connections'][$name]['driver']);
                $this->assertSame('default', $queue['connections'][$name]['connection']);
                $this->assertTrue($queue['connections'][$name]['after_commit']);
                $this->assertSame(960, $queue['connections'][$name]['retry_after']);
            }
        } finally {
            $oldCourse === null ? $repository->clear('COURSE_QUEUE_DRIVER') : $repository->set('COURSE_QUEUE_DRIVER', $oldCourse);
            $oldBulk === null ? $repository->clear('BULK_QUEUE_DRIVER') : $repository->set('BULK_QUEUE_DRIVER', $oldBulk);
        }
    }

    public function test_resume_ai_is_cached_per_user_and_tracks_actual_provider_usage(): void
    {
        config(['services.openai.key' => 'private-test-key']);
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Review your own practical examples.']]], 'usage' => ['prompt_tokens' => 20, 'completion_tokens' => 8]])]);
        $this->actingAs(User::factory()->create());
        $service = app(ResumeAiService::class);
        $this->assertSame($service->suggest('Improve my supplied summary'), $service->suggest('Improve my supplied summary'));
        Http::assertSentCount(1);
        $this->assertDatabaseHas('ai_usage_records', ['user_id' => auth()->id(), 'feature' => 'resume_assistance', 'status' => 'succeeded', 'prompt_tokens' => 20]);
        $this->actingAs(User::factory()->create());
        $service->suggest('Improve my supplied summary');
        Http::assertSentCount(2);
    }

    public function test_resume_ai_daily_limit_blocks_before_another_provider_call(): void
    {
        config(['services.openai.key' => 'private-test-key', 'bootkode_ai.resume_daily_requests' => 1]);
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'A suggestion.']]]])]);
        $this->actingAs(User::factory()->create());
        app(ResumeAiService::class)->suggest('First request');
        try {
            app(ResumeAiService::class)->suggest('Different request');
            $this->fail('Quota was not enforced');
        } catch (\App\Exceptions\AiProviderException $exception) {
            $this->assertSame('daily_limit', $exception->reason);
        }
        Http::assertSentCount(1);
    }

    public function test_resume_provider_failure_is_recorded_without_raw_payload_or_key(): void
    {
        config(['services.openai.key' => 'private-test-key']);
        Http::fake(['api.openai.com/*' => Http::response(['error' => 'private-provider-body-marker'], 500)]);
        $this->actingAs(User::factory()->create());
        try {
            app(ResumeAiService::class)->suggest('A summary');
            $this->fail('Provider error accepted');
        } catch (\App\Exceptions\AiProviderException $exception) {
            $this->assertStringNotContainsString('private-provider-body-marker', $exception->getMessage());
        }
        $this->assertDatabaseHas('ai_usage_records', ['feature' => 'resume_assistance', 'status' => 'failed', 'error_reason' => 'invalid_response']);
    }

    public function test_resume_ai_rejects_deactivated_accounts_before_http(): void
    {
        Http::fake();
        $this->actingAs(User::factory()->create(['is_active' => false]));
        try {
            app(ResumeAiService::class)->suggest('A summary');
            $this->fail('Inactive account reached AI');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        Http::assertNothingSent();
    }

    public function test_paystack_connection_failure_does_not_log_exception_credentials(): void
    {
        config(['services.paystack.secret_key' => 'private-payment-key']);
        Http::fake(fn () => throw new \RuntimeException('private-payment-key private-provider-body'));
        Log::shouldReceive('error')->once()->with('Paystack verification error', ['exception_type' => \RuntimeException::class]);
        $this->assertFalse(app(PaystackService::class)->verifyTransaction('reference')['success']);
    }

    public function test_read_only_preflight_rejects_local_settings_and_does_not_claim_skipped_database_checks(): void
    {
        $before = User::count();
        $this->artisan('bootkode:production-check', ['--skip-database' => true, '--json' => true])->assertFailed();
        $this->assertSame($before, User::count());
    }
}
