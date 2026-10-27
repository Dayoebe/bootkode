<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use App\Livewire\Ai\CodingMentor;
use App\Livewire\CourseManagement\CourseContentStudio;
use App\Models\Core\User;
use App\Services\Ai\AiConnectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AiConnectionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['super_admin', 'academy_admin', 'instructor', 'student', 'content_editor'] as $role) {
            Role::findOrCreate($role, 'web');
        }
        config([
            'bootkode_ai.enabled' => true,
            'bootkode_ai.provider' => 'groq',
            'bootkode_ai.retries' => 0,
            'bootkode_ai.providers.groq' => ['key' => 'private-test-key', 'model' => 'configured-model', 'base_url' => 'https://groq.example.test/v1'],
        ]);
        Http::preventStrayRequests();
    }

    private function reply(string $content = '{"status":"ok"}', int $status = 200): void
    {
        Http::fake(fn () => Http::response(['choices' => [['message' => ['content' => $content]]]], $status));
    }

    public function test_probe_checks_structured_output_without_sending_or_creating_user_course_data(): void
    {
        $this->reply();
        $result = app(AiConnectionService::class)->check();
        $this->assertTrue($result['success']);
        $this->assertSame('groq', $result['provider']);
        $this->assertArrayNotHasKey('key', $result);
        Http::assertSent(fn ($request) => $request->url() === 'https://groq.example.test/v1/chat/completions'
            && $request['response_format']['json_schema']['strict'] === true
            && $request['max_tokens'] === 512
            && count($request['messages']) === 2
            && $request['messages'][1]['content'] === 'Verify a structured reply.');
        $this->assertDatabaseCount('ai_conversations', 0);
        $this->assertDatabaseCount('courses', 0);
        $this->assertDatabaseCount('ai_usage_records', 0);
    }

    public function test_missing_key_and_disabled_ai_make_no_network_request(): void
    {
        Http::fake();
        foreach (['disabled', 'not_configured'] as $reason) {
            config(['bootkode_ai.enabled' => $reason !== 'disabled', 'bootkode_ai.providers.groq.key' => null]);
            try {
                app(AiConnectionService::class)->check();
                $this->fail('Unavailable AI accepted.');
            } catch (AiProviderException $exception) {
                $this->assertSame($reason, $exception->reason);
            }
        }
        Http::assertNothingSent();
    }

    public function test_nonconforming_reply_is_not_reported_as_a_verified_connection(): void
    {
        $this->reply('{"status":"ok","private":"unexpected"}');
        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessage('structured output could not be verified');
        app(AiConnectionService::class)->check();
    }

    public function test_command_reports_success_and_failure_with_exit_codes(): void
    {
        $this->reply();
        $this->artisan('bootkode:ai-check')->expectsOutputToContain('Groq: Connection verified.')->assertSuccessful();
        config(['bootkode_ai.enabled' => false]);
        $this->artisan('bootkode:ai-check')->expectsOutputToContain('currently disabled')->assertFailed();
    }

    public function test_admin_probe_is_explicit_rate_limited_and_contains_no_credential_in_component_state(): void
    {
        $this->reply();
        $admin = User::factory()->create(['role' => 'super_admin']);
        $component = Livewire::actingAs($admin)->test(CourseContentStudio::class)->assertSee('Groq · Configured');
        Http::assertNothingSent();
        $component->call('checkAiConnection')->assertSet('connectionCheck.success', true)->assertSee('Connection verified.');
        $this->assertStringNotContainsString('private-test-key', $component->html());
        $component->call('checkAiConnection')->assertSet('connectionCheck.success', true);
        $component->call('checkAiConnection')->assertSet('connectionCheck.success', false)->assertSee('Wait a minute');
        Http::assertSentCount(2);
    }

    public function test_instructor_cannot_call_the_admin_connection_probe(): void
    {
        Http::fake();
        $instructor = User::factory()->create(['role' => 'instructor']);
        Livewire::actingAs($instructor)->test(CourseContentStudio::class)->assertDontSee('Check AI connection')->call('checkAiConnection')->assertForbidden();
        Http::assertNothingSent();
    }

    public function test_live_probe_failure_uses_a_safe_message_without_provider_body_or_key(): void
    {
        $this->reply('private-provider-response', 401);
        $admin = User::factory()->create(['role' => 'academy_admin']);
        Livewire::actingAs($admin)->test(CourseContentStudio::class)->call('checkAiConnection')
            ->assertSet('connectionCheck.success', false)->assertSee('AI authentication failed')
            ->assertDontSee('private-provider-response')->assertDontSee('private-test-key');
    }

    public function test_mentor_rechecks_current_role_on_livewire_requests(): void
    {
        Http::fake();
        $user = User::factory()->create(['role' => 'student']);
        $component = Livewire::actingAs($user)->test(CodingMentor::class);
        $user->syncRoles(['content_editor']);
        $component->call('newConversation')->assertForbidden();
        $this->assertDatabaseCount('ai_conversations', 0);
        Http::assertNothingSent();
    }
}
