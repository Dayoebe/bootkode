<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Learning\VideoLibrary as VideoLibraryModel; // Fixed: Use alias to avoid conflict
use App\Models\Learning\Course;

class VideoLibrary extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategory = '';
    public $selectedCourse = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedVideo = null;

    // Form fields
    public $title = '';
    public $description = '';
    public $video_url = '';
    public $video_type = 'youtube';
    public $category = 'tutorial';
    public $course_id = '';
    public $tags = '';
    public $is_public = true;

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'video_url' => 'required|url',
        'video_type' => 'required|in:upload,youtube,vimeo,external',
        'category' => 'required|in:lecture,tutorial,demo,webinar,interview,presentation,other',
        'course_id' => 'nullable|exists:courses,id',
        'tags' => 'nullable|string',
        'is_public' => 'boolean',
    ];

    public function updatingSearch()
    {
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
        $this->resetForm();
    }

    public function openEditModal($videoId)
    {
        $video = VideoLibraryModel::findOrFail($videoId);

        $this->selectedVideo = $video;
        $this->title = $video->title;
        $this->description = $video->description;
        $this->video_url = $video->video_url;
        $this->video_type = $video->video_type;
        $this->category = $video->category;
        $this->course_id = $video->course_id;
        $this->tags = $video->tags;
        $this->is_public = $video->is_public;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function openDeleteModal($videoId)
    {
        $this->selectedVideo = VideoLibraryModel::findOrFail($videoId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedVideo = null;
    }

    public function save()
    {
        $this->validate();

        try {
            VideoLibraryModel::create([
                'title' => $this->title,
                'description' => $this->description,
                'video_url' => $this->video_url,
                'video_type' => $this->video_type,
                'category' => $this->category,
                'course_id' => $this->course_id ?: null,
                'uploaded_by' => auth()->id(),
                'tags' => $this->tags,
                'is_public' => $this->is_public,
            ]);

            $this->closeCreateModal();
            session()->flash('message', 'Video added to library successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to add video: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->selectedVideo->update([
                'title' => $this->title,
                'description' => $this->description,
                'video_url' => $this->video_url,
                'video_type' => $this->video_type,
                'category' => $this->category,
                'course_id' => $this->course_id ?: null,
                'tags' => $this->tags,
                'is_public' => $this->is_public,
            ]);

            $this->closeEditModal();
            session()->flash('message', 'Video updated successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update video: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->selectedVideo->delete();
            $this->closeDeleteModal();
            session()->flash('message', 'Video deleted successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete video: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->video_url = '';
        $this->video_type = 'youtube';
        $this->category = 'tutorial';
        $this->course_id = '';
        $this->tags = '';
        $this->is_public = true;
        $this->selectedVideo = null;
    }

    public function render()
    {
        // Fixed: Use the model alias instead of the component class
        $videos = VideoLibraryModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('tags', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('category', $this->selectedCategory);
            })
            ->when($this->selectedCourse, function ($query) {
                $query->where('course_id', $this->selectedCourse);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->with(['uploader', 'course'])
            ->paginate(12);

        $courses = Course::select('id', 'title')->orderBy('title')->get();

        $categories = VideoLibraryModel::CATEGORIES ?? [
            'lecture' => 'Lecture',
            'tutorial' => 'Tutorial',
            'demo' => 'Demo',
            'webinar' => 'Webinar',
            'interview' => 'Interview',
            'presentation' => 'Presentation',
            'other' => 'Other'
        ];

        $videoTypes = VideoLibraryModel::VIDEO_TYPES ?? [
            'upload' => 'Uploaded Video',
            'youtube' => 'YouTube',
            'vimeo' => 'Vimeo',
            'external' => 'External Link'
        ];

        return view('livewire.content.partial.video-library', compact('videos', 'courses', 'categories', 'videoTypes'));
    }
}