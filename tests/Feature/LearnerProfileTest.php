<?php

namespace Tests\Feature;

use App\Livewire\StudentManagement\LearningProfile;
use App\Models\Ai\AiLearningProfile;
use App\Models\Core\User;
use App\Models\Learning\CourseCategory;
use App\Services\LearnerProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LearnerProfileTest extends TestCase
{
    use RefreshDatabase;

    private function learner(): User
    {
        Http::preventStrayRequests();
        Notification::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_guided_profile_saves_real_categories_and_optional_self_reported_knowledge(): void
    {
        $user = $this->learner();
        $category = CourseCategory::create(['name' => 'Business']);
        Livewire::test(LearningProfile::class)
            ->set('form.category_ids', [(string) $category->id])
            ->set('form.custom_topics', " Accounting \naccounting\nPublic speaking")
            ->call('next')->assertSet('step', 2)
            ->set('form.skills', " Spreadsheets \nSpreadsheets")
            ->set('form.completed_courses', 'Introductory accounting')
            ->set('form.experience', 'Helped with a family business')
            ->set('form.practical_experience', 'Prepared a monthly budget')
            ->set('form.starting_level', 'intermediate')
            ->call('next')->assertSet('step', 3)
            ->set('form.goal', 'Learn accounting for my business')
            ->set('form.pace', 'steady')->set('form.minutes_per_week', '120')
            ->set('form.target_weeks', '12')->set('form.formats', ['reading', 'practice'])
            ->set('form.language', 'English')
            ->call('save')->assertHasNoErrors()->assertSet('saved', true)->assertSee('Your profile is saved');

        $profile = AiLearningProfile::where('user_id', $user->id)->sole();
        $this->assertSame([$category->id], $profile->interests['category_ids']);
        $this->assertSame(['Accounting', 'Public speaking'], $profile->interests['topics']);
        $this->assertSame(['Spreadsheets'], $profile->existing_knowledge['skills']);
        $this->assertSame(['Introductory accounting'], $profile->existing_knowledge['completed_courses']);
        $this->assertSame('Prepared a monthly budget', $profile->existing_knowledge['practical_experience']);
        $this->assertSame(120, $profile->learning_preferences['minutes_per_week']);
        $this->assertSame(12, $profile->learning_preferences['target_weeks']);
        $this->assertNotNull($profile->onboarding_completed_at);
        $this->assertNull($profile->skill_diagnosis);
        $this->assertNull($profile->signals);
        $this->assertDatabaseCount('course_enrollments', 0);
        $this->assertDatabaseCount('certificates', 0);
        $this->assertDatabaseCount('ai_usage_records', 0);
        Http::assertNothingSent();
    }

    public function test_only_a_goal_is_required_and_optional_values_are_stored_without_false_defaults(): void
    {
        $this->learner();
        $profile = app(LearnerProfileService::class)->save(['goal' => 'Learn a new language']);
        $this->assertNull($profile->starting_level);
        $this->assertSame([], $profile->existing_knowledge['skills']);
        $this->assertNull($profile->learning_preferences['minutes_per_week']);
        $this->assertNull($profile->learning_preferences['language']);
        $this->assertSame([], $profile->interests['category_ids']);
    }

    public function test_repeated_saves_update_one_owned_profile_and_preserve_all_recorded_evidence(): void
    {
        $user = $this->learner();
        $other = User::factory()->create();
        $evidence = ['skill_diagnosis' => [['label' => 'Historical']], 'signals' => ['recorded' => true],
            'assessment_feedback' => [['percentage' => 80]], 'adaptive_path' => [['title' => 'Existing path']],
            'course_recommendations' => [['course_id' => 123]]];
        $existing = AiLearningProfile::create(['user_id' => $user->id, 'goal' => 'Original', 'diagnosed_at' => now(), ...$evidence]);
        $otherProfile = AiLearningProfile::create(['user_id' => $other->id, 'goal' => 'Private other goal']);
        $input = ['goal' => 'Updated communication goal', 'user_id' => $other->id, 'id' => $otherProfile->id,
            'skill_diagnosis' => [['score' => 100]], 'signals' => ['forged' => true], 'onboarding_completed_at' => '2000-01-01'];
        $service = app(LearnerProfileService::class);
        $first = $service->save($input);
        $time = $first->onboarding_completed_at->toDateTimeString();
        $second = $service->save(['goal' => 'Updated again', 'custom_topics' => 'Design', 'skills' => 'Sketching']);
        $this->assertSame($existing->id, $second->id);
        $this->assertSame($time, $second->onboarding_completed_at->toDateTimeString());
        foreach ($evidence as $field => $value) {
            $this->assertSame($value, $second->$field);
        }
        $this->assertNull($second->diagnosed_at);
        $this->assertSame('Private other goal', $otherProfile->fresh()->goal);
        $this->assertSame(['Design'], $second->interests['topics']);
        $this->assertSame(['Sketching'], $second->existing_knowledge['skills']);
        $this->assertDatabaseCount('ai_learning_profiles', 2);
    }

    public function test_saved_choices_reload_and_search_retains_selections_and_escapes_private_text(): void
    {
        $this->learner();
        $business = CourseCategory::create(['name' => 'Business']);
        CourseCategory::create(['name' => 'Languages']);
        app(LearnerProfileService::class)->save(['goal' => '<script>privateGoal()</script>', 'category_ids' => [$business->id], 'custom_topics' => 'Design']);
        Livewire::test(LearningProfile::class)->assertSet('form.category_ids', [$business->id])
            ->assertSet('form.custom_topics', 'Design')->set('categorySearch', 'Languages')
            ->assertSee('Languages')->assertSet('form.category_ids', [$business->id])
            ->call('next')->call('next')->call('save')->assertSee('&lt;script&gt;privateGoal()&lt;/script&gt;', false)
            ->assertDontSee('<script>privateGoal()</script>', false);
    }

    public function test_final_save_revalidates_hidden_steps_and_does_not_persist_invalid_data(): void
    {
        $this->learner();
        Livewire::test(LearningProfile::class)->call('next')->call('next')->set('form.goal', 'A goal')
            ->set('form.category_ids', [999999])->call('save')->assertHasErrors(['form.category_ids.0'])
            ->assertSet('step', 1)->assertSet('saved', false);
        $this->assertDatabaseCount('ai_learning_profiles', 0);
    }

    public function test_validation_rejects_invalid_levels_formats_limits_and_missing_goals(): void
    {
        $this->learner();
        Livewire::test(LearningProfile::class)->set('form.custom_topics', str_repeat('x', 81))->call('next')->assertHasErrors('form.custom_topics')
            ->set('form.custom_topics', '')->call('next')->set('form.starting_level', 'verified_expert')->call('next')->assertHasErrors('form.starting_level')
            ->set('form.starting_level', '')->call('next')->set('form.goal', ' ')->set('form.formats', ['unknown'])
            ->set('form.minutes_per_week', 10081)->set('form.target_weeks', 0)->call('save')
            ->assertHasErrors(['form.goal', 'form.formats.0', 'form.minutes_per_week', 'form.target_weeks']);
        $this->assertDatabaseCount('ai_learning_profiles', 0);
    }

    public function test_skip_is_optional_idempotent_and_can_be_completed_later(): void
    {
        $user = $this->learner();
        Livewire::test(LearningProfile::class)->call('skip')->assertRedirect(route('student.dashboard'));
        $service = app(LearnerProfileService::class);
        $service->skip();
        $this->assertNotNull($service->profile()->onboarding_skipped_at);
        $this->get(route('dashboard'))->assertRedirect(route('student.dashboard'));
        $profile = $service->save(['goal' => 'Study mathematics']);
        $this->assertNull($profile->onboarding_skipped_at);
        $this->assertNotNull($profile->onboarding_completed_at);
        $service->skip();
        $this->assertNull($profile->fresh()->onboarding_skipped_at);
        $this->assertDatabaseCount('ai_learning_profiles', 1);
        $this->assertSame($user->id, $profile->user_id);
    }

    public function test_guests_unverified_learners_and_unrelated_roles_cannot_access_setup(): void
    {
        $this->get(route('learning.profile'))->assertRedirect(route('login'));
        $user = User::factory()->unverified()->create();
        $this->actingAs($user)->get(route('learning.profile'))->assertRedirect(route('verification.notice'));
        Role::findOrCreate('mentor', 'web');
        $user->forceFill(['email_verified_at' => now(), 'role' => 'mentor'])->save();
        $user->syncRoles(['mentor']);
        $this->get(route('learning.profile'))->assertForbidden();
        Livewire::test(LearningProfile::class)->assertForbidden();
    }

    public function test_fresh_account_status_and_role_are_checked_on_every_livewire_request(): void
    {
        $user = $this->learner();
        $component = Livewire::test(LearningProfile::class);
        User::whereKey($user->id)->update(['is_active' => false]);
        $component->call('next')->assertForbidden();
        $this->assertDatabaseCount('ai_learning_profiles', 0);
    }

    public function test_role_revocation_prevents_a_later_save(): void
    {
        $user = $this->learner();
        $component = Livewire::test(LearningProfile::class)->call('next')->call('next')->set('form.goal', 'A goal');
        $user->syncRoles([]);
        $component->call('save')->assertForbidden();
        $this->assertDatabaseCount('ai_learning_profiles', 0);
    }

    public function test_verified_first_visit_goes_to_setup_and_completed_profiles_go_to_dashboard(): void
    {
        $user = $this->learner();
        $this->get(route('dashboard'))->assertRedirect(route('learning.profile'));
        $this->get(route('learning.profile'))->assertOk()->assertSee('Your learning profile');
        app(LearnerProfileService::class)->save(['goal' => 'Professional development']);
        $this->get(route('dashboard'))->assertRedirect(route($user->getDashboardRouteName()));
        $this->get(route('student.dashboard'))->assertOk()->assertSee('Professional development')->assertSee('Update learning profile');
    }
}
