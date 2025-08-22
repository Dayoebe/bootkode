<?php

namespace App\Livewire\SystemManagement;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Notifications',
    'description' => 'View and manage your notifications',
    'icon' => 'fas fa-bell',
    'active' => 'notifications'
])]
class Notifications extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = 'all'; // all, read, unread

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => 'all']
    ];

    public $unreadCount = 0;

    public function mount()
    {
        $this->updateUnreadCount();
    }

    public function getNotificationsProperty()
    {
        return Auth::user()->notifications()
            ->when($this->search, function ($query) {
                $query->where('data->message', 'like', '%' . $this->search . '%');
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('data->type', $this->typeFilter);
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('read_at', $this->statusFilter === 'read' ? '!=' : '=', null);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        $notification->user->logCustomActivity('Marked notification as read', ['notification_id' => $notificationId]);
        $this->dispatch('notify', 'Notification marked as read!', 'success');
        $this->updateUnreadCount();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        Auth::user()->logCustomActivity('Marked all notifications as read');
        $this->dispatch('notify', 'All notifications marked as read!', 'success');
        $this->updateUnreadCount();
    }

    public function delete($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->delete();
        $notification->user->logCustomActivity('Deleted notification', ['notification_id' => $notificationId]);
        $this->dispatch('notify', 'Notification deleted!', 'success');
        $this->updateUnreadCount();
    }

    public function getNotificationTypes()
    {
        return Auth::user()->notifications()
            ->select('data->type as type')
            ->distinct()
            ->pluck('type')
            ->toArray();
    }

    public function updated($property)
    {
        $this->resetPage();
    }

    private function updateUnreadCount()
    {
        $this->unreadCount = Auth::user()->unreadNotifications()->count();
    }

    public function pollNotifications()
    {
        $newUnreadCount = Auth::user()->unreadNotifications()->count();
        if ($newUnreadCount > $this->unreadCount) {
            $this->dispatch('new-notification');
            $this->updateUnreadCount();
        }
    }

    public function render()
    {
        return view('livewire.system-management.notifications', [
            'notifications' => $this->notifications,
            'notificationTypes' => $this->getNotificationTypes(),
        ]);
    }
}