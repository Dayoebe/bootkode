<?php

namespace App\Livewire\Gamification;

use App\Models\Marketplace\RewardStoreItem;
use App\Services\GamificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Learning rewards', 'active' => 'gamification.dashboard'])]
class GamificationDashboard extends Component
{
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;
    use \App\Livewire\Concerns\HasActionFeedback;

    public string $activeTab = 'overview';
    public bool $publicLeaderboard = false;
    protected GamificationService $gamificationService;

    public function boot(GamificationService $service): void
    {
        $this->gamificationService = $service;
    }

    public function mount(string $activeTab = 'overview'): void
    {
        $this->setActiveTab($activeTab);
        $this->publicLeaderboard = (bool) (auth()->user()->metadata['public_leaderboard'] ?? false);
    }

    public function setActiveTab(string $tab): void
    {
        abort_unless(in_array($tab, ['overview', 'badges', 'games', 'leaderboard', 'store'], true), 422);
        $this->activeTab = $tab;
    }

    public function claimDailyReward(): void
    {
        $result = $this->gamificationService->handleDailyLogin(auth()->user());
        $this->flashFeedback($result['success'] ? 'success' : 'info', $result['message']);
    }

    public function saveLeaderboardPreference(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            $user = \App\Models\Core\User::whereKey(auth()->id())->lockForUpdate()->firstOrFail();
            $user->update(['metadata' => array_merge($user->metadata ?? [], ['public_leaderboard' => $this->publicLeaderboard])]);
        });
        $this->flashFeedback('success', 'Participation saved. Your profile must also be public to appear.');
    }

    public function purchaseItem(int $itemId): void
    {
        $result = $this->gamificationService->purchaseItem(auth()->user(), $itemId);
        $this->flashFeedback($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function playGame($gameId): void
    {
        $this->flashFeedback('info', $this->gamificationService->handleGameCompletion(auth()->user(), $gameId, 0)['message']);
    }

    public function refreshData(): void
    {
        $this->flashFeedback('success', 'Your recorded rewards have been refreshed.');
    }

    public function render()
    {
        abort_unless(in_array($this->activeTab, ['overview', 'badges', 'games', 'leaderboard', 'store'], true), 422);
        $user = auth()->user();
        return view('livewire.gamification.gamification-dashboard', [
            'userStats' => $this->gamificationService->getUserDashboardStats($user),
            'leaderboards' => $this->activeTab === 'leaderboard' ? $this->gamificationService->getLeaderboards() : [],
            'badges' => $user->badges()->latest()->get(),
            'storeItems' => RewardStoreItem::where('is_available', true)->orderBy('cost_coins')->get(),
            'purchases' => $user->storePurchases()->with('item')->latest()->get(),
            'recentTransactions' => $user->gamificationTransactions()->latest('id')->limit(10)->get(),
        ]);
    }
}
