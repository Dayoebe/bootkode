<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ForumThread;
use App\Models\ForumCategory;
use App\Models\ForumReply;

class Forums extends Component
{
    use WithPagination;

    public $selectedCategory = null;
    public $showCreateThread = false;
    public $showCreateCategory = false;
    public $search = '';

    // Thread form
    public $threadTitle = '';
    public $threadContent = '';
    public $threadCategoryId = '';

    // Category form
    public $categoryName = '';
    public $categoryDescription = '';
    public $categoryIcon = 'fas fa-folder';
    public $categoryColor = '#3B82F6';

    protected $rules = [
        'threadTitle' => 'required|min:5|max:255',
        'threadContent' => 'required|min:10',
        'threadCategoryId' => 'required|exists:forum_categories,id',
        'categoryName' => 'required|min:3|max:100',
        'categoryDescription' => 'nullable|max:500',
        'categoryIcon' => 'required|string',
        'categoryColor' => 'required|string',
    ];

    public function mount()
    {
        // Create default categories if none exist
        if (ForumCategory::count() === 0) {
            $this->createDefaultCategories();
        }
    }

    private function createDefaultCategories()
    {
        $categories = [
            [
                'name' => 'General Discussion',
                'description' => 'General discussions about learning and technology',
                'icon' => 'fas fa-comments',
                'color' => '#3B82F6',
                'order' => 1,
            ],
            [
                'name' => 'Course Help',
                'description' => 'Ask questions and get help with course content',
                'icon' => 'fas fa-question-circle',
                'color' => '#10B981',
                'order' => 2,
            ],
            [
                'name' => 'Project Showcase',
                'description' => 'Share your projects and get feedback',
                'icon' => 'fas fa-star',
                'color' => '#F59E0B',
                'order' => 3,
            ],
            [
                'name' => 'Job Board',
                'description' => 'Share job opportunities and career advice',
                'icon' => 'fas fa-briefcase',
                'color' => '#8B5CF6',
                'order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            ForumCategory::create($category);
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function createThread()
    {
        $this->validate();

        ForumThread::create([
            'category_id' => $this->threadCategoryId,
            'user_id' => auth()->id(),
            'title' => $this->threadTitle,
            'content' => $this->threadContent,
            'last_activity_at' => now(),
            'last_reply_user_id' => auth()->id(),
        ]);

        $this->reset(['threadTitle', 'threadContent', 'threadCategoryId', 'showCreateThread']);

        session()->flash('message', 'Thread created successfully!');
    }

    public function createCategory()
    {
        $this->validate([
            'categoryName' => 'required|min:3|max:100|unique:forum_categories,name',
            'categoryDescription' => 'nullable|max:500',
            'categoryIcon' => 'required|string',
            'categoryColor' => 'required|string',
        ]);

        ForumCategory::create([
            'name' => $this->categoryName,
            'description' => $this->categoryDescription,
            'icon' => $this->categoryIcon,
            'color' => $this->categoryColor,
            'order' => ForumCategory::max('order') + 1,
        ]);

        $this->reset(['categoryName', 'categoryDescription', 'categoryIcon', 'categoryColor', 'showCreateCategory']);

        session()->flash('message', 'Category created successfully!');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = ForumCategory::active()->withCount('threads')->get();

        $threadsQuery = ForumThread::with(['user', 'category', 'lastReplyUser'])
            ->when($this->selectedCategory, function ($query) {
                return $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_activity_at', 'desc');

        $threads = $threadsQuery->paginate(10);

        $popularThreads = ForumThread::with(['user', 'category'])
            ->popular()
            ->limit(5)
            ->get();

        $recentThreads = ForumThread::with(['user', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.community.partial.forums', [
            'categories' => $categories,
            'threads' => $threads,
            'popularThreads' => $popularThreads,
            'recentThreads' => $recentThreads,
        ]);
    }
}