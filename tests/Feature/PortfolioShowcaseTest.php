<?php

namespace Tests\Feature;

use App\Livewire\Career\PortfolioBuilder;
use App\Models\Content\Portfolio;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PortfolioShowcaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_learner_can_create_a_project_and_it_is_private_by_default(): void
    {
        $learner = $this->learner();

        Livewire::actingAs($learner)
            ->test(PortfolioBuilder::class)
            ->set('title', 'Learning tracker')
            ->set('description', 'A focused tracker for practical learning goals.')
            ->set('technologies', 'Laravel, Livewire, MySQL')
            ->set('category', 'web-development')
            ->call('saveProject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $learner->id,
            'title' => 'Learning tracker',
            'visibility' => 'private',
            'is_public' => false,
        ]);
    }

    public function test_learner_can_update_and_delete_an_owned_project(): void
    {
        $learner = $this->learner();
        $project = $this->project($learner, ['title' => 'Original project']);

        Livewire::actingAs($learner)
            ->test(PortfolioBuilder::class)
            ->call('editProject', $project->id)
            ->set('title', 'Published project')
            ->set('description', 'An updated description for the completed project.')
            ->set('project_url', 'https://demo.example.com/project')
            ->set('source_code_url', 'https://github.com/example/project')
            ->set('visibility', 'public')
            ->call('saveProject')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolios', [
            'id' => $project->id,
            'user_id' => $learner->id,
            'title' => 'Published project',
            'visibility' => 'public',
            'is_public' => true,
        ]);

        Livewire::actingAs($learner)
            ->test(PortfolioBuilder::class)
            ->call('deleteProject', $project->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('portfolios', ['id' => $project->id]);
    }

    public function test_learner_can_reorder_owned_projects(): void
    {
        $learner = $this->learner();
        $first = $this->project($learner, ['title' => 'First', 'sort_order' => 1]);
        $second = $this->project($learner, ['title' => 'Second', 'sort_order' => 2]);
        $third = $this->project($learner, ['title' => 'Third', 'sort_order' => 3]);

        Livewire::actingAs($learner)
            ->test(PortfolioBuilder::class)
            ->call('moveProject', $third->id, 'up')
            ->assertHasNoErrors();

        $this->assertSame(
            [$first->id, $third->id, $second->id],
            Portfolio::query()->where('user_id', $learner->id)->orderBy('sort_order')->pluck('id')->all()
        );
    }

    public function test_learner_cannot_mutate_another_learners_project_id(): void
    {
        $owner = $this->learner();
        $attacker = $this->learner();
        $project = $this->project($owner);

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->call('editProject', $project->id)
            ->assertForbidden();

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->call('moveProject', $project->id, 'up')
            ->assertForbidden();

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->call('deleteProject', $project->id)
            ->assertForbidden();

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->call('duplicateProject', $project->id)
            ->assertForbidden();

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->call('previewProject', $project->id)
            ->assertForbidden();

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->call('toggleBulkSelect', $project->id)
            ->assertForbidden();

        Livewire::actingAs($attacker)
            ->test(PortfolioBuilder::class)
            ->set('bulkSelected', [$project->id])
            ->call('bulkDelete')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('portfolios', [
            'id' => $project->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_project_links_accept_only_http_and_https_urls(): void
    {
        $learner = $this->learner();

        Livewire::actingAs($learner)
            ->test(PortfolioBuilder::class)
            ->set('title', 'Unsafe links')
            ->set('description', 'This project should not be saved with unsafe links.')
            ->set('technologies', 'Laravel')
            ->set('category', 'web-development')
            ->set('project_url', 'javascript:alert(1)')
            ->set('source_code_url', 'ftp://example.com/source')
            ->call('saveProject')
            ->assertHasErrors(['project_url', 'source_code_url']);

        $this->assertDatabaseMissing('portfolios', [
            'user_id' => $learner->id,
            'title' => 'Unsafe links',
        ]);
    }

    public function test_public_showcase_exposes_only_explicitly_published_projects(): void
    {
        $owner = $this->learner([
            'name' => '<script>alert("owner")</script>',
            'profile_visibility' => 'public',
            'bio' => 'Learning by building.',
        ]);
        $otherLearner = $this->learner(['profile_visibility' => 'public']);
        $published = $this->project($owner, [
            'title' => '<script>alert("published")</script>',
            'description' => 'A deliberately published project.',
            'visibility' => 'public',
            'is_public' => true,
            'project_url' => 'https://example.com/demo',
        ]);
        $private = $this->project($owner, [
            'title' => 'Private capstone',
            'description' => 'Private project details must not appear.',
            'visibility' => 'private',
            'is_public' => true,
        ]);
        $otherPublished = $this->project($otherLearner, [
            'title' => 'Another learners project',
            'visibility' => 'public',
            'is_public' => true,
        ]);
        Role::findOrCreate(User::ROLE_INSTRUCTOR, 'web');
        $instructor = User::factory()->create([
            'role' => User::ROLE_INSTRUCTOR,
            'profile_visibility' => 'public',
        ]);
        $instructorProject = $this->project($instructor, [
            'title' => 'Instructor project',
            'visibility' => 'public',
            'is_public' => true,
        ]);

        $response = $this->get(route('portfolio.public', $published->slug))->assertOk();
        $response->assertSee('&lt;script&gt;alert(&quot;published&quot;)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert("published")</script>', false);
        $response->assertSee('&lt;script&gt;alert(&quot;owner&quot;)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert("owner")</script>', false);
        $response->assertSee('A deliberately published project.');
        $response->assertDontSee($private->title);
        $response->assertDontSee($private->description);
        $response->assertDontSee($otherPublished->title);

        $this->get(route('portfolio.public', $private->slug))->assertNotFound();
        $this->get(route('portfolio.public', $instructorProject->slug))->assertNotFound();

        $owner->update(['profile_visibility' => 'private']);
        $this->get(route('portfolio.public', $published->slug))->assertNotFound();
    }

    public function test_portfolio_manager_is_limited_to_verified_learners(): void
    {
        $unverifiedLearner = $this->learner(['email_verified_at' => null]);
        Role::findOrCreate(User::ROLE_INSTRUCTOR, 'web');
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);

        $this->actingAs($unverifiedLearner)
            ->get(route('portfolio.show'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($instructor)
            ->get(route('portfolio.show'))
            ->assertForbidden();

        Livewire::actingAs($instructor)
            ->test(PortfolioBuilder::class)
            ->assertForbidden();
    }

    private function learner(array $attributes = []): User
    {
        Role::findOrCreate(User::ROLE_STUDENT, 'web');

        return User::factory()->create(array_merge([
            'role' => User::ROLE_STUDENT,
            'profile_visibility' => 'public',
        ], $attributes));
    }

    private function project(User $owner, array $attributes = []): Portfolio
    {
        $title = $attributes['title'] ?? 'Portfolio project';

        return $owner->portfolios()->create(array_merge([
            'title' => $title,
            'slug' => Str::slug(strip_tags($title)).'-'.Str::lower(Str::random(8)),
            'description' => 'A project description with enough detail.',
            'category' => 'web-development',
            'status' => 'completed',
            'technologies' => 'Laravel, Livewire',
            'visibility' => 'private',
            'is_public' => false,
            'sort_order' => 1,
        ], $attributes));
    }
}
