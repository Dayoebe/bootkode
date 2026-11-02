<x-app-layout>
    <div class="mx-auto max-w-2xl space-y-6 px-4 py-10 sm:px-6">
        <x-ui.page-header :title="$confirmed ? 'Course review reminders stopped' : 'Stop all course review reminders'" description="This applies to your account only." />
        <x-ui.panel>
            @if($confirmed)
                <p role="status" class="text-content">Course review reminders are stopped for your current and future courses. Existing learning records are retained.</p>
            @else
                <p class="mb-4 text-content">Confirm that you want to stop all course review reminders.</p>
                <form method="POST" action="{{ route('review-reminders.unsubscribe-all.confirm') }}">@csrf<x-primary-button>Stop all review reminders</x-primary-button></form>
            @endif
            <a href="{{ route('notification.preferences') }}" class="mt-6 inline-flex min-h-11 items-center font-semibold text-action underline">Notification preferences</a>
        </x-ui.panel>
    </div>
</x-app-layout>
