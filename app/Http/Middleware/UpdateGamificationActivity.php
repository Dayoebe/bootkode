<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UpdateGamificationActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $gamificationData = $user->getOrCreateGamificationData();
            
            // Update daily activity and energy
            $gamificationData->updateStreak();
            $gamificationData->updateEnergy();
            
            // Check for daily quest resets
            $this->checkQuestReset($gamificationData);
        }
        
        return $next($request);
    }
    
    private function checkQuestReset($gamificationData)
    {
        $now = now();
        $lastReset = $gamificationData->last_quest_reset ?? $now->copy()->subDay();
        
        // Reset daily quests at midnight
        if ($lastReset->format('Y-m-d') !== $now->format('Y-m-d')) {
            $gamificationData->generateDailyQuests();
            $gamificationData->last_quest_reset = $now->startOfDay();
            $gamificationData->save();
        }
    }
}