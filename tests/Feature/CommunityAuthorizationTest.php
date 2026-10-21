<?php

namespace Tests\Feature;

use App\Livewire\Community\CommunityCenter;
use App\Models\Community\ActivityParticipant;
use App\Models\Community\CommunityActivity;
use App\Models\Community\CommunityFeedback;
use App\Models\Community\ForumThread;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommunityAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $role = 'academy_admin'): User
    {
        Role::findOrCreate($role, 'web');
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_learner_cannot_call_any_moderation_action_or_data_method(): void
    {
        $learner = User::factory()->create();
        $target = User::factory()->create();
        $thread = ForumThread::create(['user_id' => $target->id, 'category' => 'general', 'title' => 'Protected thread', 'content' => 'Protected discussion']);
        $feedback = CommunityFeedback::create(['user_id' => $target->id, 'subject' => 'Private feedback', 'message' => 'Private learner message']);
        foreach (['reviewReport' => [999], 'dismissReport' => [999], 'assignFeedback' => [$feedback->id],
            'resolveFeedback' => [$feedback->id], 'respondToFeedback' => [$feedback->id],
            'unflagThread' => [$thread->id], 'removeThread' => [$thread->id],
            'blockUser' => [$target->id], 'unblockUser' => [$target->id], 'setModerationTab' => ['users'],
            'getCommunityReports' => [], 'getFeedbackItems' => [], 'getFlaggedThreads' => [], 'getManagedUsers' => [],
        ] as $method => $arguments) {
            Livewire::actingAs($learner)->test(CommunityCenter::class)->call($method, ...$arguments)->assertForbidden();
        }
        $this->assertTrue($target->fresh()->is_active);
        $this->assertDatabaseHas('forum_threads', ['id' => $thread->id]);
        $this->assertSame('open', $feedback->fresh()->status);
    }

    public function test_forged_moderation_tab_cannot_read_private_data(): void
    {
        Livewire::actingAs(User::factory()->create())->test(CommunityCenter::class)
            ->set('activeTab', 'moderation')->assertForbidden();
    }

    public function test_named_community_routes_open_the_matching_tab_and_guard_moderation(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['study-groups', 'code-challenges', 'live-events', 'feedback'] as $tab) {
            $response = $this->get(route('community.'.$tab))->assertOk();
            preg_match_all('/wire:snapshot="([^"]+)"/', $response->getContent(), $matches);
            $snapshots = array_map(fn ($snapshot) => json_decode(html_entity_decode($snapshot, ENT_QUOTES), true), $matches[1]);
            $component = collect($snapshots)->first(fn ($snapshot) => ($snapshot['memo']['name'] ?? '') === 'community.community-center');
            $this->assertNotNull($component);
            $this->assertSame($tab, $component['data']['activeTab']);
        }
        $this->get(route('community.moderation'))->assertForbidden();
    }

    public function test_admin_can_assign_and_resolve_feedback_and_unflag_a_thread(): void
    {
        $admin = $this->admin();
        $learner = User::factory()->create();
        $feedback = CommunityFeedback::create(['user_id' => $learner->id, 'subject' => 'Support request', 'message' => 'Please review this issue']);
        $thread = ForumThread::create(['user_id' => $learner->id, 'category' => 'general', 'title' => 'Flagged thread', 'content' => 'Discussion content', 'is_flagged' => true]);
        Livewire::actingAs($admin)->test(CommunityCenter::class)
            ->call('assignFeedback', $feedback->id)->call('resolveFeedback', $feedback->id)
            ->call('unflagThread', $thread->id)->assertHasNoErrors();
        $feedback->refresh();
        $this->assertSame('resolved', $feedback->status);
        $this->assertSame($admin->id, $feedback->assigned_to);
        $this->assertFalse($thread->fresh()->is_flagged);
    }

    public function test_academy_admin_cannot_block_a_super_admin_or_their_own_account(): void
    {
        $admin = $this->admin();
        $super = $this->admin('super_admin');
        Livewire::actingAs($admin)->test(CommunityCenter::class)->call('blockUser', $super->id)->assertForbidden();
        Livewire::test(CommunityCenter::class)->call('blockUser', $admin->id)->assertStatus(422);
        $this->assertTrue($super->fresh()->is_active);
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_code_submission_is_pending_without_a_fabricated_score(): void
    {
        $user = User::factory()->create();
        $challenge = CommunityActivity::create(['creator_id' => $user->id, 'type' => 'code_challenge',
            'title' => 'Practice challenge', 'description' => 'Complete this practice challenge', 'status' => 'active']);
        Livewire::actingAs($user)->test(CommunityCenter::class)
            ->call('setTab', 'code-challenges')->assertSee('Not recorded')
            ->call('openCodeChallenge', $challenge->id)->assertSee('Submit solution')
            ->set('code', 'const answer = 42;')
            ->call('submitCodeSolution')->assertHasNoErrors();
        $submission = ActivityParticipant::sole();
        $this->assertSame('pending', $submission->status);
        $this->assertNull($submission->score);
        $this->assertNull($submission->completed_at);
        $submission->update(['status' => 'completed', 'score' => 70]);
        Livewire::test(CommunityCenter::class)->set('selectedItem', ['id' => $challenge->id])
            ->set('code', 'const answer = 99;')->call('submitCodeSolution');
        $this->assertEquals(70, $submission->fresh()->score);
    }
}
