<?php

namespace App\Livewire\Gamification;

use Livewire\Component;
use App\Services\GamificationService;
use App\Models\GamificationData;
use App\Models\UserBadge;
use App\Models\RewardStoreItem;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Gamification Dashboard', 'description' => 'Manage your gamification stats', 'icon' => 'fas fa-trophy'])]

class GamificationDashboard extends Component
{
    public $activeTab = 'overview';
    public $userStats = [];
    public $leaderboards = [];
    public $availableGames = [];
    public $storeItems = [];
    public $recentTransactions = [];
    public $showNotification = false;
    public $notificationData = [];

    protected $gamificationService;

    public function mount()
    {
        $this->gamificationService = new GamificationService();
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();
        $this->userStats = $this->gamificationService->getUserDashboardStats($user);
        $this->leaderboards = $this->gamificationService->getLeaderboards();
        $this->availableGames = $this->gamificationService->getAvailableGames();
        
        // Get store items grouped by type
        $this->storeItems = RewardStoreItem::where('is_available', true)
            ->orderBy('item_type')
            ->orderBy('cost_coins')
            ->get()
            ->groupBy('item_type');
        
        $this->recentTransactions = $user->gamificationData?->transactions()->latest()->limit(5)->get() ?? collect();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->dispatch('tab-changed', ['tab' => $tab]);
    }

    public function claimDailyReward()
    {
        $result = $this->gamificationService->handleDailyLogin(auth()->user());

        if ($result['first_login_today'] ?? false) {
            $this->showNotification([
                'type' => 'success',
                'title' => 'Daily Reward Claimed!',
                'message' => "You earned {$result['coins_gained']} coins and {$result['xp_gained']} XP!",
                'rewards' => $result
            ]);
            $this->loadData();
        } else {
            $this->showNotification([
                'type' => 'warning',
                'title' => 'Already Claimed',
                'message' => 'You have already claimed your daily reward today!'
            ]);
        }
    }

    public function purchaseItem($itemId)
    {
        $result = $this->gamificationService->purchaseItem(auth()->user(), $itemId);

        $this->showNotification([
            'type' => $result['success'] ? 'success' : 'error',
            'title' => $result['success'] ? 'Purchase Successful!' : 'Purchase Failed',
            'message' => $result['message']
        ]);

        if ($result['success']) {
            $this->loadData();
        }
    }

    public function playGame($gameId)
    {
        $user = auth()->user();
        $data = $user->getOrCreateGamificationData();
        $game = collect($this->availableGames)->firstWhere('id', $gameId);

        if (!$game) {
            $this->showNotification([
                'type' => 'error',
                'title' => 'Game Not Found',
                'message' => 'The selected game is not available.'
            ]);
            return;
        }

        $data->updateEnergy();

        if ($data->energy < $game['energy_cost']) {
            $this->showNotification([
                'type' => 'warning',
                'title' => 'Not Enough Energy',
                'message' => "You need {$game['energy_cost']} energy to play this game. Wait for energy to regenerate or purchase an energy refill!"
            ]);
            return;
        }

        // Simulate game play and completion
        $this->simulateGamePlay($gameId, $game);
    }

    private function simulateGamePlay($gameId, $game)
    {
        // Simulate a random score between 50-100% of max possible
        $scorePercentage = rand(50, 100);
        $score = intval(($scorePercentage / 100) * $game['max_score_reward']);
        
        $result = $this->gamificationService->handleGameCompletion(auth()->user(), $gameId, $score);
        
        if ($result['success']) {
            $message = "Game completed! Score: " . number_format($score);
            if ($result['new_record']) {
                $message .= " (New Record!)";
            }
            
            $this->showNotification([
                'type' => 'success',
                'title' => 'Game Completed!',
                'message' => $message,
                'rewards' => [
                    'coins_gained' => $result['coins_earned'],
                    'xp_gained' => $result['xp_earned'],
                    'level_up' => $result['level_up'],
                    'new_level' => $result['new_level'] ?? null
                ]
            ]);
            
            $this->loadData();
        }
    }

    public function showNotification($data)
    {
        $this->notificationData = $data;
        $this->showNotification = true;
        $this->dispatch('show-notification');
    }

    public function hideNotification()
    {
        $this->showNotification = false;
        $this->notificationData = [];
    }

    public function refreshData()
    {
        $this->loadData();
        $this->dispatch('data-refreshed');
        
        $this->showNotification([
            'type' => 'success',
            'title' => 'Data Refreshed',
            'message' => 'Your stats have been updated!'
        ]);
    }

    public function render()
    {
        return view('livewire.gamification.gamification-dashboard', [
            'userStats' => $this->userStats,
            'leaderboards' => $this->leaderboards,
            'availableGames' => $this->availableGames,
            'storeItems' => $this->storeItems,
            'recentTransactions' => $this->recentTransactions
        ]);
    }
}