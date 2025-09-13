<?php
// app/Livewire/Marketplace/Partial/BrowseMarketplace.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceItem;

class BrowseMarketplace extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $category = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $minPrice = '';
    public $maxPrice = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'category' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = MarketplaceItem::published()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('keywords', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, fn($query) => $query->byType($this->type))
            ->when($this->category, fn($query) => $query->inCategory($this->category))
            ->when($this->minPrice, fn($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice, fn($query) => $query->where('price', '<=', $this->maxPrice))
            ->orderBy($this->sortBy, $this->sortOrder)
            ->paginate(12);

        $featuredItems = MarketplaceItem::published()->featured()->limit(6)->get();

        return view('livewire.marketplace.partial.browse-marketplace', [
            'items' => $items,
            'featuredItems' => $featuredItems,
            'types' => MarketplaceItem::TYPES,
            'categories' => $this->getCategories(),
        ]);
    }

    protected function getCategories()
    {
        return [
            'programming' => 'Programming',
            'design' => 'Design',
            'business' => 'Business',
            'marketing' => 'Marketing',
            'data-science' => 'Data Science',
            'mobile-development' => 'Mobile Development',
            'web-development' => 'Web Development',
            'devops' => 'DevOps',
        ];
    }
}
