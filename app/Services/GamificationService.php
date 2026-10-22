<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;
use App\Models\Credentials\GamificationData;
use App\Models\Assessment\LearningSession;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    const POINTS_LESSON_COMPLETION = 25;
    const POINTS_QUIZ_COMPLETION = 50;
    const POINTS_COURSE_COMPLETION = 200;
    const POINTS_DAILY_LOGIN = 10;
    const POINTS_STREAK_BONUS = 5;
    const POINTS_PERFECT_QUIZ = 100;

    const COINS_LESSON_COMPLETION = 5;
    const COINS_QUIZ_COMPLETION = 10;
    const COINS_COURSE_COMPLETION = 50;
    const COINS_DAILY_LOGIN = 2;

    const GEMS_COURSE_COMPLETION = 3;
    const GEMS_PERFECT_QUIZ = 1;
    const GEMS_ACHIEVEMENT = 2;

    const ENERGY_COST_LESSON = 10;
    const ENERGY_COST_QUIZ = 15;
    const ENERGY_REGEN_RATE = 1; // per 5 minutes

    public function handleLessonCompletion(User $user, ?Lesson $lesson = null)
    {
        if (!$lesson) return ['success' => false, 'message' => 'A completed lesson is required.'];
        return DB::transaction(function () use ($user, $lesson) {
            $data = $user->getOrCreateGamificationData();
            $courseId = $lesson->section?->course_id;
            $enrolled = $user->enrollments()->where('course_id', $courseId)->exists() || $user->courses()->where('courses.id', $courseId)->exists();
            $completed = $user->completedLessons()->where('lessons.id', $lesson->id)->wherePivotNotNull('completed_at')->exists()
                || DB::table('user_progress')->where('user_id', $user->id)->where('course_id', $courseId)->where('lesson_id', $lesson->id)->where('is_completed', true)->whereNotNull('completed_at')->exists();
            if (!$lesson->assessments()->get()->every(fn ($assessment) => $assessment->isPassedByStudent($user->id))) return ['success' => false, 'message' => 'A passing reviewed assessment result is required.'];
            if (!$enrolled || !$completed) return ['success' => false, 'message' => 'Verified enrollment and completion are required.'];
            if ($this->rewardRecorded($user, 'lesson_completion', 'lesson_id', $lesson->id)) return ['success' => true, 'already_awarded' => true];
            $data->updateStreak();
            $data->addPoints(self::POINTS_LESSON_COMPLETION, 'lesson_completion');
            $this->identifyReward($user, 'lesson_completion', ['lesson_id' => $lesson->id]);
            $data->addCoins(self::COINS_LESSON_COMPLETION, 'lesson_completion');
            $data->addExperience(50, 'lesson_completion');
            $this->resetQuests($data);
            $quests = $data->updateQuestProgress('complete_lesson');
            return ['success' => true, 'points_earned' => self::POINTS_LESSON_COMPLETION, 'coins_earned' => self::COINS_LESSON_COMPLETION, 'quests_completed' => $quests];
        });
    }

    public function handleQuizCompletion(User $user, ?Assessment $assessment = null, $score = 0, $passed = false)
    {
        if (!$assessment) return ['success' => false, 'message' => 'A graded assessment is required.'];
        return DB::transaction(function () use ($user, $assessment) {
            $data = $user->getOrCreateGamificationData();
            if (!$user->enrollments()->where('course_id', $assessment->course_id)->exists() && !$user->courses()->where('courses.id', $assessment->course_id)->exists()) return ['success' => false, 'message' => 'Enrollment is required.'];
            $recorded = LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->where('metadata->assessment_id', $assessment->id)->whereNotNull('ended_at')->latest('id')->first();
            if ($recorded && isset($recorded->metadata['results'])) {
                $result = $recorded->metadata['results'];
                if (!$result['passed']) return ['success' => false, 'message' => 'A recorded passing result is required.'];
                $earned = (float) $result['total_points'];
                $possible = (float) $result['max_points'];
            } else {
                $questions = $assessment->questions;
                if ($questions->isEmpty()) return ['success' => false, 'message' => 'No graded questions are available.'];
                $answers = $assessment->studentAnswers()->where('user_id', $user->id)->whereNotNull('submitted_at')->get();
                $latest = $answers->where('attempt_number', $answers->max('attempt_number'))->keyBy('question_id');
                $earned = $possible = 0;
                foreach ($questions as $question) {
                    $answer = $latest->get($question->id);
                    if (!$answer || (!in_array($question->question_type, ['multiple_choice', 'true_false'], true) && !$answer->graded_at)) return ['success' => false, 'message' => 'All questions must be submitted and graded.'];
                    $possible += max(0, (float) $question->points);
                    $earned += min(max(0, (float) $answer->points_earned), max(0, (float) $question->points));
                }
                if (!$assessment->isPassedByStudent($user->id) || $possible <= 0 || $earned / $possible * 100 < (float) $assessment->pass_percentage) return ['success' => false, 'message' => 'A recorded passing result is required.'];
            }
            if ($this->rewardRecorded($user, 'quiz_completion', 'assessment_id', $assessment->id)) return ['success' => true, 'already_awarded' => true];
            $data->addPoints(self::POINTS_QUIZ_COMPLETION, 'quiz_completion');
            $this->identifyReward($user, 'quiz_completion', ['assessment_id' => $assessment->id]);
            $data->addCoins(self::COINS_QUIZ_COMPLETION, 'quiz_completion');
            $data->addExperience(75, 'quiz_completion');
            $data->updateStreak();
            $this->resetQuests($data);
            $data->updateQuestProgress('pass_quiz');
            if ($earned >= $possible) $data->updateQuestProgress('perfect_score');
            return ['success' => true, 'points_earned' => self::POINTS_QUIZ_COMPLETION, 'coins_earned' => self::COINS_QUIZ_COMPLETION];
        });
    }

    private function rewardRecorded(User $user, string $source, string $key, int $id): bool
    {
        return $user->gamificationTransactions()->where('source', $source)->where('currency_type', 'points')->where('metadata->'.$key, $id)->exists();
    }

    private function identifyReward(User $user, string $source, array $metadata): void
    {
        $user->gamificationTransactions()->where('source', $source)->where('currency_type', 'points')->latest('id')->firstOrFail()->update(['metadata' => $metadata]);
    }

    private function resetQuests(GamificationData $data): void
    {
        if (!$data->last_quest_reset || !$data->last_quest_reset->isToday()) {
            $data->generateDailyQuests();
            $data->update(['last_quest_reset' => now()->startOfDay()]);
        }
    }

    public function handleDailyLogin(User $user)
    {
        return DB::transaction(function () use ($user) {
            $data = $user->getOrCreateGamificationData();
            if ($user->gamificationTransactions()->where('source', 'daily_login')->where('currency_type', 'points')->whereDate('created_at', today())->exists()) return ['success' => false, 'first_login_today' => false, 'message' => 'Your daily reward has already been claimed.'];
            $data->addPoints(self::POINTS_DAILY_LOGIN, 'daily_login');
            $data->addCoins(self::COINS_DAILY_LOGIN, 'daily_login');
            $data->addExperience(10, 'daily_login');
            $this->resetQuests($data);
            $data->updateEnergy();
            return ['success' => true, 'first_login_today' => true, 'points_earned' => self::POINTS_DAILY_LOGIN, 'coins_earned' => self::COINS_DAILY_LOGIN, 'xp_earned' => 10, 'message' => 'Daily reward recorded: 10 points, 2 coins and 10 XP.'];
        });
    }

    public function handleGameCompletion(User $user, $gameId, $score)
    {
        return ['success' => false, 'message' => 'Game scoring is unavailable until a server-verified game is published.'];
    }

    public function getAvailableGames(): array
    {
        return []; // No playable game implementation exists; preserve historical scores without inventing new results.
    }

    public function getUserDashboardStats(User $user): array
    {
        $data = $user->getOrCreateGamificationData();
        $data->updateEnergy();
        return array_merge($user->getGamificationSummary(), ['experience_points' => $data->experience_points, 'experience_to_next_level' => $data->experience_to_next_level]);
    }

    public function getLeaderboards(): array
    {
        return ['overall' => GamificationData::query()->whereIn('id', GamificationData::selectRaw('MIN(id)')->groupBy('user_id'))
            ->whereHas('user', fn ($query) => $query->where('is_active', true)->where('profile_visibility', 'public')->where('metadata->public_leaderboard', true))
            ->orderByDesc('total_points')->orderBy('user_id')->limit(10)->with('user:id,name')->get()
            ->map(fn ($data, $index) => ['rank' => $index + 1, 'name' => $data->user->name, 'points' => $data->total_points, 'level' => $data->level])->all()];
    }

    public function purchaseItem(User $user, $itemId): array
    {
        return DB::transaction(function () use ($user, $itemId) {
            $data = $user->getOrCreateGamificationData();
            $item = \App\Models\Marketplace\RewardStoreItem::lockForUpdate()->findOrFail($itemId);
            if (!$item->is_available || ($item->available_until && $item->available_until->isPast())) return ['success' => false, 'message' => 'This reward is unavailable.'];
            if ($item->cost_coins < 0 || $item->cost_gems < 0 || $item->required_level < 1) return ['success' => false, 'message' => 'The reward configuration needs staff review.'];
            if ($item->requirements || !in_array($item->item_type, ['energy', 'energy_refill'], true)) return ['success' => false, 'message' => 'Delivery for this reward is not configured. No currency was spent.'];
            $amount = $item->item_data['energy_amount'] ?? null;
            if (!is_int($amount) || $amount < 1 || $amount > 100) return ['success' => false, 'message' => 'The energy reward needs staff review.'];
            if ($user->storePurchases()->where('reward_store_item_id', $item->id)->exists()) return ['success' => true, 'message' => 'This reward has already been redeemed.'];
            $data->updateEnergy();
            if ($data->energy >= 100 || $data->level < $item->required_level || $data->coins < $item->cost_coins || $data->gems < $item->cost_gems) return ['success' => false, 'message' => 'Check your level, balances and available energy capacity.'];
            if ($item->cost_coins) $data->spendCoins($item->cost_coins, 'reward_store');
            if ($item->cost_gems) $data->spendGems($item->cost_gems, 'reward_store');
            $user->storePurchases()->create(['reward_store_item_id' => $item->id]);
            $data->update(['energy' => min(100, $data->energy + $amount)]);
            return ['success' => true, 'message' => 'Reward redeemed. Your energy was updated.'];
        });
    }

    public function getAnalyticsData(User $user, $timeframe = '7d')
    {
        $range = ['24h' => 'day', '7d' => 'week', '30d' => 'month', '90d' => 'quarter', '1y' => 'year'][$timeframe] ?? null;
        if (!$range) throw new \InvalidArgumentException('Choose a supported analytics period.');
        return app(LearningAnalyticsService::class)->snapshot($user, $range);
    }

    public function getLeaderboardData($limit = 10)
    {
        return collect($this->getLeaderboards()['overall'])->take(max(1, min(10, (int) $limit)));
    }

    public function exportAnalytics(User $user, $format = 'json')
    {
        $data = $this->getAnalyticsData($user, '30d');
        if ($format === 'json') return $data;
        if ($format !== 'csv') throw new \InvalidArgumentException('Only JSON and CSV exports are available.');
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, ['Date', 'Completed lessons', 'Recorded session minutes']);
        foreach ($data['activity'] as $entry) fputcsv($stream, array_values($entry));
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);
        return $csv;
    }

    public function awardSpecialEvent(User $user, $eventType, $multiplier = 1)
    {
        return ['success' => false, 'message' => 'No verified special-event reward rule is configured.'];
    }

    public function broadcastAchievement(User $user, $achievement)
    {
        return false; // No achievement broadcast delivery integration is configured.
    }

    public function calculateDifficultyAdjustment(User $user)
    {
        $score = $this->getAnalyticsData($user)['averageScore'];
        if ($score === null) return 'maintain';
        return $score >= 90 ? 'increase' : ($score <= 60 ? 'decrease' : 'maintain');
    }

    public function generateLearningPath(User $user)
    {
        return ['available' => false, 'message' => 'A reviewed learning path has not been published.'];
    }
}
