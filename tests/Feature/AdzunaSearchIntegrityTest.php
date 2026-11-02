<?php

namespace Tests\Feature;

use App\Livewire\Career\JobSearch;
use App\Models\Core\User;
use App\Services\Career\AdzunaSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Tests\TestCase;

class AdzunaSearchIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function setupSearch(): void
    {
        $this->actingAs(User::factory()->create());
        config(['services.adzuna.app_id' => 'PRIVATE-APP-ID', 'services.adzuna.app_key' => 'PRIVATE-APP-KEY']);
        Http::preventStrayRequests();
    }

    private function input(array $changes = []): array
    {
        return array_replace(['country_code' => 'gb', 'currentPage' => 1, 'sector' => 'SQL', 'location' => '', 'company' => '',
            'salary_min' => '', 'salary_max' => '', 'contract_type' => '', 'sort_by' => 'date', 'distance' => 10], $changes);
    }

    private function job(array $changes = []): array
    {
        return array_replace(['id' => '12345', 'title' => 'SQL analyst', 'description' => 'An actual provider response snippet.', 'created' => '2026-11-01T12:00:00Z',
            'company' => ['display_name' => 'Provider fixture'], 'location' => ['display_name' => 'London'], 'category' => ['label' => 'Analytics'],
            'redirect_url' => 'https://www.adzuna.co.uk/jobs/land/ad/12345?tracking=original', 'salary_min' => 25000, 'salary_max' => 35000], $changes);
    }

    public function test_missing_configuration_does_not_make_a_network_request(): void
    {
        $this->setupSearch();
        config(['services.adzuna.app_key' => null]);
        Livewire::test(JobSearch::class)->assertHasErrors('sector')->assertSee('not configured');
        Http::assertNothingSent();
    }

    public function test_duplicate_and_unsafe_destinations_are_removed_and_original_tracking_is_preserved(): void
    {
        $this->setupSearch();
        Http::fake(['api.adzuna.com/*' => Http::response(['results' => [$this->job(), $this->job(), $this->job(['id' => '2', 'redirect_url' => 'javascript:alert(1)']),
            $this->job(['id' => '3', 'redirect_url' => 'https://adzuna.co.uk.attacker.test/trap']), $this->job(['id' => '4', 'redirect_url' => 'https://attacker@www.adzuna.co.uk/trap'])], 'count' => 5])]);
        $result = app(AdzunaSearchService::class)->search($this->input());
        $this->assertCount(1, $result['jobs']);
        $this->assertSame($this->job()['redirect_url'], $result['jobs'][0]['redirect_url']);
        $this->assertArrayNotHasKey('match_score', $result['jobs'][0]);
    }

    public function test_cache_has_no_learner_flags_and_provider_visits_never_create_applications(): void
    {
        $this->setupSearch();
        Http::fake(['api.adzuna.com/*' => Http::response(['results' => [$this->job()], 'count' => 1])]);
        $component = Livewire::test(JobSearch::class)->call('saveJob', '12345')->call('openExternalJob', '12345');
        $this->assertTrue($component->get('jobs')[0]['is_saved']);
        $this->assertTrue($component->get('jobs')[0]['was_opened']);
        $this->assertDatabaseCount('job_applications', 0);
        session()->forget(['saved_jobs', 'opened_external_jobs']);
        $second = Livewire::actingAs(User::factory()->create())->test(JobSearch::class);
        $this->assertFalse($second->get('jobs')[0]['is_saved']);
        $this->assertFalse($second->get('jobs')[0]['was_opened']);
        $this->assertSame([], $second->get('popular_searches'));
        $second->assertSee('Adzuna')->assertDontSee('% Match')->assertDontSee('1,250');
        $this->assertStringNotContainsString('PRIVATE-APP-KEY', json_encode($second->snapshot));
        Http::assertSentCount(1);
    }

    public function test_failure_logs_do_not_include_credentials_query_body_or_exception_messages(): void
    {
        $this->setupSearch();
        Log::spy();
        Http::fake(['api.adzuna.com/*' => Http::response('PRIVATE PROVIDER RESPONSE', 500)]);
        try {
            app(AdzunaSearchService::class)->search($this->input());
            $this->fail('Expected provider failure');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertStringNotContainsString('PRIVATE', $e->getMessage());
        }
        Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context) => $context === ['provider' => 'adzuna', 'status' => 500])->once();
        Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context) => $context === ['provider' => 'adzuna', 'exception_type' => \RuntimeException::class])->once();
    }

    public function test_network_exception_url_cannot_disclose_credentials(): void
    {
        $this->setupSearch();
        Log::spy();
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('PRIVATE-APP-KEY in URL'));
        Livewire::test(JobSearch::class)->assertHasErrors('sector')->assertDontSee('PRIVATE-APP-KEY');
        Log::shouldHaveReceived('warning')->withArgs(fn ($message, $context) => $context === ['provider' => 'adzuna', 'exception_type' => \Illuminate\Http\Client\ConnectionException::class])->once();
    }

    public function test_later_livewire_requests_reload_credentials_and_filters_reset_the_actual_page(): void
    {
        $this->setupSearch();
        Http::fake(['api.adzuna.com/*' => Http::response(['results' => [$this->job()], 'count' => 50])]);
        $component = Livewire::test(JobSearch::class)->call('nextPage')->assertSet('currentPage', 2)
            ->set('sector', 'Accounting')->assertSet('currentPage', 1);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/search/2') && $request['app_key'] === 'PRIVATE-APP-KEY');
        $this->assertSame('GBP 25,000 – 35,000', $component->get('jobs')[0]['salary_formatted']);
    }

    public function test_invalid_filters_are_rejected_before_requesting_provider_data(): void
    {
        $this->setupSearch();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        try {
            app(AdzunaSearchService::class)->search($this->input(['country_code' => '../private', 'currentPage' => 1000]));
        } finally {
            Http::assertNothingSent();
        }
    }
}
