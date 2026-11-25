<div class="space-y-6">
    <x-ui.page-header
        title="Privacy settings"
        description="These controls map to privacy rules currently enforced by BootKode. Account security, data export, session logout, deactivation, and deletion remain in the account centre."
    >
        <x-slot:actions>
            <a href="{{ route('profile.view') }}" class="bk-button bk-button-secondary">Open account centre</a>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel title="Public profile visibility">
        <form wire:submit="saveProfilePrivacy" class="space-y-4">
            <fieldset class="space-y-3">
                <legend class="text-sm font-semibold text-content">Who can open your public profile?</legend>
                @foreach (['private' => 'Private - only you can view it', 'public' => 'Public - anyone with the profile link can view it'] as $value => $label)
                    <label class="flex min-h-11 items-center gap-3 rounded-lg border border-line px-4 py-3 text-sm text-content">
                        <input type="radio" wire:model="profile_visibility" value="{{ $value }}" class="text-action focus:ring-action" />
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </fieldset>
            @error('profile_visibility') <x-input-error :messages="$message" /> @enderror
            <x-primary-button wire:loading.attr="disabled" wire:target="saveProfilePrivacy">Save profile visibility</x-primary-button>
        </form>
    </x-ui.panel>

    <x-ui.panel title="Course messages">
        <form wire:submit="saveCommunicationPrivacy" class="space-y-4">
            <label class="flex min-h-11 items-start gap-3 rounded-lg border border-line px-4 py-3 text-sm text-content">
                <input type="checkbox" wire:model="allow_course_messages" class="mt-1 rounded border-line text-action focus:ring-action" />
                <span><strong class="block">Allow course messages</strong><span class="text-content-muted">Enrolled learners can start a course conversation with you when you are the assigned instructor.</span></span>
            </label>
            <x-primary-button wire:loading.attr="disabled" wire:target="saveCommunicationPrivacy">Save message preference</x-primary-button>
        </form>
    </x-ui.panel>

    <x-ui.panel title="Optional analytics">
        <form wire:submit="saveDataPrivacy" class="space-y-4">
            <p class="text-sm text-content-muted">Authenticated marketing analytics loads only when all required consent controls below are enabled. They are off by default.</p>
            @foreach ([
                'data_collection_consent' => ['Data collection consent', 'Permit optional collection for the authenticated marketing analytics integration.'],
                'analytics_tracking' => ['Analytics tracking', 'Permit the optional analytics integration on your authenticated pages.'],
                'marketing_analytics' => ['Marketing analytics', 'Permit marketing-focused analytics processing.'],
                'third_party_integrations' => ['Third-party integration', 'Permit the configured external analytics provider to load.'],
            ] as $property => [$label, $description])
                <label class="flex min-h-11 items-start gap-3 rounded-lg border border-line px-4 py-3 text-sm text-content">
                    <input type="checkbox" wire:model="{{ $property }}" class="mt-1 rounded border-line text-action focus:ring-action" />
                    <span><strong class="block">{{ $label }}</strong><span class="text-content-muted">{{ $description }}</span></span>
                </label>
                @error($property) <x-input-error :messages="$message" /> @enderror
            @endforeach
            <x-primary-button wire:loading.attr="disabled" wire:target="saveDataPrivacy">Save analytics choices</x-primary-button>
        </form>
    </x-ui.panel>
</div>
