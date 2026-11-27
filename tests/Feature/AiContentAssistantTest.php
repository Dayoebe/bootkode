<?php

namespace Tests\Feature;

use App\Data\AiResponse;
use App\Exceptions\AiProviderException;
use App\Models\Ai\AiUsageRecord;
use App\Models\Core\User;
use App\Services\Ai\AiContentAssistantService;
use App\Services\Ai\AiManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;
use Tests\Support\FakeAiManager;
use Tests\Support\FakeContentProvider;

class AiContentAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('bootkode_ai.enabled', true);
        config()->set('bootkode_ai.content_assist_daily_requests', 30);
        config()->set('bootkode_ai.content_assist_requests_per_minute', 20);
        RateLimiter::clear('ai-content-assist:1');
    }

    public function test_field_generation_uses_bounded_context_and_records_usage(): void
    {
        $editor = User::factory()->create();
        $provider = new FakeContentProvider([
            new AiResponse(json_encode(['content' => '<b>Build practical Laravel APIs</b>'])),
        ]);
        $service = $this->service($provider);

        $result = $service->generateField(
            $editor,
            'course details',
            'title',
            ['label' => 'Course title', 'max' => 255],
            'improve',
            '<script>ignore()</script>Laravel APIs',
            ['category' => 'Web development', 'difficulty' => 'beginner'],
            'Keep it concise',
        );

        $this->assertSame('Build practical Laravel APIs', $result);
        $this->assertStringContainsString('Web development', $provider->messages[0][1]['content']);
        $this->assertStringNotContainsString('<script>', $provider->messages[0][1]['content']);
        $prompt = json_decode($provider->messages[0][1]['content'], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('plain_text', $prompt['output_format']);
        $this->assertSame('replace_with_complete_draft', $prompt['application_mode']);
        $this->assertSame('json_schema', $provider->options[0]['response_format']['type']);
        $this->assertDatabaseHas('ai_usage_records', ['user_id' => $editor->id, 'feature' => 'content_assist', 'status' => 'succeeded']);
    }

    public function test_form_generation_returns_only_defined_fields_and_normalizes_lists(): void
    {
        $editor = User::factory()->create();
        $provider = new FakeContentProvider([new AiResponse(json_encode(['fields' => [
            'title' => 'Practical PHP',
            'outcomes' => ['Build a small application', '<em>Explain routing</em>'],
        ]]))]);

        $result = $this->service($provider)->generateForm($editor, 'course details', [
            'title' => ['label' => 'Title', 'form' => true, 'max' => 100],
            'outcomes' => ['label' => 'Outcomes', 'form' => true, 'type' => 'list', 'max_items' => 5],
            'price' => ['label' => 'Price', 'form' => false],
        ], ['audience' => 'Beginners'], 'Draft a short course');

        $this->assertSame(['title' => 'Practical PHP', 'outcomes' => ['Build a small application', 'Explain routing']], $result);
        $this->assertArrayNotHasKey('price', $result);
    }

    public function test_append_prompt_uses_the_end_of_existing_content_and_requests_structured_markdown(): void
    {
        $editor = User::factory()->create();
        $provider = new FakeContentProvider([new AiResponse(json_encode(['content' => "## Next section\n\nUseful detail."]))]);
        $existing = str_repeat('Earlier material. ', 500).'Important ending context.';

        $result = $this->service($provider)->generateField(
            $editor,
            'complete blog article',
            'content',
            [
                'label' => 'Article body',
                'purpose' => 'A publication-ready article',
                'editorial_requirements' => 'Use descriptive sections and short paragraphs.',
                'rich_text' => true,
                'max' => 30000,
            ],
            'continue',
            $existing,
            ['title' => 'A practical article'],
            'Add a practical next section',
            'append',
        );

        $prompt = json_decode($provider->messages[0][1]['content'], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame("## Next section\n\nUseful detail.", $result);
        $this->assertSame('markdown', $prompt['output_format']);
        $this->assertSame('append_only_new_content', $prompt['application_mode']);
        $this->assertSame('Use descriptive sections and short paragraphs.', $prompt['editorial_requirements']);
        $this->assertStringContainsString('Important ending context.', $prompt['current_value']);
        $this->assertLessThanOrEqual(6000, mb_strlen($prompt['current_value']));
    }

    public function test_daily_quota_is_separate_and_enforced(): void
    {
        $editor = User::factory()->create();
        config()->set('bootkode_ai.content_assist_daily_requests', 1);
        $provider = new FakeContentProvider([new AiResponse('{"content":"First"}')]);
        $service = $this->service($provider);
        $definition = ['label' => 'Title', 'max' => 100];

        $service->generateField($editor, 'blog', 'title', $definition, 'generate', '', [], null);

        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessage('daily AI Assist limit');
        $service->generateField($editor, 'blog', 'title', $definition, 'generate', '', [], null);
    }

    public function test_provider_failure_is_normalized_and_recorded_without_prompt_content(): void
    {
        $editor = User::factory()->create();
        $provider = new FakeContentProvider([new AiProviderException('Provider unavailable.', 'unavailable')]);

        try {
            $this->service($provider)->generateField($editor, 'blog', 'title', ['label' => 'Title'], 'generate', '', [], null);
            $this->fail('The provider failure was not propagated.');
        } catch (AiProviderException $exception) {
            $this->assertSame('unavailable', $exception->reason);
        }

        $usage = AiUsageRecord::where('user_id', $editor->id)->firstOrFail();
        $this->assertSame('failed', $usage->status);
        $this->assertSame('unavailable', $usage->error_reason);
        $this->assertArrayNotHasKey('prompt', $usage->getAttributes());
    }

    public function test_disabled_ai_fails_without_consuming_usage(): void
    {
        $editor = User::factory()->create();
        config()->set('bootkode_ai.enabled', false);

        $this->expectException(AiProviderException::class);
        try {
            (new AiContentAssistantService(new AiManager()))->generateField($editor, 'blog', 'title', ['label' => 'Title'], 'generate', '', [], null);
        } finally {
            $this->assertSame(0, AiUsageRecord::count());
        }
    }

    private function service(FakeContentProvider $provider): AiContentAssistantService
    {
        return new AiContentAssistantService(new FakeAiManager($provider));
    }
}
