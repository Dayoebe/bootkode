<?php

namespace App\Models\Credentials;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Core\User;

class GamificationData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'level',
        'experience_points',
        'experience_to_next_level',
        'current_streak',
        'longest_streak',
        'last_activity_date',
        'coins',
        'gems',
        'energy',
        'energy_last_updated',
        'game_scores',
        'unlocked_features',
        'daily_quests',
        'weekly_quests',
        'last_quest_reset',
        'friends_count',
        'challenges_won',
        'challenges_participated'
    ];

    protected $casts = [
        'last_activity_date' => 'date',
        'energy_last_updated' => 'datetime',
        'last_quest_reset' => 'datetime',
        'game_scores' => 'array',
        'unlocked_features' => 'array',
        'daily_quests' => 'array',
        'weekly_quests' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(GamificationTransaction::class, 'user_id', 'user_id');
    }

    private function mutate(callable $callback)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($callback) {
            User::whereKey($this->user_id)->lockForUpdate()->firstOrFail();
            $fresh = static::whereKey($this->id)->lockForUpdate()->firstOrFail();
            $this->setRawAttributes($fresh->getAttributes(), true);
            return $callback();
        });
    }

    private function requirePositive(int $amount): void
    {
        if ($amount <= 0) throw new \InvalidArgumentException('Reward amounts must be positive.');
    }

    public function addExperience(int $xp, string $source = 'general')
    {
        $this->requirePositive($xp);
        return $this->mutate(function () use ($xp, $source) {
            $this->experience_points += $xp;
            $this->experience_to_next_level = max(1, $this->experience_to_next_level);
            $coins = $gems = 0;
            $leveledUp = false;
            while ($this->experience_points >= $this->experience_to_next_level) {
                $this->experience_points -= $this->experience_to_next_level;
                $this->level++;
                $this->experience_to_next_level = $this->calculateNextLevelXP();
                $coins += $this->level * 10;
                if ($this->level % 5 === 0) $gems += 5;
                $leveledUp = true;
            }
            $this->save();
            $this->logTransaction('earn', 'experience', $xp, $source, "Earned {$xp} XP from {$source}");
            if ($coins) $this->addCoins($coins, 'level_up');
            if ($gems) $this->addGems($gems, 'level_up');
            return ['leveled_up' => $leveledUp, 'new_level' => $this->level];
        });
    }

    private function earn(string $field, int $amount, string $source)
    {
        $this->requirePositive($amount);
        return $this->mutate(function () use ($field, $amount, $source) {
            $this->{$field} += $amount;
            $this->save();
            $this->logTransaction('earn', $field, $amount, $source, "Earned {$amount} {$field} from {$source}");
            return $this;
        });
    }

    public function addPoints(int $points, string $source = 'general')
    {
        return $this->earn('total_points', $points, $source);
    }

    public function addCoins(int $coins, string $source = 'general')
    {
        return $this->earn('coins', $coins, $source);
    }

    public function addGems(int $gems, string $source = 'general')
    {
        return $this->earn('gems', $gems, $source);
    }

    private function spend(string $field, int $amount, string $purpose): bool
    {
        $this->requirePositive($amount);
        return $this->mutate(function () use ($field, $amount, $purpose) {
            if ($this->{$field} < $amount) return false;
            $this->{$field} -= $amount;
            $this->save();
            $this->logTransaction('spend', $field, $amount, $purpose, "Spent {$amount} {$field} on {$purpose}");
            return true;
        });
    }

    public function spendCoins(int $amount, string $purpose = 'purchase')
    {
        return $this->spend('coins', $amount, $purpose);
    }

    public function spendGems(int $amount, string $purpose = 'purchase')
    {
        return $this->spend('gems', $amount, $purpose);
    }

    public function updateEnergy()
    {
        return $this->mutate(function () {
            if (!$this->energy_last_updated) $this->energy_last_updated = now();
            $units = max(0, (int) floor($this->energy_last_updated->diffInMinutes(now(), false) / 5));
            $this->energy = min(100, max(0, $this->energy) + $units);
            if ($this->energy >= 100) $this->energy_last_updated = now();
            elseif ($units > 0) $this->energy_last_updated = $this->energy_last_updated->addMinutes($units * 5);
            $this->save();
        });
    }

    public function useEnergy(int $amount = 1)
    {
        $this->requirePositive($amount);
        return $this->mutate(function () use ($amount) {
            $this->updateEnergy();
            if ($this->energy < $amount) return false;
            $this->energy -= $amount;
            $this->save();
            return true;
        });
    }

    public function updateStreak()
    {
        return $this->mutate(function () {
            $today = Carbon::today();
            if ($this->last_activity_date && $this->last_activity_date->gte($today)) return;
            $this->current_streak = $this->last_activity_date?->isYesterday() ? $this->current_streak + 1 : 1;
            $this->longest_streak = max($this->longest_streak, $this->current_streak);
            $this->last_activity_date = $today;
            $this->save();
            if ($this->current_streak % 7 === 0) $this->addGems(2, 'streak');
            if ($this->current_streak % 30 === 0) $this->addGems(10, 'streak');
        });
    }

    // Quest System
    public function generateDailyQuests()
    {
        $quests = [
            ['id' => 'complete_lesson', 'title' => 'Complete a Lesson', 'description' => 'Finish any lesson today', 'target' => 1, 'progress' => 0, 'reward_coins' => 25, 'reward_xp' => 50],
            ['id' => 'pass_quiz', 'title' => 'Pass a Quiz', 'description' => 'Score 80% or higher on any quiz', 'target' => 1, 'progress' => 0, 'reward_coins' => 30, 'reward_xp' => 75],
            ['id' => 'study_time', 'title' => 'Study for 30 Minutes', 'description' => 'Spend at least 30 minutes learning', 'target' => 30, 'progress' => 0, 'reward_coins' => 20, 'reward_xp' => 40],
            ['id' => 'perfect_score', 'title' => 'Perfect Score', 'description' => 'Get 100% on any assessment', 'target' => 1, 'progress' => 0, 'reward_coins' => 50, 'reward_xp' => 100],
            ['id' => 'social_engage', 'title' => 'Community Engagement', 'description' => 'Interact with course content or community', 'target' => 3, 'progress' => 0, 'reward_coins' => 15, 'reward_xp' => 30],
        ];

        // Randomly select 3 quests for the day
        $selectedQuests = collect($quests)->random(3)->values()->toArray();

        $this->daily_quests = $selectedQuests;
        $this->save();

        return $selectedQuests;
    }

    public function updateQuestProgress(string $questId, int $progress = 1)
    {
        $this->requirePositive($progress);
        return $this->mutate(function () use ($questId, $progress) {
            $quests = $this->daily_quests ?? [];
            foreach ($quests as &$quest) {
                if ($quest['id'] !== $questId || !empty($quest['completed'])) continue;
                $quest['progress'] = min($quest['target'], $quest['progress'] + $progress);
                if ($quest['progress'] < $quest['target']) continue;
                $quest['completed'] = true;
                $quest['completed_at'] = now()->toISOString();
                $completed = $quest;
            }
            unset($quest);
            $this->daily_quests = $quests;
            $this->save();
            if (isset($completed)) {
                if ($completed['reward_coins'] > 0) $this->addCoins($completed['reward_coins'], 'daily_quest');
                if ($completed['reward_xp'] > 0) $this->addExperience($completed['reward_xp'], 'daily_quest');
            }
            return ['quest_completed' => isset($completed), 'quest' => $completed ?? null];
        });
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->experience_to_next_level == 0)
            return 100;
        return round(($this->experience_points / $this->experience_to_next_level) * 100, 1);
    }

    private function calculateNextLevelXP()
    {
        return 100 + ($this->level * 50); // Exponential growth
    }

    private function logTransaction($type, $currency, $amount, $source, $description)
    {
        GamificationTransaction::create([
            'user_id' => $this->user_id,
            'transaction_type' => $type,
            'currency_type' => $currency === 'total_points' ? 'points' : $currency,
            'amount' => $amount,
            'source' => $source,
            'description' => $description,
        ]);
    }

    // Game score methods
    public function setGameScore(string $game, int $score)
    {
        $scores = $this->game_scores ?? [];
        $scores[$game] = max($scores[$game] ?? 0, $score);
        $this->game_scores = $scores;
        $this->save();
    }

    public function getGameScore(string $game)
    {
        return ($this->game_scores ?? [])[$game] ?? 0;
    }

    // Static methods for leaderboards
    public static function getTopPlayers($limit = 10)
    {
        return self::with('user')
            ->orderBy('total_points', 'desc')
            ->orderBy('level', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getTopByGame($game, $limit = 10)
    {
        return self::with('user')
            ->whereNotNull('game_scores')
            ->get()
            ->filter(function ($data) use ($game) {
                return isset($data->game_scores[$game]);
            })
            ->sortByDesc(function ($data) use ($game) {
                return $data->game_scores[$game];
            })
            ->take($limit)
            ->values();
    }
}