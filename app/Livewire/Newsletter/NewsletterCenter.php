<?php
// UPDATED: NewsletterCenter.php - Add Performance tab
namespace App\Livewire\Newsletter;

use Livewire\Component;
use App\Models\User;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Newsletter Management', 'description' => 'Manage subscribers, campaigns, and email marketing', 'icon' => 'fas fa-envelope', 'active' => 'newsletter'])]

class NewsletterCenter extends Component
{
    public $activeTab = 'subscribers';
    public $user;
    public $stats = [];

    public function mount()
    {
        $this->user = auth()->user();

        if (!$this->user->hasRole([User::ROLE_CONTENT_EDITOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN])) {
            abort(403, 'Unauthorized access to newsletter management.');
        }

        $currentRoute = Route::currentRouteName();
        $this->activeTab = match ($currentRoute) {
            'newsletter.subscribers' => 'subscribers',
            'newsletter.campaigns' => 'campaigns',
            'newsletter.templates' => 'templates',
            'newsletter.analytics' => 'analytics',
            'newsletter.reports' => 'reports',
            'newsletter.performance' => 'performance', // NEW
            'newsletter.settings' => 'settings',
            default => 'subscribers'
        };

        $this->loadStatistics();
    }

    public function setActiveTab($tab)
    {
        if ($tab === 'settings' && !$this->user->hasRole(User::ROLE_SUPER_ADMIN)) {
            session()->flash('error', 'You do not have permission to access newsletter settings.');
            return;
        }

        $this->activeTab = $tab;
        $this->loadStatistics();
    }

    // ADD MISSING METHODS
    public function createCampaign()
    {
        $this->setActiveTab('campaigns');
        $this->dispatch('show-create-campaign-modal');
    }

    public function importSubscribers()
    {
        $this->setActiveTab('subscribers');
        $this->dispatch('show-import-modal');
    }

    public function viewReports()
    {
        $this->setActiveTab('reports');
    }

    public function viewPerformance() // NEW
    {
        $this->setActiveTab('performance');
    }

    protected function loadStatistics()
    {
        try {
            $this->stats['total_subscribers'] = NewsletterSubscriber::count();
            $this->stats['active_subscribers'] = NewsletterSubscriber::active()->count();
            $this->stats['total_campaigns'] = NewsletterCampaign::campaigns()->count();
            $this->stats['campaigns_sent'] = NewsletterCampaign::campaigns()->where('status', NewsletterCampaign::STATUS_SENT)->count();
            $this->stats['total_templates'] = NewsletterCampaign::templates()->count();
            $this->stats['total_reports'] = NewsletterCampaign::campaigns()->where('status', NewsletterCampaign::STATUS_SENT)->count();

            // Calculate average rates from sent campaigns
            $sentCampaigns = NewsletterCampaign::campaigns()
                ->where('status', NewsletterCampaign::STATUS_SENT)
                ->where('sent_count', '>', 0)
                ->get();

            if ($sentCampaigns->count() > 0) {
                $this->stats['avg_open_rate'] = round($sentCampaigns->avg('open_rate'), 2);
                $this->stats['avg_click_rate'] = round($sentCampaigns->avg('click_rate'), 2);
            } else {
                $this->stats['avg_open_rate'] = 0;
                $this->stats['avg_click_rate'] = 0;
            }

            $this->stats['recent_signups'] = NewsletterSubscriber::where('created_at', '>=', now()->subDays(7))->count();

        } catch (\Exception $e) {
            $this->stats = [
                'total_subscribers' => 0,
                'active_subscribers' => 0,
                'total_campaigns' => 0,
                'campaigns_sent' => 0,
                'total_templates' => 0,
                'total_reports' => 0,
                'avg_open_rate' => 0,
                'avg_click_rate' => 0,
                'recent_signups' => 0,
            ];
        }
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        session()->flash('message', 'Statistics refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.newsletter.newsletter-center', [
            'user' => $this->user,
            'stats' => $this->stats,
        ]);
    }
}