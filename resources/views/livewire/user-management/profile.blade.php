<div
    class="bk-profile"
    x-data="{ profileTab: @js($activeTab) }"
>
    <section class="bk-profile-hero" aria-labelledby="profile-page-title" data-profile-surface="modern">
        <div class="bk-profile-hero-decoration" aria-hidden="true"><span></span><span></span></div>

        <div class="bk-profile-identity">
            <div class="bk-profile-avatar" aria-hidden="true">
                @if($user->profile_picture)
                    <img src="{{ $user->profile_picture }}" alt="">
                @else
                    <span>{{ str($user->name)->substr(0, 1)->upper() }}</span>
                @endif
                <span class="bk-profile-avatar-status" title="Active account"></span>
            </div>

            <div class="min-w-0">
                <p class="bk-profile-eyebrow"><i class="fas fa-id-card" aria-hidden="true"></i> My BootKode account</p>
                <h1 id="profile-page-title" class="bk-profile-title">{{ $user->name }}</h1>
                <div class="bk-profile-badges" aria-label="Account status">
                    <span><i class="fas fa-user-tag" aria-hidden="true"></i>{{ str($user->role)->replace('_', ' ')->title() }}</span>
                    <span class="{{ $user->hasVerifiedEmail() ? 'is-positive' : 'is-warning' }}">
                        <i class="fas {{ $user->hasVerifiedEmail() ? 'fa-circle-check' : 'fa-circle-exclamation' }}" aria-hidden="true"></i>
                        {{ $user->hasVerifiedEmail() ? 'Email verified' : 'Email verification needed' }}
                    </span>
                    <span><i class="fas fa-shield-halved" aria-hidden="true"></i>{{ str($profileVisibility)->title() }} profile</span>
                </div>
                <p class="bk-profile-intro">{{ $user->bio ?: 'Add a short introduction so your account reflects the skills and work you are building.' }}</p>
            </div>
        </div>

        <aside class="bk-profile-completion" aria-label="Profile completeness">
            <div class="bk-profile-completion-heading">
                <span><i class="fas fa-sparkles" aria-hidden="true"></i></span>
                <div>
                    <p>Profile readiness</p>
                    <strong>{{ $profileCompleteness }}% complete</strong>
                </div>
            </div>
            <div class="bk-profile-progress" role="progressbar" aria-label="Profile completeness" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $profileCompleteness }}">
                <span style="width: {{ $profileCompleteness }}%"></span>
            </div>
            <p>{{ $profileCompleteness === 100 ? 'Your core profile fields are complete.' : 'Add missing contact, background, skills, photo, or link details to strengthen your account.' }}</p>
            <div class="bk-profile-hero-actions">
                @if($isEditing)
                    <button type="button" class="bk-button bk-button-secondary" wire:click="toggleEditMode">
                        <i class="fas fa-xmark" aria-hidden="true"></i> Cancel editing
                    </button>
                    <button type="button" class="bk-button bk-button-primary" wire:click="updateProfile" wire:loading.attr="disabled" wire:target="updateProfile,profile_picture">
                        <i class="fas fa-floppy-disk" aria-hidden="true"></i>
                        <span wire:loading.remove wire:target="updateProfile">Save profile</span>
                        <span wire:loading wire:target="updateProfile">Saving…</span>
                    </button>
                @else
                    <button type="button" class="bk-button bk-button-primary" wire:click="toggleEditMode">
                        <i class="fas fa-pen" aria-hidden="true"></i> Edit profile
                    </button>
                    <a class="bk-button bk-button-secondary" href="{{ route('privacy.settings') }}" wire:navigate>
                        <i class="fas fa-lock" aria-hidden="true"></i> Privacy controls
                    </a>
                @endif
            </div>
        </aside>
    </section>

    <section class="bk-profile-snapshot" aria-label="Account snapshot">
        <article><span class="bk-profile-snapshot-icon"><i class="fas fa-layer-group" aria-hidden="true"></i></span><div><strong>{{ $learningProgress['total_courses'] }}</strong><span>Enrolled courses</span></div></article>
        <article><span class="bk-profile-snapshot-icon"><i class="fas fa-circle-check" aria-hidden="true"></i></span><div><strong>{{ $learningProgress['completed_lessons'] }}</strong><span>Lessons completed</span></div></article>
        <article><span class="bk-profile-snapshot-icon"><i class="fas fa-bookmark" aria-hidden="true"></i></span><div><strong>{{ $learningProgress['saved_resources'] }}</strong><span>Saved resources</span></div></article>
        <article><span class="bk-profile-snapshot-icon"><i class="fas fa-certificate" aria-hidden="true"></i></span><div><strong>{{ $learningProgress['certificates'] }}</strong><span>Certificate records</span></div></article>
    </section>

    <div class="bk-profile-workspace">
        <nav class="bk-profile-nav" aria-label="Profile sections" role="tablist" aria-orientation="vertical">
            <div class="bk-profile-nav-heading">
                <span>{{ $isEditing ? 'Edit profile' : 'Account centre' }}</span>
                <small>{{ $isEditing ? 'Update one section at a time' : 'Profile, learning and preferences' }}</small>
            </div>

            @php
                $tabs = $isEditing
                    ? [
                        'identity' => ['Identity', 'Name, contact and bio', 'fa-address-card'],
                        'details' => ['Background', 'Location, education and skills', 'fa-briefcase'],
                        'links' => ['Links & photo', 'Public links and profile image', 'fa-link'],
                    ]
                    : [
                        'overview' => ['Overview', 'Personal and professional details', 'fa-user'],
                        'learning' => ['Learning', 'Progress and recent activity', 'fa-chart-line'],
                        'saved' => ['Saved', 'Resources and course wishlist', 'fa-bookmark'],
                        'account' => ['Account', 'Appearance, privacy and security', 'fa-shield-halved'],
                    ];
            @endphp

            @foreach($tabs as $key => [$label, $description, $icon])
                <button
                    type="button"
                    role="tab"
                    id="profile-tab-{{ $key }}"
                    aria-controls="profile-panel-{{ $key }}"
                    :aria-selected="(profileTab === '{{ $key }}').toString()"
                    :tabindex="profileTab === '{{ $key }}' ? 0 : -1"
                    :class="{ 'is-active': profileTab === '{{ $key }}' }"
                    @click="profileTab = '{{ $key }}'; $wire.switchTab('{{ $key }}')"
                    @keydown.arrow-down.prevent="($el.nextElementSibling?.matches('[role=tab]') ? $el.nextElementSibling : $el.parentElement.querySelector('[role=tab]'))?.focus()"
                    @keydown.arrow-right.prevent="($el.nextElementSibling?.matches('[role=tab]') ? $el.nextElementSibling : $el.parentElement.querySelector('[role=tab]'))?.focus()"
                    @keydown.arrow-up.prevent="($el.previousElementSibling?.matches('[role=tab]') ? $el.previousElementSibling : $el.parentElement.querySelector('[role=tab]:last-of-type'))?.focus()"
                    @keydown.arrow-left.prevent="($el.previousElementSibling?.matches('[role=tab]') ? $el.previousElementSibling : $el.parentElement.querySelector('[role=tab]:last-of-type'))?.focus()"
                    @keydown.home.prevent="$el.parentElement.querySelector('[role=tab]')?.focus()"
                    @keydown.end.prevent="$el.parentElement.querySelector('[role=tab]:last-of-type')?.focus()"
                >
                    <span><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                    <span><strong>{{ $label }}</strong><small>{{ $description }}</small></span>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
            @endforeach
        </nav>

        <div class="bk-profile-content">
            @if($isEditing)
                @include('livewire.user-management.profile.edit-profile')
            @else
                @include('livewire.user-management.profile.view-profile')
            @endif
        </div>
    </div>
</div>
