<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MobileBottomNav extends Component
{
    /** Shortcuts follow the user's primary workspace; the drawer exposes all allowed tools. */
    public function items(?\App\Models\Core\User $user): array
    {
        $work = match (true) {
            $user?->hasAnyRole(['super_admin', 'academy_admin']) => ['People', 'fas fa-users', 'all-users', ['all-users', 'user-management', 'roles-permissions']],
            $user?->hasRole('instructor') => ['Teaching', 'fas fa-chalkboard-user', 'my-course', ['my-course', 'course-builder', 'create.course']],
            $user?->hasRole('mentor') => ['Mentoring', 'fas fa-user-group', 'mentorship.dashboard', ['mentorship.*']],
            $user?->hasRole('content_editor') => ['Content', 'fas fa-pen-to-square', 'my-course', ['my-course', 'course-quality.control']],
            $user?->hasRole('affiliate_ambassador') => ['Referrals', 'fas fa-handshake', 'affiliate.dashboard', ['affiliate.*']],
            default => ['Learning', 'fas fa-book-open', 'student.enrolled-courses', ['student.enrolled-courses', 'course.view', 'student.course-catalog']],
        };

        return [
            ['label' => 'Home', 'icon' => 'fas fa-house', 'route' => 'dashboard', 'active' => ['dashboard', 'student.dashboard', 'instructor.dashboard', 'mentor.dashboard', 'content_editor.dashboard', 'affiliate_ambassador.dashboard', 'academy_admin.dashboard', 'super_admin.dashboard']],
            ['label' => $work[0], 'icon' => $work[1], 'route' => $work[2], 'active' => $work[3]],
            ['label' => 'Messages', 'icon' => 'fas fa-comments', 'route' => 'messages.index', 'active' => ['messages.*']],
            ['label' => 'Account', 'icon' => 'fas fa-user', 'route' => 'profile.view', 'active' => ['profile.*', 'settings']],
        ];
    }

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('livewire.dashboard.layout.mobile-bottom-nav');
    }
}
