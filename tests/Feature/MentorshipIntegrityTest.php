<?php

namespace Tests\Feature;

use App\Livewire\Mentorship\MentorshipActions;
use App\Livewire\Mentorship\MentorshipHub;
use App\Models\Core\User;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use App\Models\Marketplace\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MentorshipIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $mentor = User::factory()->create();
        $mentee = User::factory()->create();
        $mentorship = Mentorship::create(['mentor_id' => $mentor->id, 'mentee_id' => $mentee->id,
            'status' => 'active', 'is_paid' => true, 'hourly_rate' => 100]);
        $session = MentorshipSession::create(['mentorship_id' => $mentorship->id, 'title' => 'PRIVATE fixture session',
            'description' => 'Private session content for regression tests.', 'scheduled_at' => now()->addDay(),
            'status' => 'scheduled', 'duration_minutes' => 60, 'is_billable' => true, 'session_cost' => 100]);
        return [$mentor, $mentee, $mentorship, $session];
    }

    public function test_non_student_role_does_not_inherit_other_peoples_mentorship_data(): void
    {
        $this->fixture();
        Role::findOrCreate('content_editor', 'web');
        $user = User::factory()->create(['role' => 'content_editor']);
        Livewire::actingAs($user)->test(MentorshipHub::class)->assertSet('activeMentorships', 0)
            ->assertDontSee('PRIVATE fixture session');
    }

    public function test_legacy_actions_cannot_view_or_create_against_an_unrelated_mentorship(): void
    {
        [, , $mentorship, $session] = $this->fixture();
        $user = User::factory()->create();
        foreach (['viewSession' => $session->id, 'createSession' => $mentorship->id, 'createCodeReview' => $mentorship->id, 'createReview' => $mentorship->id] as $method => $id) {
            try {
                Livewire::actingAs($user)->test(MentorshipActions::class)->call($method, $id);
                $this->fail('Unrelated mentorship access was accepted');
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
                $this->assertSame(Mentorship::class, $exception->getModel());
            }
        }
        $this->assertDatabaseCount('mentorship_sessions', 1);
    }

    public function test_paid_completion_transfers_existing_funds_and_stale_replay_is_safe(): void
    {
        [$mentor, $mentee, , $session] = $this->fixture();
        $wallet = Wallet::getOrCreateWallet($mentee->id);
        $wallet->credit(1000, 'funding', 'Fixture funding');
        $stale = $session->fresh();
        $session->completeWithOperations();
        $stale->completeWithOperations();
        $this->assertEquals(900, $wallet->fresh()->balance);
        $this->assertEquals(100, Wallet::getOrCreateWallet($mentor->id, 'instructor')->fresh()->balance);
        $this->assertSame('paid', $session->fresh()->payment_status);
        $this->assertDatabaseCount('wallet_transactions', 3);
    }

    public function test_unfunded_completion_rolls_back_session_and_never_mints_mentor_balance(): void
    {
        [$mentor, , , $session] = $this->fixture();
        try {
            $session->completeWithOperations();
            $this->fail('Unfunded completion minted a payout');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('Insufficient', $exception->getMessage());
        }
        $this->assertSame('scheduled', $session->fresh()->status);
        $this->assertEquals(0, Wallet::getOrCreateWallet($mentor->id, 'instructor')->fresh()->balance);
        $this->assertDatabaseCount('wallet_transactions', 0);
    }

    public function test_a_real_mentor_profile_serializes_its_rating_without_missing_or_fabricated_attributes(): void
    {
        $profile = \App\Models\Mentorship\MentorProfile::create(['user_id' => User::factory()->create()->id, 'rating' => 4.5]);
        $data = $profile->toArray();
        $this->assertSame('★★★★☆', $data['rating_stars']);
        $this->assertArrayNotHasKey('success_rate', $data);
        $this->assertArrayNotHasKey('response_time', $data);
    }

    public function test_a_mentee_cannot_invoke_the_mentor_completion_action(): void
    {
        [, $mentee, , $session] = $this->fixture();
        Livewire::actingAs($mentee)->test(MentorshipActions::class)->call('viewSession', $session->id)
            ->set('sessionNotes', str_repeat('Detailed notes ', 3))->call('submitSessionCompletion')->assertForbidden();
        $this->assertSame('scheduled', $session->fresh()->status);
    }
    public function test_legacy_missing_dates_render_without_fabricating_dates(): void
    {
        [, $mentee] = $this->fixture();
        Livewire::actingAs($mentee)->test(MentorshipHub::class)->assertSee('Start date not recorded');
        Livewire::test(\App\Livewire\Mentorship\MyMentorships::class)->assertSee('Request date not recorded');
    }

    public function test_learner_cannot_mount_mentor_only_components(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['MentorDashboard', 'MentorProfile', 'MentorResources', 'Partial\\Profile', 'Partial\\Overview', 'Partial\\Analytics', 'Partial\\Mentorships'] as $component) {
            Livewire::test('App\\Livewire\\Mentorship\\'.$component)->assertForbidden();
        }
    }

    public function test_skill_search_cannot_escape_mentor_verification(): void
    {
        $user = User::factory()->create(['name' => 'PRIVATE unverified mentor']);
        \App\Models\Mentorship\MentorProfile::create(['user_id' => $user->id, 'is_verified' => false, 'is_available' => true, 'skills' => ['private-skill']]);
        Livewire::actingAs(User::factory()->create())->test(\App\Livewire\Mentorship\FindMentor::class)
            ->set('searchTerm', 'private-skill')->assertDontSee('PRIVATE unverified mentor');
    }

    public function test_existing_mentor_can_render_the_real_profile_editor(): void
    {
        Role::findOrCreate('mentor', 'web');
        $mentor = User::factory()->create(['role' => 'mentor']);
        \App\Models\Mentorship\MentorProfile::create(['user_id' => $mentor->id, 'bio' => 'Profile fixture', 'rating' => 4.5]);
        Livewire::actingAs($mentor)->test(\App\Livewire\Mentorship\Partial\Profile::class)
            ->call('editProfile')->assertSet('showProfileModal', true)->assertSee('Save Profile')
            ->call('closeModal')->assertSet('showProfileModal', false);
    }

    public function test_paid_completion_feedback_survives_the_session_refresh_event(): void
    {
        [$mentor, $mentee, , $session] = $this->fixture();
        Wallet::getOrCreateWallet($mentee->id)->credit(1000, 'funding', 'Test funding');
        Livewire::actingAs($mentor)->test(\App\Livewire\Mentorship\Sessions::class)
            ->call('completeSession', $session->id)->set('sessionNotes', 'Detailed feedback for the completed test session.')
            ->call('submitCompletion')->assertSee('Session completed and mentor payout was credited.')
            ->call('refreshSessions')->assertSee('Session completed and mentor payout was credited.');
        $this->assertSame('paid', $session->fresh()->payment_status);
    }

}
