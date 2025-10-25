<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Credentials\GamificationData;

class ResetDailyQuests extends Command
{
    protected $signature = 'gamification:reset-daily-quests';
    protected $description = 'Reset daily quests for all users';

    public function handle()
    {
        $count = 0;
        GamificationData::chunk(100, function ($gamificationData) use (&$count) {
            foreach ($gamificationData as $data) {
                $data->generateDailyQuests();
                $data->last_quest_reset = now()->startOfDay();
                $data->save();
                $count++;
            }
        });

        $this->info("Reset daily quests for {$count} users.");
        return 0;
    }
}