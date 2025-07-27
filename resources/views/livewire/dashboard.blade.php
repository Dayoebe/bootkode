<div>
    @if (auth()->user()->hasRole('super_admin'))
        <livewire:dashboard.super-admin-dashboard />
    @elseif (auth()->user()->hasRole('academy_admin'))
        <livewire:dashboard.academy-admin-dashboard />
    @elseif (auth()->user()->hasRole('instructor'))
        <livewire:dashboard.instructor-dashboard />
    @elseif (auth()->user()->hasRole('mentor'))
        <livewire:dashboard.mentor-dashboard />
    @elseif (auth()->user()->hasRole('content_editor'))
        <livewire:dashboard.content-editor-dashboard />
    @elseif (auth()->user()->hasRole('affiliate_ambassador'))
        <livewire:dashboard.affiliate-ambassador-dashboard />
    @elseif (auth()->user()->hasRole('student'))
        <livewire:dashboard.student-dashboard />
    @endif
</div>