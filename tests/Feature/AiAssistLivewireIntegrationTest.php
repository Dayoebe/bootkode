<?php

namespace Tests\Feature;

use App\Data\AiResponse;
use App\Livewire\Blog\AdminBlogPostForm;
use App\Livewire\CourseManagement\CourseForm;
use App\Models\Content\BlogCategory;
use App\Models\Core\User;
use App\Models\Learning\CourseCategory;
use App\Services\Ai\AiManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tests\Support\FakeAiManager;
use Tests\Support\FakeContentProvider;

class AiAssistLivewireIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('bootkode_ai.enabled', true);
        config()->set('bootkode_ai.content_assist_requests_per_minute', 20);
        Role::findOrCreate(User::ROLE_INSTRUCTOR, 'web');
        Role::findOrCreate(User::ROLE_CONTENT_EDITOR, 'web');
    }

    public function test_course_field_draft_preserves_existing_content_until_confirmed_application(): void
    {
        $editor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);
        CourseCategory::create(['name' => 'Web Development']);
        $this->fakeManager([new AiResponse('{"content":"Practical Laravel APIs"}')]);

        Livewire::actingAs($editor)->test(CourseForm::class)
            ->set('title', 'My original title')
            ->call('openAiAssist', 'title')
            ->set('aiAssistAction', 'improve')
            ->call('generateAiAssist')
            ->assertSet('title', 'My original title')
            ->assertSet('aiAssistDraft.title', 'Practical Laravel APIs')
            ->call('applyAiAssistField', 'title')
            ->assertHasErrors('aiAssist')
            ->set('aiAssistConfirmReplace', true)
            ->call('applyAiAssistField', 'title')
            ->assertSet('title', 'Practical Laravel APIs')
            ->assertDispatched('ai-assist-applied');
    }

    public function test_blog_assistance_maps_only_an_existing_category_and_never_changes_publish_state(): void
    {
        $editor = User::factory()->create(['role' => User::ROLE_CONTENT_EDITOR]);
        $category = BlogCategory::create(['name' => 'Learning', 'is_active' => true]);
        $this->fakeManager([new AiResponse('{"content":["Learning"]}')]);

        Livewire::actingAs($editor)->test(AdminBlogPostForm::class)
            ->assertSet('status', 'draft')
            ->call('openAiAssist', 'category_ids')
            ->call('generateAiAssist')
            ->call('applyAiAssistField', 'category_ids')
            ->assertSet('category_ids', [$category->id])
            ->assertSet('status', 'draft')
            ->assertSet('is_featured', false);
    }

    public function test_blog_rich_text_draft_is_applied_as_plain_text_and_dispatches_editor_sync(): void
    {
        $editor = User::factory()->create(['role' => User::ROLE_CONTENT_EDITOR]);
        $this->fakeManager([new AiResponse('{"content":"<h2>Start safely</h2><script>alert(1)</script>"}')]);

        Livewire::actingAs($editor)->test(AdminBlogPostForm::class)
            ->call('openAiAssist', 'content')
            ->call('generateAiAssist')
            ->assertSet('content', '')
            ->assertSet('aiAssistDraft.content', 'Start safely')
            ->call('applyAiAssistField', 'content')
            ->assertSet('content', 'Start safely')
            ->assertDispatched('ai-assist-applied');
    }

    public function test_unauthorized_roles_cannot_open_course_ai_assist(): void
    {
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

        Livewire::actingAs($student)->test(CourseForm::class)->assertForbidden();
    }

    private function fakeManager(array $responses): void
    {
        $this->app->instance(AiManager::class, new FakeAiManager(new FakeContentProvider($responses)));
    }
}
