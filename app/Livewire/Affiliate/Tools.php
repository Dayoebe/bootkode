<?php
// app/Livewire/Affiliate/Tools.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Services\AffiliateService;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate tools'])]
class Tools extends Component
{
    public $selectedTool = 'links';
    public $customMessage = '';
    public $copiedText = '';
    
    private AffiliateService $affiliateService;

    public function boot(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function mount()
    {
        $user = auth()->user();
        
        if (!$user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
    }

    public function setTool($tool)
    {
        $this->selectedTool = $tool;
    }

    public function copyToClipboard($text, $type)
    {
        $this->copiedText = $type;
        $this->dispatch('clipboard-copy', text: $text);
        
        // Reset after 3 seconds
        $this->js("setTimeout(() => { \$wire.copiedText = ''; }, 3000)");
    }

    public function generateCustomEmail()
    {
        $user = auth()->user();
        if (!$user->isAffiliate()) {
            return;
        }

        $template = "Hi there!\n\n" .
                   ($this->customMessage ?: "I wanted to share something amazing with you - BootKode!") . "\n\n" .
                   "It's a comprehensive coding education platform where you can learn programming from scratch or advance your existing skills.\n\n" .
                   "Use my referral link to get started: {$user->affiliate->referral_link}\n\n" .
                   "Best regards,\n{$user->name}";

        $this->dispatch('custom-email-generated', template: $template);
    }

    public function render()
    {
        $user = auth()->user();
        
        // The redirect should be handled in mount() instead
        if (!$user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
        
        $affiliate = $user->affiliate;
        $marketingAssets = $this->affiliateService->generateMarketingAssets($affiliate);
        
        return view('livewire.affiliate.tools', [
            'affiliate' => $affiliate,
            'marketingAssets' => $marketingAssets
        ]);
    }
}