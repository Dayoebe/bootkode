<?php
// app/Livewire/Marketplace/Partial/PromotionsDiscounts.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class PromotionsDiscounts extends Component
{
    use WithPagination;

    public $activeTab = 'codes';
    
    // Discount Code Properties
    public $showCreateModal = false;
    public $editingCode = null;
    public $code = '';
    public $type = 'percentage';
    public $value = 0;
    public $minAmount = 0;
    public $maxUses = null;
    public $usesPerUser = 1;
    public $validFrom = '';
    public $validUntil = '';
    public $isActive = true;
    public $description = '';

    // Filters
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    public $search = '';

    protected $rules = [
        'code' => 'required|string|max:50|unique:discount_codes,code',
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'minAmount' => 'nullable|numeric|min:0',
        'maxUses' => 'nullable|integer|min:1',
        'usesPerUser' => 'required|integer|min:1|max:100',
        'validFrom' => 'nullable|date|after_or_equal:today',
        'validUntil' => 'nullable|date|after:validFrom',
        'description' => 'nullable|string|max:255',
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->editingCode = null;
        $this->resetForm();
    }

    public function generateCode()
    {
        $this->code = 'SAVE' . strtoupper(Str::random(6));
    }

    public function createDiscountCode()
    {
        $this->validate();

        // Simulate saving to database - you'll need to create a DiscountCode model
        $discountData = [
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'min_amount' => $this->minAmount ?: null,
            'max_uses' => $this->maxUses,
            'uses_per_user' => $this->usesPerUser,
            'valid_from' => $this->validFrom ? \Carbon\Carbon::parse($this->validFrom) : null,
            'valid_until' => $this->validUntil ? \Carbon\Carbon::parse($this->validUntil) : null,
            'is_active' => $this->isActive,
            'description' => $this->description,
            'created_by' => auth()->id(),
        ];

        // DiscountCode::create($discountData);

        session()->flash('message', 'Discount code created successfully!');
        $this->closeCreateModal();
    }

    public function toggleCodeStatus($codeId)
    {
        // Toggle active status - simulate with flash message
        session()->flash('message', 'Discount code status updated!');
    }

    public function deleteCode($codeId)
    {
        // Delete code - simulate with flash message
        session()->flash('message', 'Discount code deleted!');
    }

    private function resetForm()
    {
        $this->code = '';
        $this->type = 'percentage';
        $this->value = 0;
        $this->minAmount = 0;
        $this->maxUses = null;
        $this->usesPerUser = 1;
        $this->validFrom = '';
        $this->validUntil = '';
        $this->isActive = true;
        $this->description = '';
        $this->resetValidation();
    }

    // Simulate data - replace with actual database queries
    private function getDiscountCodes()
    {
        return collect([
            (object)[
                'id' => 1,
                'code' => 'WELCOME20',
                'type' => 'percentage',
                'value' => 20,
                'min_amount' => 100,
                'max_uses' => 100,
                'uses_per_user' => 1,
                'used_count' => 25,
                'valid_from' => now()->subDays(30),
                'valid_until' => now()->addDays(30),
                'is_active' => true,
                'description' => 'Welcome discount for new users',
                'created_at' => now()->subDays(30),
            ],
            (object)[
                'id' => 2,
                'code' => 'SAVE50FIXED',
                'type' => 'fixed',
                'value' => 50,
                'min_amount' => 200,
                'max_uses' => 50,
                'uses_per_user' => 2,
                'used_count' => 12,
                'valid_from' => now(),
                'valid_until' => now()->addDays(14),
                'is_active' => true,
                'description' => 'Fixed ₦50 discount',
                'created_at' => now()->subDays(5),
            ],
            (object)[
                'id' => 3,
                'code' => 'EXPIRED10',
                'type' => 'percentage',
                'value' => 10,
                'min_amount' => null,
                'max_uses' => 200,
                'uses_per_user' => 1,
                'used_count' => 45,
                'valid_from' => now()->subDays(60),
                'valid_until' => now()->subDays(10),
                'is_active' => false,
                'description' => 'Expired 10% discount',
                'created_at' => now()->subDays(60),
            ],
        ]);
    }

    private function getPromotionStats()
    {
        return [
            'total_codes' => 3,
            'active_codes' => 2,
            'expired_codes' => 1,
            'total_uses' => 82,
            'total_savings' => 2450,
            'conversion_rate' => 15.5,
        ];
    }

    public function render()
    {
        $discountCodes = $this->getDiscountCodes();
        $stats = $this->getPromotionStats();

        // Apply filters (simulate filtering)
        if ($this->statusFilter === 'active') {
            $discountCodes = $discountCodes->where('is_active', true);
        } elseif ($this->statusFilter === 'expired') {
            $discountCodes = $discountCodes->where('valid_until', '<', now());
        }

        if ($this->search) {
            $discountCodes = $discountCodes->filter(function($code) {
                return stripos($code->code, $this->search) !== false || 
                       stripos($code->description, $this->search) !== false;
            });
        }

        return view('livewire.marketplace.partial.promotions-discounts', [
            'discountCodes' => $discountCodes,
            'stats' => $stats,
        ]);
    }
}