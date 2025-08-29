<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Intervention\Image\Facades\Image;


#[Layout('layouts.dashboard', ['title' => 'Portfolio Builder', 'description' => 'Create and showcase your professional portfolio', 'icon' => 'fas fa-palette', 'active' => 'portfolio.builder'])]

class PortfolioBuilder extends Component
{
    use WithFileUploads;

    // Portfolio data
    public $portfolios = [];
    public $title = '';
    public $description = '';
    public $project_url = '';
    public $technologies = '';
    public $category = '';
    public $status = 'completed';
    public $start_date = '';
    public $end_date = '';
    public $client_name = '';
    public $image; // Temporary image file
    public $additional_images = []; // For multiple images
    public $editingProjectId = null;

    // UI State
    public $showForm = false;
    public $viewMode = 'grid'; // grid, list, masonry
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $filterCategory = '';
    public $filterStatus = '';
    public $searchTerm = '';
    public $showPreview = false;
    public $previewProject = null;
    public $bulkSelected = [];
    public $showBulkActions = false;

    // Portfolio statistics
    public $totalProjects = 0;
    public $completedProjects = 0;
    public $inProgressProjects = 0;
    public $inPlanningProjects = 0;
    public $inOnHoldProjects = 0;

    public $totalViews = 0;

    // Categories and technologies
    public $categories = [
        'web-development' => 'Web Development',
        'mobile-app' => 'Mobile App',
        'ui-ux-design' => 'UI/UX Design',
        'graphic-design' => 'Graphic Design',
        'branding' => 'Branding',
        'photography' => 'Photography',
        'video-editing' => 'Video Editing',
        'data-analysis' => 'Data Analysis',
        'machine-learning' => 'Machine Learning',
        'other' => 'Other'
    ];

    public $techSuggestions = [
        'React',
        'Vue.js',
        'Angular',
        'Laravel',
        'Django',
        'Node.js',
        'Python',
        'JavaScript',
        'TypeScript',
        'PHP',
        'MySQL',
        'PostgreSQL',
        'MongoDB',
        'AWS',
        'Docker',
        'Kubernetes',
        'Figma',
        'Adobe XD',
        'Photoshop',
        'Illustrator',
        'After Effects',
        'Premiere Pro',
        'Blender'
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:10',
        'project_url' => 'nullable|url',
        'technologies' => 'required|string',
        'category' => 'required|string',
        'status' => 'required|in:completed,in-progress,planning,on-hold',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'client_name' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:2048', // 2MB Max
        'additional_images.*' => 'nullable|image|max:1024',
    ];

    protected $messages = [
        'title.required' => 'Project title is required.',
        'description.min' => 'Description must be at least 10 characters.',
        'technologies.required' => 'Please specify the technologies used.',
        'category.required' => 'Please select a project category.',
    ];

    public function mount()
    {
        $this->loadPortfolios();
        $this->calculateStatistics();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['sortBy', 'sortDirection', 'filterCategory', 'filterStatus', 'searchTerm'])) {
            $this->loadPortfolios();
        }
    }


    public function loadPortfolios()
    {
        $query = Auth::user()->portfolios();

        // Apply search
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('technologies', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply filters
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $this->portfolios = $query->get();
        $this->calculateStatistics();
    }

    public function calculateStatistics()
    {
        $user = Auth::user();
        $this->totalProjects = $user->portfolios()->count();
        $this->completedProjects = $user->portfolios()->where('status', 'completed')->count();
        $this->inProgressProjects = $user->portfolios()->where('status', 'in-progress')->count();
        $this->inPlanningProjects = $user->portfolios()->where('status', 'planning')->count();
        $this->inOnHoldProjects = $user->portfolios()->where('status', 'on-hold')->count();

   
        $this->totalViews = $user->portfolios()->sum('views_count') ?? 0;
    }

    public function saveProject()
    {
        $this->validate();

        try {
            // Convert empty date strings to null
            $startDate = !empty($this->start_date) ? $this->start_date : null;
            $endDate = !empty($this->end_date) ? $this->end_date : null;

            $projectData = [
                'title' => $this->title,
                'description' => $this->description,
                'project_url' => $this->project_url,
                'technologies' => $this->technologies,
                'category' => $this->category,
                'status' => $this->status,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'client_name' => $this->client_name,
                'slug' => Str::slug($this->title . '-' . Str::random(6)),
            ];

            // Handle main image upload with optimization
            if ($this->image) {
                $imagePath = $this->optimizeAndStoreImage($this->image, 'portfolio/main');
                $projectData['image_path'] = $imagePath;
            }

            // Handle additional images
            $additionalImagePaths = [];
            if ($this->additional_images) {
                foreach ($this->additional_images as $additionalImage) {
                    $path = $this->optimizeAndStoreImage($additionalImage, 'portfolio/gallery');
                    $additionalImagePaths[] = $path;
                }
                $projectData['additional_images'] = json_encode($additionalImagePaths);
            }

            if ($this->editingProjectId) {
                // Update existing project
                $project = Portfolio::find($this->editingProjectId);
                $oldImagePath = $project->image_path;
                $oldAdditionalImages = json_decode($project->additional_images, true) ?? [];

                $project->update($projectData);

                // Delete old images if new ones are uploaded
                if (isset($projectData['image_path']) && $oldImagePath) {
                    $this->deleteImage($oldImagePath);
                }

                if (isset($projectData['additional_images']) && $oldAdditionalImages) {
                    $this->deleteAdditionalImages($oldAdditionalImages);
                }

                session()->flash('message', 'Project updated successfully! 🎉');
            } else {
                // Create new project
                Auth::user()->portfolios()->create($projectData);
                session()->flash('message', 'Project created successfully! 🚀');
            }

            $this->resetForm();
            $this->loadPortfolios();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save project: ' . $e->getMessage());
            \Log::error('Project save error: ' . $e->getMessage());
        }
    }

    public function deleteProject($projectId)
    {
        $project = Portfolio::find($projectId);
        if ($project && $project->user_id === Auth::id()) {
            // Delete associated images
            $this->deleteImage($project->image_path);
            
            if ($project->additional_images) {
                $additionalImages = json_decode($project->additional_images, true);
                $this->deleteAdditionalImages($additionalImages);
            }

            $project->delete();
            $this->loadPortfolios();
            session()->flash('message', 'Project deleted successfully.');
        }
    }

    protected function optimizeAndStoreImage($image, $directory)
{
    $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
    
    // Store directly in the public directory
    $path = $image->storeAs('public/' . $directory, $filename);
    
    // Remove the 'public/' prefix from the path to return
    $relativePath = str_replace('public/', '', $path);
    
    // Check if Intervention Image is available
    if (class_exists('Intervention\Image\Facades\Image')) {
        try {
            $fullPath = storage_path('app/public/' . $relativePath);
            
            // Create the thumbs directory if it doesn't exist
            $thumbDir = storage_path('app/public/' . $directory . '/thumbs');
            if (!file_exists($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            
            // Create thumbnail (300x300)
            $thumbnail = Image::make($fullPath)->fit(300, 300);
            $thumbnailPath = 'public/' . $directory . '/thumbs/' . $filename;
            $thumbnail->save(storage_path('app/' . $thumbnailPath));

            // Create medium size (800x600)
            $medium = Image::make($fullPath)->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $medium->save($fullPath, 85); // Compress to 85% quality

        } catch (\Exception $e) {
            \Log::error('Image optimization failed: ' . $e->getMessage());
        }
    }

    return $relativePath;
}
    protected function deleteImage($path)
    {
        if ($path) {
            // Delete main image
            Storage::disk('public')->delete($path);
            
            // Delete thumbnail if it exists
            if (class_exists('Intervention\Image\Facades\Image')) {
                $thumbnailPath = str_replace('/main/', '/thumbs/', $path);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }
        }
    }

    protected function deleteAdditionalImages($images)
    {
        if ($images) {
            foreach ($images as $imagePath) {
                $this->deleteImage($imagePath);
            }
        }
    }




































    
    public function editProject($projectId)
    {
        $project = Portfolio::find($projectId);
        if ($project && $project->user_id === Auth::id()) {
            $this->editingProjectId = $project->id;
            $this->title = $project->title;
            $this->description = $project->description;
            $this->project_url = $project->project_url;
            $this->technologies = $project->technologies;
            $this->category = $project->category;
            $this->status = $project->status;
            $this->start_date = $project->start_date ? $project->start_date->format('Y-m-d') : '';
            $this->end_date = $project->end_date ? $project->end_date->format('Y-m-d') : '';
            $this->client_name = $project->client_name;
            $this->showForm = true;
        }
    }




















    
   
    private function createThumbnail($sourceImage, $originalWidth, $originalHeight, $mimeType, $directory, $filename)
    {
        $thumbnailSize = 300;
        $thumbnailImage = imagecreatetruecolor($thumbnailSize, $thumbnailSize);
        
        // Maintain transparency for PNG/GIF/WebP
        if (in_array($mimeType, ['image/png', 'image/gif', 'image/webp'])) {
            imagealphablending($thumbnailImage, false);
            imagesavealpha($thumbnailImage, true);
            $transparent = imagecolorallocatealpha($thumbnailImage, 255, 255, 255, 127);
            imagefill($thumbnailImage, 0, 0, $transparent);
        }
    
        // Calculate crop dimensions for square thumbnail
        $cropSize = min($originalWidth, $originalHeight);
        $cropX = ($originalWidth - $cropSize) / 2;
        $cropY = ($originalHeight - $cropSize) / 2;
    
        imagecopyresampled(
            $thumbnailImage, 
            $sourceImage, 
            0, 0, $cropX, $cropY,
            $thumbnailSize, 
            $thumbnailSize, 
            $cropSize, 
            $cropSize
        );
    
        // Save thumbnail
        $thumbnailPath = storage_path('app/public/' . $directory . '/thumbs/' . $filename);
        $this->saveImageByType($thumbnailImage, $thumbnailPath, $mimeType);
        
        imagedestroy($thumbnailImage);
    }
    
    private function optimizeOriginalImage($sourceImage, $originalWidth, $originalHeight, $mimeType, $fullPath)
    {
        $maxWidth = 800;
        $maxHeight = 600;
        
        // Only resize if image is larger than max dimensions
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return; // No need to resize
        }
        
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);
    
        $mediumImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Maintain transparency for PNG/GIF/WebP
        if (in_array($mimeType, ['image/png', 'image/gif', 'image/webp'])) {
            imagealphablending($mediumImage, false);
            imagesavealpha($mediumImage, true);
            $transparent = imagecolorallocatealpha($mediumImage, 255, 255, 255, 127);
            imagefill($mediumImage, 0, 0, $transparent);
        }
    
        imagecopyresampled(
            $mediumImage, 
            $sourceImage, 
            0, 0, 0, 0,
            $newWidth, 
            $newHeight, 
            $originalWidth, 
            $originalHeight
        );
    
        // Save compressed medium image
        $this->saveImageByType($mediumImage, $fullPath, $mimeType);
        
        imagedestroy($mediumImage);
    }
    
    private function saveImageByType($imageResource, $path, $mimeType)
    {
        match($mimeType) {
            'image/jpeg' => imagejpeg($imageResource, $path, 85),
            'image/png' => imagepng($imageResource, $path, 6),
            'image/gif' => imagegif($imageResource, $path),
            'image/webp' => function_exists('imagewebp') ? imagewebp($imageResource, $path, 85) : false,
            default => false
        };
    }






















 

    public function duplicateProject($projectId)
    {
        $project = Portfolio::find($projectId);
        if ($project && $project->user_id === Auth::id()) {
            $newProject = $project->replicate();
            $newProject->title = $project->title . ' (Copy)';
            $newProject->slug = Str::slug($newProject->title . '-' . Str::random(6));
            $newProject->created_at = now();
            $newProject->save();

            $this->loadPortfolios();
            session()->flash('message', 'Project duplicated successfully!');
        }
    }

    public function previewProject($projectId)
    {
        $this->previewProject = Portfolio::find($projectId);
        $this->showPreview = true;

        // Increment view count
        if ($this->previewProject) {
            $this->previewProject->increment('views_count');
        }
    }

    public function closePreview()
    {
        $this->showPreview = false;
        $this->previewProject = null;
    }

    public function toggleBulkSelect($projectId)
    {
        if (in_array($projectId, $this->bulkSelected)) {
            $this->bulkSelected = array_diff($this->bulkSelected, [$projectId]);
        } else {
            $this->bulkSelected[] = $projectId;
        }

        $this->showBulkActions = count($this->bulkSelected) > 0;
    }

    public function selectAllVisible()
    {
        $this->bulkSelected = $this->portfolios->pluck('id')->toArray();
        $this->showBulkActions = true;
    }

    public function clearBulkSelection()
    {
        $this->bulkSelected = [];
        $this->showBulkActions = false;
    }

    public function bulkDelete()
    {
        Portfolio::whereIn('id', $this->bulkSelected)
            ->where('user_id', Auth::id())
            ->each(function ($project) {
                $this->deleteProject($project->id);
            });

        $this->clearBulkSelection();
        session()->flash('message', count($this->bulkSelected) . ' projects deleted successfully.');
    }

    public function exportPortfolio()
    {
        // This would generate a PDF or export data
        session()->flash('message', 'Portfolio export feature coming soon!');
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'description',
            'project_url',
            'technologies',
            'category',
            'status',
            'start_date',
            'end_date',
            'client_name',
            'image',
            'additional_images',
            'editingProjectId'
        ]);
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.career.portfolio-builder');
    }
}