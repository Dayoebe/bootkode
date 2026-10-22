<?php

namespace App\Livewire\Pages;

use App\Models\Learning\CourseCategory;
use App\Services\PublicCatalogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app', ['title' => 'Courses'])]
class Courses extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    public function updated($property): void
    {
        if (in_array($property, ['search', 'category'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'category');
        $this->resetPage();
    }

    public function render(PublicCatalogService $catalog)
    {
        return view('livewire.pages.courses', [
            'courses' => $catalog->courses()
                ->when($this->search !== '', fn ($q) => $q->where('title', 'like', '%'.mb_substr($this->search, 0, 120).'%'))
                ->when($this->category !== '', fn ($q) => $q->where('category_id', $this->category))
                ->latest('id')->paginate(12),
            'categories' => CourseCategory::whereHas('courses', fn ($q) => $q->publiclyAvailable())->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
