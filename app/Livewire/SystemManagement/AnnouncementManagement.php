<?php

namespace App\Livewire\SystemManagement;

use App\Models\Announcement;
use App\Models\Course;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Announcement Management', 'description' => 'Manage platform announcements', 'icon' => 'fas fa-bullhorn', 'active' => 'announcement.management'])]
class AnnouncementManagement extends Component
{
    use WithPagination;

    public $title = '';
    public $content = '';
    public $course_id = null;
    public $status = 'draft';
    public $editId = null;
    public $search = '';
    public $statusFilter = 'all';

    protected $rules = [
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string', 'max:2000'],
        'course_id' => ['nullable', 'exists:courses,id'],
        'status' => ['required', 'in:draft,published'],
    ];

    public function saveAnnouncement()
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'course_id' => $this->course_id,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
        ];

        if ($this->editId) {
            $announcement = Announcement::findOrFail($this->editId);
            $announcement->update($data);
            $message = 'Announcement updated successfully!';
        } else {
            $announcement = Announcement::create($data);
            $message = 'Announcement created successfully!';
        }

        if ($this->status === 'published') {
            $users = $this->course_id
                ? Course::find($this->course_id)->enrolledUsers
                : User::all();
            foreach ($users as $user) {
                $user->notify(new NewAnnouncementNotification($announcement));
            }
            $this->dispatchTo('notifications', 'notify', [
                'message' => 'New announcement: ' . $announcement->title,
                'type' => 'success'
            ]);
        }

        Auth::user()->logCustomActivity($this->editId ? 'Updated announcement' : 'Created announcement', ['announcement_id' => $announcement->id]);
        $this->dispatch('notify', $message, 'success');
        $this->reset(['title', 'content', 'course_id', 'status', 'editId']);
    }

    public function editAnnouncement($announcementId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $announcement = Announcement::findOrFail($announcementId);
        $this->title = $announcement->title;
        $this->content = $announcement->content;
        $this->course_id = $announcement->course_id;
        $this->status = $announcement->status;
        $this->editId = $announcement->id;
    }

    public function deleteAnnouncement($announcementId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $announcement = Announcement::findOrFail($announcementId);
        $announcement->delete();
        Auth::user()->logCustomActivity('Deleted announcement', ['announcement_id' => $announcementId]);
        $this->dispatch('notify', 'Announcement deleted successfully!', 'success');
    }

    public function render()
    {
        $announcements = Announcement::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
        })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['user', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.system-management.announcement-management', [
            'announcements' => $announcements,
            'courses' => Course::where('is_published', true)->where('is_approved', true)->get(),
        ]);
    }
}