<?php

namespace Tests\Feature;

use App\Livewire\Gamification\GamificationDashboard;
use App\Livewire\StudentManagement\LearningAnalytics;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Section;
use App\Models\Learning\Lesson;
use App\Models\Marketplace\RewardStoreItem;
use App\Services\GamificationService;
use App\Services\LearningAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class GamificationAnalyticsIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $user = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => User::factory()->create()->id, 'title' => 'Analytics owned course', 'slug' => 'analytics-owned-'.uniqid()]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'Section', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Recorded lesson', 'slug' => 'recorded-'.uniqid()]));
        $user->courses()->attach($course->id, ['progress' => 40]);
        $user->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        return [$user, $course, $lesson];
    }

    public function test_daily_reward_is_persisted_once_across_stale_models_and_livewire_requests(): void
    {
        $user = User::factory()->create();
        $stale = $user->fresh();
        $service = app(GamificationService::class);
        $this->assertTrue($service->handleDailyLogin($user)['success']);
        $this->assertFalse($service->handleDailyLogin($stale)['success']);
        Livewire::actingAs($user)->test(GamificationDashboard::class)->call('claimDailyReward')->assertSee('already been claimed');
        $this->assertSame(10, $user->getOrCreateGamificationData()->total_points);
        $this->assertSame(2, $user->getOrCreateGamificationData()->coins);
        $this->assertSame(1, DB::table('gamification_data')->where('user_id', $user->id)->count());
        $this->assertSame(1, DB::table('gamification_transactions')->where('user_id', $user->id)->where('source', 'daily_login')->where('currency_type', 'points')->count());
    }

    public function test_random_or_forged_game_scores_never_create_rewards(): void
    {
        $user = User::factory()->create();
        $this->assertFalse(app(GamificationService::class)->handleGameCompletion($user, 'invented-game', 999999)['success']);
        Livewire::actingAs($user)->test(GamificationDashboard::class)->call('playGame', 'invented-game')->assertSee('server-verified game');
        $this->assertDatabaseCount('gamification_transactions', 0);
        $this->actingAs($user)->get(route('gamification.games.play', 'invented-game'))->assertNotFound();
    }

    public function test_currency_operations_reject_negative_values_and_use_fresh_balances(): void
    {
        $data = User::factory()->create()->getOrCreateGamificationData();
        $data->addCoins(10);
        $stale = $data->fresh();
        $this->assertTrue($data->spendCoins(8));
        $this->assertFalse($stale->spendCoins(8));
        $this->assertSame(2, $data->fresh()->coins);
        try { $data->spendCoins(-20); $this->fail('Negative spend accepted'); }
        catch (\InvalidArgumentException) { $this->assertSame(2, $data->fresh()->coins); }
    }

    public function test_quest_completion_persists_before_reward_and_replays_once(): void
    {
        $data = User::factory()->create()->getOrCreateGamificationData();
        $data->update(['daily_quests' => [['id' => 'complete_lesson', 'target' => 1, 'progress' => 0, 'reward_coins' => 7, 'reward_xp' => 10]]]);
        $this->assertTrue($data->updateQuestProgress('complete_lesson')['quest_completed']);
        $this->assertFalse($data->fresh()->updateQuestProgress('complete_lesson')['quest_completed']);
        $this->assertTrue($data->fresh()->daily_quests[0]['completed']);
        $this->assertSame(7, $data->fresh()->coins);
    }

    public function test_streak_advances_on_consecutive_days_without_replayed_gem_rewards(): void
    {
        $data = User::factory()->create()->getOrCreateGamificationData();
        $data->update(['last_activity_date' => today()->subDay(), 'current_streak' => 6, 'longest_streak' => 6]);
        $data->updateStreak(); $data->fresh()->updateStreak();
        $this->assertSame(7, $data->fresh()->current_streak);
        $this->assertSame(2, $data->fresh()->gems);
    }

    public function test_verified_lesson_rewards_require_enrollment_and_completion_and_are_replay_safe(): void
    {
        [$user, , $lesson] = $this->fixture();
        $service = app(GamificationService::class);
        $this->assertFalse($service->handleLessonCompletion($user, $lesson)['success']);
        $user->completedLessons()->attach($lesson->id, ['completed_at' => now()]);
        $this->assertTrue($service->handleLessonCompletion($user, $lesson)['success']);
        $this->assertTrue($service->handleLessonCompletion($user->fresh(), $lesson)['already_awarded']);
        $this->assertSame(25, $user->getOrCreateGamificationData()->total_points);
        $this->assertSame(5, $user->getOrCreateGamificationData()->coins);
        $this->assertDatabaseCount('learning_sessions', 0);
    }

    public function test_store_redemption_is_atomic_replay_safe_and_cannot_spend_for_unavailable_delivery(): void
    {
        $user = User::factory()->create();
        $data = $user->getOrCreateGamificationData();
        $data->update(['coins' => 30, 'gems' => 2, 'energy' => 20]);
        $item = RewardStoreItem::create(['item_type' => 'energy', 'item_key' => 'test-energy', 'name' => 'Test energy', 'description' => 'Test reward', 'icon' => 'bolt', 'cost_coins' => 10, 'cost_gems' => 1, 'required_level' => 1, 'item_data' => ['energy_amount' => 25]]);
        $service = app(GamificationService::class);
        $this->assertTrue($service->purchaseItem($user, $item->id)['success']);
        $this->assertTrue($service->purchaseItem($user->fresh(), $item->id)['success']);
        $this->assertSame(20, $data->fresh()->coins);
        $this->assertSame(1, $data->fresh()->gems);
        $this->assertSame(45, $data->fresh()->energy);
        $this->assertDatabaseCount('user_store_purchases', 1);
        $item->update(['item_type' => 'unconfigured', 'cost_coins' => -100]);
        $this->assertFalse($service->purchaseItem($user, $item->id)['success']);
        $this->assertSame(20, $data->fresh()->coins);
    }

    public function test_leaderboard_only_projects_consented_public_names_without_private_account_fields(): void
    {
        $private = User::factory()->create(['name' => 'PRIVATE NAME', 'metadata' => ['public_leaderboard' => false]]);
        $private->getOrCreateGamificationData()->addPoints(1000);
        $public = User::factory()->create(['name' => 'Public learner', 'metadata' => ['public_leaderboard' => true]]);
        $public->getOrCreateGamificationData()->addPoints(25);
        $result = app(GamificationService::class)->getLeaderboards();
        $this->assertSame(['rank', 'name', 'points', 'level'], array_keys($result['overall'][0]));
        $this->assertSame('Public learner', $result['overall'][0]['name']);
        Livewire::actingAs($public)->test(GamificationDashboard::class, ['activeTab' => 'leaderboard'])->assertDontSee('PRIVATE NAME')->assertDontSee($private->email);
    }

    public function test_analytics_deduplicate_progress_preserve_scope_and_do_not_estimate_time(): void
    {
        [$user, $course, $lesson] = $this->fixture();
        $user->completedLessons()->attach($lesson->id, ['completed_at' => now(), 'time_spent_minutes' => 7]);
        $user->enrollments()->create(['course_id' => $course->id, 'enrolled_at' => now(), 'progress_percentage' => 40]);
        DB::table('user_progress')->insert(['user_id' => $user->id, 'course_id' => $course->id, 'lesson_id' => $lesson->id, 'is_completed' => true, 'completed_at' => now()]);
        $result = app(LearningAnalyticsService::class)->snapshot($user);
        $this->assertSame(1, $result['totalCourses']); $this->assertSame(1, $result['recentLessons']);
        $this->assertSame(0, $result['sessionMinutes']); $this->assertSame(7, $result['recordedLessonMinutes']);
        $this->assertNull($result['averageScore']);
        $this->assertSame(0, app(LearningAnalyticsService::class)->snapshot(User::factory()->create())['totalCourses']);
        Livewire::actingAs(User::factory()->create())->test(\App\Livewire\Dashboard\LearningAnalyticsDashboard::class, ['userId' => $user->id])->assertForbidden();
    }

    public function test_goals_are_persisted_for_the_signed_in_user_with_validation_and_real_export(): void
    {
        [$user] = $this->fixture();
        Livewire::actingAs($user)->test(LearningAnalytics::class)->set('studyGoal', 45)->set('weeklyGoal', 8)->call('saveGoals')->assertHasNoErrors()->assertSee('Your learning goals were saved.')
            ->call('exportReport')->assertFileDownloaded('my-learning-'.now()->format('Y-m-d').'.csv');
        $this->assertDatabaseHas('learning_goals', ['user_id' => $user->id, 'goal_type' => 'study_minutes', 'target_value' => 45]);
        Livewire::actingAs($user)->test(LearningAnalytics::class)->set('studyGoal', -1)->call('saveGoals')->assertHasErrors('studyGoal');
        $this->assertDatabaseCount('learning_goals', 2);
    }

    public function test_all_named_reward_pages_mount_the_real_component(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['dashboard', 'badges', 'leaderboard', 'store', 'games'] as $page) $this->get(route('gamification.'.$page))->assertOk()->assertSee('Learning rewards');
        $this->get(route('learning.analytics'))->assertOk();
        $this->get(route('student.learning-analytics'))->assertOk();
    }

    public function test_leaderboard_withdrawal_and_private_profile_take_effect_without_a_shared_cache(): void
    {
        $user = User::factory()->create(['metadata' => ['public_leaderboard' => true], 'profile_visibility' => 'public']);
        $user->getOrCreateGamificationData()->addPoints(25);
        $this->assertCount(1, app(GamificationService::class)->getLeaderboards()['overall']);
        Livewire::actingAs($user)->test(GamificationDashboard::class, ['activeTab' => 'leaderboard'])->set('publicLeaderboard', false)->call('saveLeaderboardPreference')->assertHasNoErrors();
        $this->assertCount(0, app(GamificationService::class)->getLeaderboards()['overall']);
        $user->update(['profile_visibility' => 'private', 'metadata' => ['public_leaderboard' => true]]);
        $this->assertCount(0, app(GamificationService::class)->getLeaderboards()['overall']);
    }

    public function test_reward_redemption_failure_rolls_back_both_currencies_and_inventory(): void
    {
        $user = User::factory()->create(); $data = $user->getOrCreateGamificationData();
        $data->update(['coins' => 30, 'gems' => 2, 'energy' => 20]);
        $item = RewardStoreItem::create(['item_type' => 'energy', 'item_key' => 'rollback-energy', 'name' => 'Rollback energy', 'description' => 'Test', 'icon' => 'bolt', 'cost_coins' => 10, 'cost_gems' => 1, 'required_level' => 1, 'item_data' => ['energy_amount' => 25]]);
        \App\Models\Marketplace\UserStorePurchase::created(fn () => throw new \RuntimeException('Forced inventory failure'));
        try { app(GamificationService::class)->purchaseItem($user, $item->id); $this->fail('Expected rollback'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced inventory failure', $exception->getMessage()); }
        finally { \App\Models\Marketplace\UserStorePurchase::flushEventListeners(); }
        $this->assertSame(30, $data->fresh()->coins); $this->assertSame(2, $data->fresh()->gems);
        $this->assertDatabaseCount('user_store_purchases', 0); $this->assertDatabaseCount('gamification_transactions', 0);
    }

    public function test_legacy_service_contracts_use_recorded_analytics_and_do_not_fabricate_delivery(): void
    {
        [$user] = $this->fixture(); $service = app(GamificationService::class);
        $this->assertSame(1, $service->exportAnalytics($user)['totalCourses']);
        $this->assertStringStartsWith('Date,"Completed lessons","Recorded session minutes"', $service->exportAnalytics($user, 'csv'));
        $this->assertFalse($service->awardSpecialEvent($user, 'invented-event', 9999)['success']);
        $this->assertFalse($service->generateLearningPath($user)['available']);
        $this->assertFalse($service->broadcastAchievement($user, []));
        $this->assertDatabaseCount('gamification_transactions', 0);
    }
}
