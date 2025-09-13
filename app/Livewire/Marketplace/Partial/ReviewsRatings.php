<?php
// app/Livewire/Marketplace/Partial/ReviewsRatings.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Review;
use App\Models\MarketplaceItem;

class ReviewsRatings extends Component
{
    use WithPagination;

    public $status = 'all';
    public $rating = '';
    public $search = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';

    // Moderation
    public $showModerationModal = false;
    public $selectedReview = null;
    public $moderationAction = '';
    public $moderationReason = '';

    protected $queryString = [
        'status' => ['except' => 'all'],
        'rating' => ['except' => ''],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingRating()
    {
        $this->resetPage();
    }

    public function openModerationModal($reviewId, $action)
    {
        $this->selectedReview = Review::findOrFail($reviewId);
        $this->moderationAction = $action;
        $this->showModerationModal = true;
    }

    public function closeModerationModal()
    {
        $this->showModerationModal = false;
        $this->selectedReview = null;
        $this->moderationAction = '';
        $this->moderationReason = '';
    }

    public function approveReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->approve();
        
        session()->flash('message', 'Review approved successfully!');
    }

    public function rejectReview()
    {
        if (!$this->selectedReview || !$this->moderationReason) {
            session()->flash('error', 'Please provide a reason for rejection.');
            return;
        }

        $this->selectedReview->update([
            'is_approved' => false,
            'moderation_reason' => $this->moderationReason,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        session()->flash('message', 'Review rejected successfully!');
        $this->closeModerationModal();
    }

    public function deleteReview()
    {
        if (!$this->selectedReview) return;

        $this->selectedReview->delete();
        
        // Update item rating
        if ($this->selectedReview->reviewable) {
            $this->selectedReview->reviewable->updateRating();
        }

        session()->flash('message', 'Review deleted successfully!');
        $this->closeModerationModal();
    }

    public function toggleFeatured($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['is_featured' => !$review->is_featured]);
        
        $status = $review->is_featured ? 'featured' : 'unfeatured';
        session()->flash('message', "Review {$status} successfully!");
    }

    public function getReviewStats()
    {
        return [
            'total_reviews' => Review::where('reviewable_type', MarketplaceItem::class)->count(),
            'approved_reviews' => Review::where('reviewable_type', MarketplaceItem::class)->approved()->count(),
            'pending_reviews' => Review::where('reviewable_type', MarketplaceItem::class)->where('is_approved', false)->count(),
            'featured_reviews' => Review::where('reviewable_type', MarketplaceItem::class)->featured()->count(),
            'average_rating' => Review::where('reviewable_type', MarketplaceItem::class)->approved()->avg('rating') ?? 0,
        ];
    }

    public function getRatingDistribution()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = Review::where('reviewable_type', MarketplaceItem::class)
                ->approved()
                ->where('rating', $i)
                ->count();
        }
        return $distribution;
    }

    public function render()
    {
        $query = Review::with(['user', 'reviewable'])
            ->where('reviewable_type', MarketplaceItem::class);

        // Apply filters
        if ($this->status === 'approved') {
            $query->approved();
        } elseif ($this->status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($this->status === 'featured') {
            $query->featured();
        }

        if ($this->rating) {
            $query->where('rating', $this->rating);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('comment', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('reviewable', function($itemQuery) {
                      $itemQuery->where('title', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $query->orderBy($this->sortBy, $this->sortOrder);
        $reviews = $query->paginate(15);

        return view('livewire.marketplace.partial.reviews-ratings', [
            'reviews' => $reviews,
            'stats' => $this->getReviewStats(),
            'ratingDistribution' => $this->getRatingDistribution(),
        ]);
    }
}