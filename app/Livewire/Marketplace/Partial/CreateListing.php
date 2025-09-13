<?php

namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceCategory;
use Illuminate\Support\Facades\Storage;

class CreateListing extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $short_description = '';
    public $type = 'course';
    public $price = 0;
    public $discount_price = null;
    public $is_digital = true;
    public $tags = [];
    public $thumbnail;
    public $images = [];
    public $files = [];
    public $meta_title = '';
    public $meta_description = '';
    public $keywords = '';
    public $duration_minutes = null;
    public $selectedCategories = [];
    public $tagInput = ''; // Add this for the tag input field

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:100',
        'short_description' => 'required|string|max:160',
        'type' => 'required|in:course,resource,service',
        'price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|lt:price',
        'thumbnail' => 'nullable|image|max:2048',
        'selectedCategories' => 'required|array|min:1',
        'selectedCategories.*' => 'exists:marketplace_categories,id',
        'tags' => 'array', // Changed from 'nullable|array|min:1'
    ];
    
    public function save()
    {
        $this->validate();

        $thumbnailPath = null;
        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('marketplace/thumbnails', 'public');
        }

        $item = MarketplaceItem::create([
            'vendor_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'type' => $this->type,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'is_digital' => $this->is_digital,
            'thumbnail' => $thumbnailPath,
            'tags' => $this->tags,
            'meta_title' => $this->meta_title ?: $this->title,
            'meta_description' => $this->meta_description ?: $this->short_description,
            'keywords' => $this->keywords,
            'duration_minutes' => $this->duration_minutes,
            'status' => MarketplaceItem::STATUS_DRAFT,
        ]);

        // Attach categories
        $item->categories()->attach($this->selectedCategories);

        session()->flash('message', 'Listing created successfully! You can now add more details.');
        
        return redirect()->route('marketplace.seller.listings');
    }

    public function saveDraft()
    {
        $this->save();
    }

    public function submitForReview()
    {
        $this->validate();
        // Additional validation for submission
        $this->validate([
            'thumbnail' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]);

        $item = $this->save();
        $item->submitForReview();

        session()->flash('message', 'Listing submitted for review!');
    }

    // Add this method to handle tag input
    public function updatedTagInput($value)
    {
        if (!empty($value)) {
            $tags = array_map('trim', explode(',', $value));
            $this->tags = array_merge($this->tags, $tags);
            $this->tagInput = '';
        }
    }

    // Add this method to remove a tag
    public function removeTag($index)
    {
        if (isset($this->tags[$index])) {
            unset($this->tags[$index]);
            $this->tags = array_values($this->tags); // Reindex array
        }
    }

    public function render()
    {
        $availableCategories = MarketplaceCategory::active()
            ->ordered()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return view('livewire.marketplace.partial.create-listing', [
            'types' => MarketplaceItem::TYPES,
            'availableCategories' => $availableCategories,
        ]);
    }
}