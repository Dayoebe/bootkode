<?php
// app/Livewire/Marketplace/Partial/MarketplaceContent.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductReview;
use App\Models\MarketplaceItem;
use App\Models\DiscountCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MarketplaceContent extends Component
{
    use WithPagination;

    // Main Tab Management
    public $activeTab = 'promotions';
    
    // === PROMOTIONS & DISCOUNTS PROPERTIES ===
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
    
    // Promotion Filters
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    public $promotionSearch = '';

    // === REVIEWS & RATINGS PROPERTIES ===
    public $reviewStatus = 'all';
    public $rating = '';
    public $reviewSearch = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    // Review Moderation
    public $showModerationModal = false;
    public $selectedReview = null;
    public $moderationAction = '';
    public $moderationReason = '';

    // Bulk Actions
    public $selectedReviews = [];
    public $bulkAction = '';
    public $selectAll = false;

    protected $rules = [
        'code' => 'required|string|max:50',
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric|min:0',
        'minAmount' => 'nullable|numeric|min:0',
        'maxUses' => 'nullable|integer|min:1',
        'usesPerUser' => 'required|integer|min:1|max:100',
        'validFrom' => 'nullable|date|after_or_equal:today',
        'validUntil' => 'nullable|date|after:validFrom',
        'description' => 'nullable|string|max:255',
        'moderationReason' => 'required_if:moderationAction,reject,delete|string|max:500',
    ];

    protected $listeners = [
        'refreshStats' => 'loadStats',
    ];

    public function mount()
    {
        $this->loadStats();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->resetFilters();
    }

    private function resetFilters()
    {
        if ($this->activeTab === 'promotions') {
            $this->promotionSearch = '';
            $this->statusFilter = 'all';
            $this->typeFilter = 'all';
        } else {
            $this->reviewSearch = '';
            $this->reviewStatus = 'all';
            $this->rating = '';
        }
        $this->selectedReviews = [];
        $this->selectAll = false;
    }

    // === PROMOTIONS & DISCOUNTS METHODS ===
    public function openCreateModal()
    {
        $this->resetPromotionForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->editingCode = null;
        $this->resetPromotionForm();
    }

    public function generateCode()
    {
        $this->code = 'SAVE' . strtoupper(Str::random(6));
    }

    public function createDiscountCode()
    {
        $this->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minAmount' => 'nullable|numeric|min:0',
            'maxUses' => 'nullable|integer|min:1',
            'usesPerUser' => 'required|integer|min:1|max:100',
            'validFrom' => 'nullable|date|after_or_equal:today',
            'validUntil' => 'nullable|date|after:validFrom',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DiscountCode::create([
                'code' => $this->code,
                'type' => $this->type,
                'value' => $this->value,
                'min_amount' => $this->minAmount ?: null,
                'max_uses' => $this->maxUses,
                'uses_per_user' => $this->usesPerUser,
                'valid_from' => $this->validFrom ? Carbon::parse($this->validFrom) : null,
                'valid_until' => $this->validUntil ? Carbon::parse($this->validUntil) : null,
                'is_active' => $this->isActive,
                'description' => $this->description,
                'created_by' => auth()->id(),
                'used_count' => 0,
            ]);

            session()->flash('message', 'Discount code created successfully!');
            $this->closeCreateModal();
            $this->loadStats();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create discount code: ' . $e->getMessage());
        }
    }

    public function toggleCodeStatus($codeId)
    {
        try {
            $code = DiscountCode::findOrFail($codeId);
            $code->update(['is_active' => !$code->is_active]);
            
            session()->flash('message', 'Discount code status updated successfully!');
            $this->loadStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update discount code status.');
        }
    }

    public function deleteCode($codeId)
    {
        try {
            DiscountCode::findOrFail($codeId)->delete();
            
            session()->flash('message', 'Discount code deleted successfully!');
            $this->loadStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete discount code.');
        }
    }

    private function resetPromotionForm()
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

    // === REVIEWS & RATINGS METHODS ===
    public function openModerationModal($reviewId, $action)
    {
        $this->selectedReview = ProductReview::with(['user', 'reviewable'])->findOrFail($reviewId);
        $this->moderationAction = $action;
        $this->moderationReason = '';
        $this->showModerationModal = true;
    }

    public function closeModerationModal()
    {
        $this->showModerationModal = false;
        $this->selectedReview = null;
        $this->moderationAction = '';
        $this->moderationReason = '';
        $this->resetValidation();
    }

    public function approveReview($reviewId)
    {
        try {
            $review = ProductReview::findOrFail($reviewId);
            $review->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'moderation_reason' => null,
            ]);

            // Update item rating
            if ($review->reviewable && method_exists($review->reviewable, 'updateRating')) {
                $review->reviewable->updateRating();
            }
            
            session()->flash('message', 'Review approved successfully!');
            $this->loadStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve review.');
        }
    }

    public function rejectReview()
    {
        $this->validate([
            'moderationReason' => 'required|string|max:500'
        ]);

        if (!$this->selectedReview) return;

        try {
            $this->selectedReview->update([
                'is_approved' => false,
                'moderation_reason' => $this->moderationReason,
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
            ]);

            session()->flash('message', 'Review rejected successfully!');
            $this->closeModerationModal();
            $this->loadStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject review.');
        }
    }

    public function deleteReview()
    {
        if (!$this->selectedReview) return;

        try {
            DB::beginTransaction();

            $reviewable = $this->selectedReview->reviewable;
            $this->selectedReview->delete();
            
            // Update item rating after deletion
            if ($reviewable && method_exists($reviewable, 'updateRating')) {
                $reviewable->updateRating();
            }

            DB::commit();

            session()->flash('message', 'Review deleted successfully!');
            $this->closeModerationModal();
            $this->loadStats();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete review.');
        }
    }

    public function toggleFeatured($reviewId)
    {
        try {
            $review = ProductReview::findOrFail($reviewId);
            $review->update(['is_featured' => !$review->is_featured]);
            
            $status = $review->is_featured ? 'featured' : 'unfeatured';
            session()->flash('message', "Review {$status} successfully!");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update review status.');
        }
    }

    public function bulkActionReviews()
    {
        if (empty($this->selectedReviews) || !$this->bulkAction) {
            session()->flash('error', 'Please select reviews and an action.');
            return;
        }

        try {
            DB::beginTransaction();

            $reviews = ProductReview::whereIn('id', $this->selectedReviews);
            $count = count($this->selectedReviews);

            switch ($this->bulkAction) {
                case 'approve':
                    $reviews->update([
                        'is_approved' => true,
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                        'moderation_reason' => null,
                    ]);
                    $message = "Approved {$count} reviews successfully!";
                    break;

                case 'reject':
                    $reviews->update([
                        'is_approved' => false,
                        'moderation_reason' => 'Bulk rejection by admin',
                        'moderated_by' => auth()->id(),
                        'moderated_at' => now(),
                    ]);
                    $message = "Rejected {$count} reviews successfully!";
                    break;

                case 'feature':
                    $reviews->update(['is_featured' => true]);
                    $message = "Featured {$count} reviews successfully!";
                    break;

                case 'unfeature':
                    $reviews->update(['is_featured' => false]);
                    $message = "Unfeatured {$count} reviews successfully!";
                    break;

                case 'delete':
                    $reviewables = ProductReview::whereIn('id', $this->selectedReviews)->with('reviewable')->get();
                    $reviews->delete();
                    
                    // Update ratings for affected items
                    foreach ($reviewables->unique('reviewable_id') as $review) {
                        if ($review->reviewable && method_exists($review->reviewable, 'updateRating')) {
                            $review->reviewable->updateRating();
                        }
                    }
                    $message = "Deleted {$count} reviews successfully!";
                    break;
            }

            DB::commit();

            $this->selectedReviews = [];
            $this->bulkAction = '';
            $this->selectAll = false;
            $this->loadStats();

            session()->flash('message', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    // Watch for select all changes
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedReviews = $this->getReviews()->pluck('id')->toArray();
        } else {
            $this->selectedReviews = [];
        }
    }

    // === DATA RETRIEVAL METHODS ===
    private function loadStats()
    {
        // Clear cached stats to force refresh
        Cache::forget('marketplace_content_stats');
        Cache::forget('promotion_stats');
        Cache::forget('review_stats');
    }

    private function getPromotionStats()
    {
        return Cache::remember('promotion_stats', 300, function() {
            $totalCodes = DiscountCode::count();
            $activeCodes = DiscountCode::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('valid_until')
                      ->orWhere('valid_until', '>=', now());
                })
                ->count();
            
            $expiredCodes = DiscountCode::where(function($q) {
                $q->where('is_active', false)
                  ->orWhere('valid_until', '<', now());
            })->count();
            
            $totalUses = DiscountCode::sum('used_count');
            $totalSavings = DB::table('marketplace_orders')
                ->where('discount_amount', '>', 0)
                ->sum('discount_amount');

            return [
                'total_codes' => $totalCodes,
                'active_codes' => $activeCodes,
                'expired_codes' => $expiredCodes,
                'total_uses' => $totalUses,
                'total_savings' => $totalSavings,
                'conversion_rate' => $totalCodes > 0 ? round(($totalUses / $totalCodes) * 100, 1) : 0,
            ];
        });
    }

    private function getReviewStats()
    {
        return Cache::remember('review_stats', 300, function() {
            $totalReviews = ProductReview::where('reviewable_type', MarketplaceItem::class)->count();
            $approvedReviews = ProductReview::where('reviewable_type', MarketplaceItem::class)
                ->where('is_approved', true)->count();
            $pendingReviews = ProductReview::where('reviewable_type', MarketplaceItem::class)
                ->where('is_approved', false)->count();
            $featuredReviews = ProductReview::where('reviewable_type', MarketplaceItem::class)
                ->where('is_featured', true)->count();
            $averageRating = ProductReview::where('reviewable_type', MarketplaceItem::class)
                ->where('is_approved', true)->avg('rating') ?? 0;

            return [
                'total_reviews' => $totalReviews,
                'approved_reviews' => $approvedReviews,
                'pending_reviews' => $pendingReviews,
                'featured_reviews' => $featuredReviews,
                'average_rating' => round($averageRating, 1),
            ];
        });
    }

    private function getRatingDistribution()
    {
        return Cache::remember('rating_distribution', 300, function() {
            $distribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $distribution[$i] = ProductReview::where('reviewable_type', MarketplaceItem::class)
                    ->where('is_approved', true)
                    ->where('rating', $i)
                    ->count();
            }
            return $distribution;
        });
    }

    private function getDiscountCodes()
    {
        $query = DiscountCode::query();

        // Apply filters
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true)
                  ->where(function($q) {
                      $q->whereNull('valid_until')
                        ->orWhere('valid_until', '>=', now());
                  });
        } elseif ($this->statusFilter === 'expired') {
            $query->where(function($q) {
                $q->where('is_active', false)
                  ->orWhere('valid_until', '<', now());
            });
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->promotionSearch) {
            $query->where(function($q) {
                $q->where('code', 'like', '%' . $this->promotionSearch . '%')
                  ->orWhere('description', 'like', '%' . $this->promotionSearch . '%');
            });
        }

        return $query->latest()->get();
    }

    private function getReviews()
    {
        $query = ProductReview::with(['user', 'reviewable'])
            ->where('reviewable_type', MarketplaceItem::class);

        // Apply filters
        if ($this->reviewStatus === 'approved') {
            $query->where('is_approved', true);
        } elseif ($this->reviewStatus === 'pending') {
            $query->where('is_approved', false);
        } elseif ($this->reviewStatus === 'featured') {
            $query->where('is_featured', true);
        }

        if ($this->rating) {
            $query->where('rating', $this->rating);
        }

        if ($this->reviewSearch) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->reviewSearch . '%')
                  ->orWhere('comment', 'like', '%' . $this->reviewSearch . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->reviewSearch . '%');
                  })
                  ->orWhereHas('reviewable', function($itemQuery) {
                      $itemQuery->where('title', 'like', '%' . $this->reviewSearch . '%');
                  });
            });
        }

        $query->orderBy($this->sortBy, $this->sortOrder);
        return $query->paginate(10);
    }

    public function render()
    {
        $data = [];

        if ($this->activeTab === 'promotions') {
            $data = [
                'discountCodes' => $this->getDiscountCodes(),
                'promotionStats' => $this->getPromotionStats(),
            ];
        } elseif ($this->activeTab === 'reviews') {
            $data = [
                'reviews' => $this->getReviews(),
                'reviewStats' => $this->getReviewStats(),
                'ratingDistribution' => $this->getRatingDistribution(),
            ];
        }

        return view('livewire.marketplace.partial.marketplace-content', $data);
    }
}