<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    // Level and Experience Methods
    public function addExperience(int $xp, string $source = 'general')
    {
        $this->experience_points += $xp;
        $leveledUp = false;

        while ($this->experience_points >= $this->experience_to_next_level) {
            $this->experience_points -= $this->experience_to_next_level;
            $this->level++;
            $this->experience_to_next_level = $this->calculateNextLevelXP();
            $leveledUp = true;

            // Level up rewards
            $this->addCoins($this->level * 10);
            if ($this->level % 5 === 0) {
                $this->addGems(5); // Bonus gems every 5 levels
            }
        }

        $this->save();

        // Log transaction
        $this->logTransaction('earn', 'experience', $xp, $source, "Earned {$xp} XP from {$source}");

        return ['leveled_up' => $leveledUp, 'new_level' => $this->level];
    }

    public function addPoints(int $points, string $source = 'general')
    {
        $this->total_points += $points;
        $this->save();
        $this->logTransaction('earn', 'points', $points, $source, "Earned {$points} points from {$source}");
        return $this;
    }

    public function addCoins(int $coins, string $source = 'general')
    {
        $this->coins += $coins;
        $this->save();
        $this->logTransaction('earn', 'coins', $coins, $source, "Earned {$coins} coins from {$source}");
        return $this;
    }

    public function addGems(int $gems, string $source = 'general')
    {
        $this->gems += $gems;
        $this->save();
        $this->logTransaction('earn', 'gems', $gems, $source, "Earned {$gems} gems from {$source}");
        return $this;
    }

    public function spendCoins(int $amount, string $purpose = 'purchase')
    {
        if ($this->coins < $amount) {
            return false;
        }
        $this->coins -= $amount;
        $this->save();
        $this->logTransaction('spend', 'coins', $amount, $purpose, "Spent {$amount} coins on {$purpose}");
        return true;
    }

    public function spendGems(int $amount, string $purpose = 'purchase')
    {
        if ($this->gems < $amount) {
            return false;
        }
        $this->gems -= $amount;
        $this->save();
        $this->logTransaction('spend', 'gems', $amount, $purpose, "Spent {$amount} gems on {$purpose}");
        return true;
    }

    // Energy System
    public function updateEnergy()
    {
        if (!$this->energy_last_updated) {
            $this->energy_last_updated = now();
            $this->save();
            return;
        }

        $minutesSinceUpdate = $this->energy_last_updated->diffInMinutes(now());
        $energyToAdd = floor($minutesSinceUpdate / 5); // 1 energy per 5 minutes

        if ($energyToAdd > 0) {
            $this->energy = min(100, $this->energy + $energyToAdd);
            $this->energy_last_updated = now();
            $this->save();
        }
    }

    public function useEnergy(int $amount = 1)
    {
        $this->updateEnergy();
        if ($this->energy >= $amount) {
            $this->energy -= $amount;
            $this->save();
            return true;
        }
        return false;
    }

    // Streak Management
    public function updateStreak()
    {
        $today = Carbon::today();
        $lastActivity = $this->last_activity_date ? Carbon::parse($this->last_activity_date) : null;

        if (!$lastActivity || $lastActivity->lt($today->copy()->subDay())) {
            // Broke streak or first activity
            if ($lastActivity && $lastActivity->eq($today->copy()->subDay())) {
                // Consecutive day
                $this->current_streak++;
            } else {
                // Reset streak
                $this->current_streak = 1;
            }
        } elseif ($lastActivity->eq($today)) {
            // Already active today
            return;
        }

        $this->last_activity_date = $today;
        $this->longest_streak = max($this->longest_streak, $this->current_streak);
        $this->save();

        // Streak rewards
        if ($this->current_streak % 7 === 0) {
            $this->addGems(2);
        }
        if ($this->current_streak % 30 === 0) {
            $this->addGems(10);
        }
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
        $quests = $this->daily_quests ?? [];

        foreach ($quests as &$quest) {
            if ($quest['id'] === $questId && $quest['progress'] < $quest['target']) {
                $quest['progress'] = min($quest['target'], $quest['progress'] + $progress);

                // Check if quest completed
                if ($quest['progress'] >= $quest['target'] && !isset($quest['completed'])) {
                    $quest['completed'] = true;
                    $quest['completed_at'] = now()->toISOString();

                    // Award rewards
                    $this->addCoins($quest['reward_coins'], 'daily_quest');
                    $this->addExperience($quest['reward_xp'], 'daily_quest');

                    return ['quest_completed' => true, 'quest' => $quest];
                }
            }
        }

        $this->daily_quests = $quests;
        $this->save();

        return ['quest_completed' => false];
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
            'currency_type' => $currency,
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