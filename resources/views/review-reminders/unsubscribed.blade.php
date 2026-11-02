<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6 px-4 py-10 sm:px-6">
        <x-ui.page-header :title="$confirmed ? 'Course review reminders stopped' : 'Stop course review reminders'" description="Manage reminders for this course." />
        <x-ui.panel>
            @if($confirmed || $reminder->unsubscribed)
                <p role="status" class="text-content">You will receive no more review reminders for {{ $course->title }}.</p>
            @else
                <p class="mb-4 text-content">Stop review reminders for {{ $course->title }}? Your learning records and course access are retained.</p>
                <form method="POST" action="{{ request()->fullUrl() }}">@csrf<x-primary-button>Stop these reminders</x-primary-button></form>
            @endif
            <a href="{{ route('login') }}" class="mt-6 inline-flex min-h-11 items-center font-semibold text-action underline">Sign in to BootKode</a>
        </x-ui.panel>
    </div>
</x-app-layout>
