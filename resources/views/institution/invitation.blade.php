<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-12">
        <x-ui.page-header title="Institution invitation" description="Review the invitation before accepting it." />
        <x-ui.panel>
            <p class="text-content">{{ $invitation->institution->name }} invited you to join as {{ $invitation->role_name }}.</p>
            <p class="mt-3 text-sm text-content-muted">This membership applies to this institution. It does not grant platform administration permissions.</p>
            <form method="POST" action="{{ route('institution.invitations.confirm', $invitation->token) }}" class="mt-6">
                @csrf
                <x-primary-button>Accept invitation</x-primary-button>
            </form>
        </x-ui.panel>
    </div>
</x-app-layout>
