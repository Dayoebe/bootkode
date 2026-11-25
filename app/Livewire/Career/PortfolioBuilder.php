<?php

namespace App\Livewire\Career;

use App\Models\Content\Portfolio;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard', ['title' => 'Project portfolio', 'description' => 'Manage private projects and choose what to publish', 'icon' => 'fas fa-folder-open', 'active' => 'portfolio.builder'])]
class PortfolioBuilder extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public $portfolios = [];
    public string $title = '';
    public string $description = '';
    public string $project_url = '';
    public string $source_code_url = '';
    public string $technologies = '';
    public string $category = '';
    public string $status = 'completed';
    public string $visibility = 'private';
    public string $start_date = '';
    public string $end_date = '';
    public string $client_name = '';
    public $image;
    public array $additional_images = [];

    #[Locked]
    public ?int $editingProjectId = null;

    public bool $showForm = false;
    public string $viewMode = 'grid';
    public string $sortBy = 'sort_order';
    public string $sortDirection = 'asc';
    public string $filterCategory = '';
    public string $filterStatus = '';
    public string $searchTerm = '';
    public bool $showPreview = false;

    #[Locked]
    public ?Portfolio $previewProject = null;

    public array $bulkSelected = [];
    public bool $showBulkActions = false;
    public int $totalProjects = 0;
    public int $completedProjects = 0;
    public int $inProgressProjects = 0;
    public int $inPlanningProjects = 0;
    public int $inOnHoldProjects = 0;
    public int $totalViews = 0;

    public array $categories = [
        'web-development' => 'Web Development',
        'mobile-app' => 'Mobile App',
        'ui-ux-design' => 'UI/UX Design',
        'graphic-design' => 'Graphic Design',
        'branding' => 'Branding',
        'photography' => 'Photography',
        'video-editing' => 'Video Editing',
        'data-analysis' => 'Data Analysis',
        'machine-learning' => 'Machine Learning',
        'other' => 'Other',
    ];

    public array $techSuggestions = [
        'React', 'Vue.js', 'Angular', 'Laravel', 'Django', 'Node.js', 'Python',
        'JavaScript', 'TypeScript', 'PHP', 'MySQL', 'PostgreSQL', 'MongoDB',
        'AWS', 'Docker', 'Kubernetes', 'Figma', 'Adobe XD', 'Photoshop',
        'Illustrator', 'After Effects', 'Premiere Pro', 'Blender',
    ];

    public function boot(): void
    {
        abort_unless(
            Auth::check()
                && Auth::user()->isStudent()
                && Auth::user()->is_active
                && Auth::user()->deactivated_at === null,
            403
        );
    }

    public function mount(): void
    {
        $this->authorize('create', Portfolio::class);
        $this->loadPortfolios();
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'project_url' => ['nullable', 'url:http,https', 'max:255'],
            'source_code_url' => ['nullable', 'url:http,https', 'max:2048'],
            'technologies' => ['required', 'string', 'max:1000'],
            'category' => ['required', 'in:'.implode(',', array_keys($this->categories))],
            'status' => ['required', 'in:completed,in-progress,planning,on-hold'],
            'visibility' => ['required', 'in:private,public'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'additional_images.*' => ['nullable', 'image', 'max:1024'],
        ];
    }

    protected function messages(): array
    {
        return [
            'description.min' => 'Describe the project in at least 10 characters.',
            'project_url.url' => 'The demo link must be a safe HTTP or HTTPS URL.',
            'source_code_url.url' => 'The source-code link must be a safe HTTP or HTTPS URL.',
            'technologies.required' => 'List at least one technology or tool.',
            'visibility.in' => 'Choose private or published visibility.',
        ];
    }

    public function updated(string $propertyName): void
    {
        if (in_array($propertyName, ['sortBy', 'sortDirection', 'filterCategory', 'filterStatus', 'searchTerm'], true)) {
            $this->loadPortfolios();
        }
    }

    public function loadPortfolios(): void
    {
        $allowedSorts = ['sort_order', 'created_at', 'title', 'status'];
        $sortBy = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'sort_order';
        $direction = $this->sortDirection === 'desc' ? 'desc' : 'asc';
        $this->sortBy = $sortBy;
        $this->sortDirection = $direction;

        $query = Auth::user()->portfolios();

        if ($this->searchTerm !== '') {
            $search = mb_substr($this->searchTerm, 0, 100);
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('technologies', 'like', '%'.$search.'%');
            });
        }

        if (array_key_exists($this->filterCategory, $this->categories)) {
            $query->where('category', $this->filterCategory);
        }

        if (in_array($this->filterStatus, ['completed', 'in-progress', 'planning', 'on-hold'], true)) {
            $query->where('status', $this->filterStatus);
        }

        $this->portfolios = $query->orderBy($sortBy, $direction)->orderBy('id')->get();
        $this->calculateStatistics();
    }

    public function calculateStatistics(): void
    {
        $summary = Auth::user()->portfolios()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(status = 'completed') as completed")
            ->selectRaw("SUM(status = 'in-progress') as in_progress")
            ->selectRaw("SUM(status = 'planning') as planning")
            ->selectRaw("SUM(status = 'on-hold') as on_hold")
            ->selectRaw('COALESCE(SUM(views_count), 0) as views')
            ->first();

        $this->totalProjects = (int) ($summary?->total ?? 0);
        $this->completedProjects = (int) ($summary?->completed ?? 0);
        $this->inProgressProjects = (int) ($summary?->in_progress ?? 0);
        $this->inPlanningProjects = (int) ($summary?->planning ?? 0);
        $this->inOnHoldProjects = (int) ($summary?->on_hold ?? 0);
        $this->totalViews = (int) ($summary?->views ?? 0);
    }

    public function saveProject(): void
    {
        $this->title = trim($this->title);
        $this->description = trim($this->description);
        $this->project_url = trim($this->project_url);
        $this->source_code_url = trim($this->source_code_url);
        $this->technologies = trim($this->technologies);
        $this->client_name = trim($this->client_name);

        $validated = $this->validate();
        $data = [
            'title' => trim($validated['title']),
            'description' => trim($validated['description']),
            'project_url' => $this->nullableTrimmed($validated['project_url'] ?? null),
            'source_code_url' => $this->nullableTrimmed($validated['source_code_url'] ?? null),
            'technologies' => trim($validated['technologies']),
            'category' => $validated['category'],
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'is_public' => $validated['visibility'] === 'public',
            'start_date' => $validated['start_date'] ?: null,
            'end_date' => $validated['end_date'] ?: null,
            'client_name' => $this->nullableTrimmed($validated['client_name'] ?? null),
        ];

        $storedPaths = [];

        try {
            $project = $this->editingProjectId !== null
                ? $this->ownedProject($this->editingProjectId, 'update')
                : null;

            if ($project === null) {
                $this->authorize('create', Portfolio::class);
            } elseif ($data['visibility'] === 'public') {
                $this->authorize('publish', $project);
            }

            if ($this->image) {
                $data['image_path'] = $this->image->store('portfolio/main', 'public');
                $storedPaths[] = $data['image_path'];
            }
            if ($this->additional_images !== []) {
                $data['additional_images'] = collect($this->additional_images)
                    ->map(fn ($image) => $image->store('portfolio/gallery', 'public'))
                    ->all();
                $storedPaths = [...$storedPaths, ...$data['additional_images']];
            }

            if ($project !== null) {
                $oldImage = $project->image_path;
                $oldAdditionalImages = $project->additional_images ?? [];
                $project->update($data);
                if (isset($data['image_path'])) {
                    $this->deleteImage($oldImage);
                }
                if (isset($data['additional_images'])) {
                    $this->deleteAdditionalImages($oldAdditionalImages);
                }
                session()->flash('message', 'Project updated.');
            } else {
                $data['slug'] = $this->uniqueSlug($data['title']);
                $data['sort_order'] = ((int) Auth::user()->portfolios()->max('sort_order')) + 1;
                Auth::user()->portfolios()->create($data);
                session()->flash(
                    'message',
                    $data['visibility'] === 'public'
                        ? 'Project created and published to your showcase.'
                        : 'Project created privately. Publish it only when you are ready to share it.'
                );
            }

            $this->resetForm();
            $this->loadPortfolios();
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->deleteAdditionalImages($storedPaths);
            report($exception);
            $this->addError('project', 'The project could not be saved. Your existing projects were not changed.');
        }
    }

    public function editProject(int $projectId): void
    {
        $project = $this->ownedProject($projectId, 'update');
        $this->editingProjectId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description;
        $this->project_url = $project->project_url ?? '';
        $this->source_code_url = $project->source_code_url ?? '';
        $this->technologies = $project->technologies;
        $this->category = $project->category;
        $this->status = $project->status;
        $this->visibility = $project->visibility ?? 'private';
        $this->start_date = $project->start_date?->format('Y-m-d') ?? '';
        $this->end_date = $project->end_date?->format('Y-m-d') ?? '';
        $this->client_name = $project->client_name ?? '';
        $this->showForm = true;
        $this->showPreview = false;
    }

    public function deleteProject(int $projectId): void
    {
        $project = $this->ownedProject($projectId, 'delete');
        $image = $project->image_path;
        $additionalImages = $project->additional_images ?? [];
        $project->delete();
        $this->deleteImage($image);
        $this->deleteAdditionalImages($additionalImages);
        $this->loadPortfolios();
        session()->flash('message', 'Project deleted.');
    }

    public function moveProject(int $projectId, string $direction): void
    {
        abort_unless(in_array($direction, ['up', 'down'], true), 422);
        $project = $this->ownedProject($projectId, 'reorder');

        DB::transaction(function () use ($project, $direction): void {
            $projects = Portfolio::query()
                ->where('user_id', Auth::id())
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $projects->each(fn (Portfolio $item, int $index) => $item->update(['sort_order' => $index + 1]));
            $index = $projects->search(fn (Portfolio $item) => $item->is($project));
            $otherIndex = $direction === 'up' ? $index - 1 : $index + 1;

            if ($index === false || ! $projects->has($otherIndex)) {
                return;
            }

            $current = $projects->get($index);
            $other = $projects->get($otherIndex);
            $currentOrder = $current->sort_order;
            $current->update(['sort_order' => $other->sort_order]);
            $other->update(['sort_order' => $currentOrder]);
        });

        $this->sortBy = 'sort_order';
        $this->sortDirection = 'asc';
        $this->loadPortfolios();
        session()->flash('message', 'Project order updated.');
    }

    public function duplicateProject(int $projectId): void
    {
        $project = $this->ownedProject($projectId, 'view');
        $this->authorize('create', Portfolio::class);
        $copy = $project->replicate(['views_count', 'likes_count']);
        $copy->title = Str::limit($project->title.' (Copy)', 255, '');
        $copy->slug = $this->uniqueSlug($copy->title);
        $copy->visibility = 'private';
        $copy->is_public = false;
        $copy->sort_order = ((int) Auth::user()->portfolios()->max('sort_order')) + 1;
        $copy->image_path = null;
        $copy->additional_images = null;
        $copy->views_count = 0;
        $copy->likes_count = 0;
        $copy->save();
        $this->loadPortfolios();
        session()->flash('message', 'A private copy was created without shared media files.');
    }

    public function previewProject(int $projectId): void
    {
        $this->previewProject = $this->ownedProject($projectId, 'view');
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->previewProject = null;
    }

    public function toggleBulkSelect(int $projectId): void
    {
        $this->ownedProject($projectId, 'view');
        $this->bulkSelected = in_array($projectId, $this->bulkSelected, true)
            ? array_values(array_diff($this->bulkSelected, [$projectId]))
            : [...$this->bulkSelected, $projectId];
        $this->showBulkActions = $this->bulkSelected !== [];
    }

    public function selectAllVisible(): void
    {
        $visibleIds = collect($this->portfolios)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->bulkSelected = Auth::user()->portfolios()->whereKey($visibleIds)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $this->showBulkActions = $this->bulkSelected !== [];
    }

    public function clearBulkSelection(): void
    {
        $this->bulkSelected = [];
        $this->showBulkActions = false;
    }

    public function bulkDelete(): void
    {
        $projectIds = collect($this->bulkSelected)
            ->filter(fn ($id) => is_int($id) || (is_string($id) && ctype_digit($id)))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(100)
            ->all();
        $projects = Auth::user()->portfolios()->whereKey($projectIds)->get();
        $count = $projects->count();
        foreach ($projects as $project) {
            $this->authorize('delete', $project);
            $image = $project->image_path;
            $additionalImages = $project->additional_images ?? [];
            $project->delete();
            $this->deleteImage($image);
            $this->deleteAdditionalImages($additionalImages);
        }
        $this->clearBulkSelection();
        $this->loadPortfolios();
        session()->flash('message', $count.' projects deleted.');
    }

    public function exportPortfolio()
    {
        $this->authorize('create', Portfolio::class);
        $projects = Auth::user()->portfolios()->orderBy('sort_order')->orderBy('id')->get();
        if ($projects->isEmpty()) {
            $this->addError('project', 'There are no projects to export.');

            return null;
        }

        return response()->streamDownload(function () use ($projects): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Title', 'Category', 'Status', 'Visibility', 'Technologies', 'Demo URL', 'Source URL']);
            foreach ($projects as $project) {
                fputcsv($handle, [
                    $this->csvCell($project->title), $project->category_label, $project->status_label,
                    $project->visibility, $this->csvCell($project->technologies),
                    $project->project_url, $project->source_code_url,
                ]);
            }
            fclose($handle);
        }, 'portfolio_projects_'.now()->format('Y_m_d_His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function resetForm(): void
    {
        $this->reset([
            'title', 'description', 'project_url', 'source_code_url', 'technologies',
            'category', 'start_date', 'end_date', 'client_name', 'image',
            'additional_images', 'editingProjectId',
        ]);
        $this->status = 'completed';
        $this->visibility = 'private';
        $this->showForm = false;
        $this->resetValidation();
    }

    private function ownedProject(int $projectId, string $ability): Portfolio
    {
        $project = Portfolio::query()->findOrFail($projectId);
        $this->authorize($ability, $project);

        return $project;
    }

    private function uniqueSlug(string $title): string
    {
        do {
            $slug = Str::slug($title).'-'.Str::lower(Str::random(8));
        } while (Portfolio::query()->where('slug', $slug)->exists());

        return $slug;
    }

    private function nullableTrimmed(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function deleteAdditionalImages(array $images): void
    {
        foreach ($images as $path) {
            $this->deleteImage(is_string($path) ? $path : null);
        }
    }

    private function csvCell(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^\s*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }

    public function render()
    {
        return view('livewire.career.portfolio-builder');
    }
}
