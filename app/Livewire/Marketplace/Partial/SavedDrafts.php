<?php

// app/Livewire/Marketplace/Partial/SavedDrafts.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\MarketplaceItem;

class SavedDrafts extends Component
{
    public function render()
    {
        $drafts = MarketplaceItem::byVendor(auth()->id())
            ->where('status', MarketplaceItem::STATUS_DRAFT)
            ->latest()
            ->get();

        return view('livewire.marketplace.partial.saved-drafts', [
            'drafts' => $drafts
        ]);
    }
}
