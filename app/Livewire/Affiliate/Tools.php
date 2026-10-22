<?php
// app/Livewire/Affiliate/Tools.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Services\AffiliateService;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate tools'])]
class Tools extends Component
{
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;

    use \App\Livewire\Concerns\HasActionFeedback;

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
        abort_unless(in_array($tool, ['links', 'banners', 'social', 'email'], true), 422);
        $this->selectedTool = $tool;
    }

    public function copyToClipboard($text, $type)
    {
        abort_unless(auth()->user()->affiliate?->isActive(), 403);
        $this->dispatch('clipboard-copy', text: $text, type: $type);
        
        // Reset after 3 seconds
        $this->js("setTimeout(() => { \$wire.copiedText = ''; }, 3000)");
    }

    public function clipboardResult(string $type, bool $success): void
    {
        $this->copiedText = $success ? $type : '';
        $this->flashFeedback($success ? 'success' : 'error', $success ? 'Copied to clipboard.' : 'Clipboard access was denied. Select and copy the text manually.');
    }

    public function generateCustomEmail()
    {
        $user = auth()->user();
        if (!$user->isAffiliate()) {
            return;
        }

        abort_unless($user->affiliate?->isActive(), 403);
        $this->validate(['customMessage' => 'string|max:2000']);
        $template = "Hi there!\n\n" .
                   ($this->customMessage ?: "Explore practical technology learning with BootKode.") . "\n\n" .
                   "Explore the published courses and resources. Learn. Build. Grow.\n\n" .
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