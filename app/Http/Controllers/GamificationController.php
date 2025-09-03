<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GamificationService;
use App\Models\GamificationData;
use App\Models\UserBadge;
use App\Models\RewardStoreItem;
use App\Models\UserStorePurchase;

class GamificationController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function dashboard()
    {
        return view('livewire.gamification.gamification-dashboard');
    }

    public function badges()
    {
        $user = auth()->user();
        $userBadges = $user->badges()->latest()->paginate(20);
        
        // Get achievement progress
        $progress = $user->getAchievementProgress();
        
        return view('livewire.gamification.partial.gamification.badges', compact('userBadges', 'progress'));
    }

    public function leaderboard(Request $request)
    {
        $type = $request->get('type', 'overall');
        $leaderboards = $this->gamificationService->getLeaderboards();
        
        return view('livewire.gamification.partial.gamification.leaderboard', compact('leaderboards', 'type'));
    }

    public function store()
    {
        $user = auth()->user();
        $storeItems = RewardStoreItem::where('is_available', true)
            ->orderBy('item_type')
            ->orderBy('cost_coins')
            ->get()
            ->groupBy('item_type');
        
        $userPurchases = $user->storePurchases()->pluck('reward_store_item_id')->toArray();
        $userStats = $user->getGamificationSummary();
        
        return view('livewire.gamification.partial.gamification.store', compact('storeItems', 'userPurchases', 'userStats'));
    }

    public function games()
    {
        $user = auth()->user();
        $availableGames = $this->gamificationService->getAvailableGames();
        $gameStats = $user->getGameStats();
        $energyStatus = $user->getEnergyStatus();
        
        return view('livewire.gamification.partial.gamification.games', compact('availableGames', 'gameStats', 'energyStatus'));
    }

    public function playGame($gameId)
    {
        $user = auth()->user();
        $games = $this->gamificationService->getAvailableGames();
        $game = collect($games)->firstWhere('id', $gameId);
        
        if (!$game) {
            return redirect()->route('livewire.gamification.partial.gamification.games')->with('error', 'Game not found.');
        }
        
        $energyStatus = $user->getEnergyStatus();
        
        if ($energyStatus['current'] < $game['energy_cost']) {
            return redirect()->route('livewire.gamification.partial.gamification.games')
                ->with('error', "Not enough energy! You need {$game['energy_cost']} energy to play this game.");
        }
        
        return view('livewire.gamification.partial.gamification.games.' . $gameId, compact('game', 'energyStatus'));
    }

    public function purchaseItem(Request $request, $itemId)
    {
        $user = auth()->user();
        $result = $this->gamificationService->purchaseItem($user, $itemId);
        
        if ($request->expectsJson()) {
            return response()->json($result);
        }
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->with('error', $result['message']);
        }
    }

    public function toggleEquip(Request $request, $purchaseId)
    {
        $user = auth()->user();
        $success = $user->toggleEquipItem($purchaseId);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => $success]);
        }
        
        return back()->with($success ? 'success' : 'error', 
            $success ? 'Item equipped/unequipped successfully!' : 'Failed to toggle item.');
    }

    // API endpoints for AJAX calls
    public function gameComplete(Request $request)
    {
        $user = auth()->user();
        $gameId = $request->input('game_id');
        $score = $request->input('score', 0);
        
        $result = $this->gamificationService->handleGameCompletion($user, $gameId, $score);
        
        // Check for new achievements
        $newBadges = $user->checkAllAchievements();
        $result['new_badges'] = $newBadges;
        
        return response()->json($result);
    }

    public function claimDaily(Request $request)
    {
        $user = auth()->user();
        $result = $this->gamificationService->handleDailyLogin($user);
        
        // Check for new achievements
        $newBadges = $user->checkAllAchievements();
        $result['new_badges'] = $newBadges;
        
        return response()->json($result);
    }

    public function userStats(Request $request)
    {
        $user = auth()->user();
        $stats = $user->getGamificationSummary();
        
        return response()->json($stats);
    }
}
