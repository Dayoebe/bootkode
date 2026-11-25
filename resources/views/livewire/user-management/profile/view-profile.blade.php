<div
    id="profile-panel-overview"
    role="tabpanel"
    aria-labelledby="profile-tab-overview"
    x-show="profileTab === 'overview'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Profile overview</span><h2>Your account at a glance</h2><p>Review the personal and professional details attached to your BootKode account.</p></div>
        <button type="button" class="bk-button bk-button-secondary" wire:click="toggleEditMode"><i class="fas fa-pen" aria-hidden="true"></i> Update details</button>
    </header>

    <div class="bk-profile-card-grid">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-address-card" aria-hidden="true"></i></span><div><h3>Personal details</h3><p>Your private account information</p></div></div>
            <dl class="bk-profile-detail-list">
                <div><dt>Full name</dt><dd>{{ $user->name }}</dd></div>
                <div><dt>Email address</dt><dd>{{ $user->email }}</dd></div>
                <div><dt>Phone number</dt><dd>{{ $user->phone_number ?: 'Not provided' }}</dd></div>
                <div><dt>Date of birth</dt><dd>{{ $user->date_of_birth?->format('M j, Y') ?: 'Not provided' }}</dd></div>
            </dl>
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-briefcase" aria-hidden="true"></i></span><div><h3>Professional profile</h3><p>Background used across your account</p></div></div>
            <dl class="bk-profile-detail-list">
                <div><dt>Occupation</dt><dd>{{ $user->occupation ?: 'Not provided' }}</dd></div>
                <div><dt>Education</dt><dd>{{ $user->education_level ?: 'Not provided' }}</dd></div>
                <div><dt>Location</dt><dd>{{ collect([$user->address_city, $user->address_state, $user->address_country])->filter()->implode(', ') ?: 'Not provided' }}</dd></div>
                <div><dt>Member since</dt><dd>{{ $user->created_at->format('M Y') }}</dd></div>
            </dl>
        </section>
    </div>

    <div class="bk-profile-card-grid mt-5">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-code" aria-hidden="true"></i></span><div><h3>Skills and interests</h3><p>Self-reported areas you are developing</p></div></div>
            @if($user->skills)
                <div class="bk-profile-tags">
                    @foreach(collect(explode(',', $user->skills))->map(fn ($skill) => trim($skill))->filter()->take(20) as $skill)
                        <span>{{ $skill }}</span>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state icon="fas fa-code" title="No skills added" description="Add the technologies and practical areas you are learning." />
            @endif
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-link" aria-hidden="true"></i></span><div><h3>Connected links</h3><p>Web profiles you chose to save</p></div></div>
            @php($connectedLinks = collect($user->social_links ?? [])->filter())
            @if($connectedLinks->isNotEmpty())
                <div class="bk-profile-link-list">
                    @foreach($connectedLinks as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">
                            <span><i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>{{ str($network)->title() }}</span>
                            <small>{{ str($url)->limit(42) }}</small>
                        </a>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state icon="fas fa-link" title="No links connected" description="Add a portfolio, GitHub, LinkedIn, or other profile when you are ready." />
            @endif
        </section>
    </div>
</div>

<div
    id="profile-panel-learning"
    role="tabpanel"
    aria-labelledby="profile-tab-learning"
    x-show="profileTab === 'learning'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Learning record</span><h2>Progress backed by your activity</h2><p>These figures come from your saved course, lesson, resource, and certificate records.</p></div>
        @if($user->hasRole(\App\Models\Core\User::ROLE_STUDENT))
            <a class="bk-button bk-button-secondary" href="{{ route('student.enrolled-courses') }}" wire:navigate><i class="fas fa-graduation-cap" aria-hidden="true"></i> My learning</a>
        @endif
    </header>

    <div class="bk-profile-learning-grid">
        @foreach([
            ['Courses', $learningProgress['total_courses'], 'fas fa-layer-group'],
            ['Completed lessons', $learningProgress['completed_lessons'], 'fas fa-circle-check'],
            ['Downloaded items', $learningProgress['downloaded_content'], 'fas fa-download'],
            ['Weekly learning hours', number_format($learningProgress['average_weekly_progress'], 1), 'fas fa-clock'],
        ] as [$label, $value, $icon])
            <article><span><i class="{{ $icon }}" aria-hidden="true"></i></span><strong>{{ $value }}</strong><small>{{ $label }}</small></article>
        @endforeach
    </div>

    <div class="bk-profile-card-grid mt-5">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-clock-rotate-left" aria-hidden="true"></i></span><div><h3>Recent lesson activity</h3><p>Your five most recently completed lessons</p></div></div>
            @if($recentActivities->isNotEmpty())
                <ol class="bk-profile-timeline">
                    @foreach($recentActivities as $activity)
                        <li><span><i class="fas fa-check" aria-hidden="true"></i></span><div><strong>{{ $activity['title'] }}</strong><small>{{ $activity['course'] }} · {{ $activity['date']?->diffForHumans() }}</small></div></li>
                    @endforeach
                </ol>
            @else
                <x-ui.empty-state icon="fas fa-clock-rotate-left" title="No lesson activity yet" description="Completed lessons will appear here as your learning record grows." />
            @endif
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-certificate" aria-hidden="true"></i></span><div><h3>Credentials</h3><p>Certificate records attached to this account</p></div></div>
            <div class="bk-profile-feature-number"><strong>{{ $learningProgress['certificates'] }}</strong><span>{{ \Illuminate\Support\Str::plural('certificate record', $learningProgress['certificates']) }}</span></div>
            <p class="bk-profile-card-copy">Only recorded certificate requests and issued credentials are counted. This number is not a claim of completion or approval.</p>
            @if($user->hasRole(\App\Models\Core\User::ROLE_STUDENT))
                <a class="bk-profile-inline-link" href="{{ route('student.certificates.index') }}" wire:navigate>Open certificates <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
            @endif
        </section>
    </div>
</div>

<div
    id="profile-panel-saved"
    role="tabpanel"
    aria-labelledby="profile-tab-saved"
    x-show="profileTab === 'saved'"
    x-cloak
>
    <header class="bk-profile-section-header">
        <div><span>Saved for later</span><h2>Your learning shortlist</h2><p>Return to resources and publicly available courses you deliberately saved.</p></div>
    </header>

    <div class="bk-profile-card-grid">
        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-bookmark" aria-hidden="true"></i></span><div><h3>Saved resources</h3><p>{{ $savedResources->count() }} most recent {{ \Illuminate\Support\Str::plural('item', $savedResources->count()) }}</p></div></div>
            @if($savedResources->isNotEmpty())
                <ul class="bk-profile-saved-list">
                    @foreach($savedResources as $resource)
                        <li><span><i class="fas fa-file-lines" aria-hidden="true"></i></span><div><strong>{{ $resource->resourceable?->title ?: 'Saved resource' }}</strong><small>{{ $resource->course?->title ?: 'General resource' }} · saved {{ $resource->created_at->diffForHumans() }}</small></div></li>
                    @endforeach
                </ul>
            @else
                <x-ui.empty-state icon="fas fa-bookmark" title="No saved resources" description="Resources you bookmark in a course will appear here." />
            @endif
        </section>

        <section class="bk-profile-card">
            <div class="bk-profile-card-heading"><span><i class="fas fa-heart" aria-hidden="true"></i></span><div><h3>Course wishlist</h3><p>{{ $wishlist->count() }} available {{ \Illuminate\Support\Str::plural('course', $wishlist->count()) }}</p></div></div>
            @if($wishlist->isNotEmpty())
                <ul class="bk-profile-wishlist">
                    @foreach($wishlist as $item)
                        <li>
                            <div><strong>{{ $item->course->title }}</strong><small>{{ $item->course->category?->name ?: 'Uncategorised' }}</small></div>
                            <div><a href="{{ route('course.view', $item->course) }}" wire:navigate>View</a><button type="button" aria-label="Remove {{ $item->course->title }} from wishlist" wire:click="removeFromWishlist({{ $item->course_id }})"><i class="fas fa-xmark" aria-hidden="true"></i></button></div>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-ui.empty-state icon="fas fa-heart" title="Your wishlist is empty" description="Courses you save from the catalogue will appear here." />
            @endif
        </section>
    </div>
</div>

@include('livewire.user-management.profile.tab.settings-tab')
