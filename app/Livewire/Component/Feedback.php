<?php

namespace App\Livewire\Component;

use App\Models\Course;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Feedback',
    'description' => 'Share your feedback and view past submissions',
    'icon' => 'fas fa-comment-dots',
    'active' => 'feedback'
])]
class Feedback extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'submit_feedback';
    public $category = 'general';
    public $course_id = null;
    public $message = '';
    public $rating = 0;
    public $attachment; // New for file upload
    public $search = '';
    public $statusFilter = 'all';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    protected function rules()
    {
        return [
            'category' => ['required', 'in:general,course,platform'],
            'course_id' => ['nullable', 'required_if:category,course', 'exists:courses,id'],
            'message' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,png,pdf', 'max:2048'], // 2MB max
        ];
    }

    public function submitFeedback()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'course_id' => $this->category === 'course' ? $this->course_id : null,
            'category' => $this->category,
            'message' => $this->message,
            'rating' => $this->rating,
            'status' => 'open',
        ];

        if ($this->attachment) {
            $data['attachment'] = $this->attachment->store('feedback_attachments', 'public');
        }

        $feedback = \App\Models\Feedback::create($data);

        Auth::user()->logCustomActivity('Submitted feedback: ' . $this->message, ['feedback_id' => $feedback->id]);
        $this->dispatch('notify', 'Feedback submitted successfully!', 'success');
        $this->reset(['category', 'course_id', 'message', 'rating', 'attachment']);
        $this->activeTab = 'my_feedback';
    }

    public function render()
    {
        $feedbacks = \App\Models\Feedback::where('user_id', Auth::id())
            ->when($this->search, function ($query) {
                $query->where('message', 'like', '%' . $this->search . '%')
                      ->orWhereHas('course', function ($q) {
                          $q->where('title', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['course', 'responder'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.component.feedback', [
            'feedbacks' => $feedbacks,
            'courses' => Course::where('is_published', true)->where('is_approved', true)->get(),
        ]);
    }
}