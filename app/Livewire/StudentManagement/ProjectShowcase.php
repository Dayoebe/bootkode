<?php

namespace App\Livewire\StudentDashboard;

use App\Models\Course;
use App\Models\Project;
use App\Models\ProjectSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.student', ['title' => 'Project Showcase', 'description' => 'View and submit your course projects', 'icon' => 'fas fa-project-diagram', 'active' => 'student.projects'])]
class ProjectShowcase extends Component
{
    use WithFileUploads;

    public $courses;
    public $selectedCourseId;
    public $searchTerm = '';
    public $filterStatus = 'all';
    public $showSubmissionForm = false;
    public $selectedProject = null;

    // Submission form fields
    #[Rule('required|string|min:3|max:255')]
    public $submissionTitle = '';

    #[Rule('nullable|string|max:1000')]
    public $submissionDescription = '';

    #[Rule(['nullable|array|max:5'])]
    #[Rule(['files.*' => 'file|mimes:jpg,png,pdf,zip|max:10240'])] // 10MB max per file
    public $files = [];

    public function mount()
    {
        $this->courses = Course::whereHas('enrollments', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();
    }

    public function getProjectsProperty()
    {
        $query = Project::query()
            ->whereIn('course_id', $this->courses->pluck('id'))
            ->with(['submissions' => function ($q) {
                $q->where('user_id', Auth::id());
            }]);

        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->filterStatus !== 'all') {
            $query->whereHas('submissions', function ($q) {
                $q->where('status', $this->filterStatus);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function openSubmissionForm($projectId)
    {
        $this->selectedProject = Project::findOrFail($projectId);
        $this->submissionTitle = $this->selectedProject->title;
        $this->showSubmissionForm = true;
    }

    public function submitProject()
    {
        $this->validate();

        $filePaths = [];
        foreach ($this->files as $file) {
            $filePaths[] = $file->store('submissions', 'public');
        }

        ProjectSubmission::create([
            'project_id' => $this->selectedProject->id,
            'user_id' => Auth::id(),
            'course_id' => $this->selectedProject->course_id,
            'section_id' => $this->selectedProject->section_id,
            'title' => $this->submissionTitle,
            'description' => $this->submissionDescription,
            'files' => $filePaths,
            'status' => 'submitted',
        ]);

        $this->reset(['submissionTitle', 'submissionDescription', 'files', 'showSubmissionForm', 'selectedProject']);
        $this->dispatch('notify', 'Project submitted successfully!', 'success');
    }

    public function toggleFeatured($submissionId)
    {
        if (Auth::user()->hasRole('instructor')) {
            $submission = ProjectSubmission::findOrFail($submissionId);
            $submission->update(['is_featured' => !$submission->is_featured]);
            $this->dispatch('notify', 'Featured status updated!', 'success');
        }
    }

    public function render()
    {
        return view('livewire.student-management.project-showcase', [
            'projects' => $this->projects,
        ]);
    }
}