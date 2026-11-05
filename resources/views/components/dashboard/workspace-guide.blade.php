@props(['role'])

@php
    $workspaces = [
        'student' => [
            'eyebrow' => 'Learner workspace',
            'title' => 'Everything you need to learn, build and grow',
            'description' => 'Move from your next lesson to practical evidence, support and career preparation without hunting through the menu.',
            'items' => [
                ['label' => 'My learning', 'title' => 'Continue your courses', 'description' => 'Open enrolled courses, resume lessons and review completion recorded against your account.', 'route' => 'student.enrolled-courses', 'action' => 'Open my learning', 'icon' => 'fas fa-book-open'],
                ['label' => 'Progress', 'title' => 'Understand your activity', 'description' => 'Review recorded study activity, assessments, completed lessons and category performance.', 'route' => 'learning.analytics', 'action' => 'View learning analytics', 'icon' => 'fas fa-chart-line'],
                ['label' => 'Build proof', 'title' => 'Shape your portfolio', 'description' => 'Manage private projects and explicitly choose which work appears on your public profile.', 'route' => 'portfolio.show', 'action' => 'Manage portfolio', 'icon' => 'fas fa-folder-open'],
                ['label' => 'Career', 'title' => 'Prepare for opportunities', 'description' => 'Explore available opportunities and manage applications, evidence and career preparation.', 'route' => 'career.centre', 'action' => 'Open career centre', 'icon' => 'fas fa-briefcase'],
                ['label' => 'Guidance', 'title' => 'Use mentorship support', 'description' => 'Review mentoring relationships, sessions and the mentorship tools available to your account.', 'route' => 'mentorship.hub', 'action' => 'Open mentorship', 'icon' => 'fas fa-user-group'],
                ['label' => 'Saved', 'title' => 'Return to saved resources', 'description' => 'Find courses and learning resources you saved for later from one focused list.', 'route' => 'student.saved-resources', 'action' => 'View saved resources', 'icon' => 'fas fa-bookmark'],
            ],
            'steps' => [
                ['title' => 'Learn', 'description' => 'Continue the next recorded lesson or assessment in your active learning.'],
                ['title' => 'Build', 'description' => 'Turn completed work into a project or another piece of verifiable evidence.'],
                ['title' => 'Grow', 'description' => 'Use feedback, mentorship and career tools to decide your next practical step.'],
            ],
        ],
        'instructor' => [
            'eyebrow' => 'Instructor workspace',
            'title' => 'Plan, publish and improve your teaching',
            'description' => 'Keep course production, learner signals, earnings and supporting resources within one operating view.',
            'items' => [
                ['label' => 'Course studio', 'title' => 'Manage every course', 'description' => 'Review your drafts, published courses, curriculum structure and recorded learner activity.', 'route' => 'my-course', 'action' => 'Open course workspace', 'icon' => 'fas fa-layer-group'],
                ['label' => 'Create', 'title' => 'Start a new course', 'description' => 'Create a course draft and build its lessons, materials and assessments through the existing authoring flow.', 'route' => 'create.course', 'action' => 'Create a course', 'icon' => 'fas fa-circle-plus'],
                ['label' => 'Earnings', 'title' => 'Review recorded earnings', 'description' => 'See the earnings and transaction information recorded for your instructor account.', 'route' => 'instructor.earnings', 'action' => 'View earnings', 'icon' => 'fas fa-wallet'],
                ['label' => 'Marketplace', 'title' => 'Manage your listings', 'description' => 'Review the learning resources or products you have listed in the marketplace.', 'route' => 'marketplace.seller.listings', 'action' => 'Open seller listings', 'icon' => 'fas fa-store'],
            ],
            'steps' => [
                ['title' => 'Prepare', 'description' => 'Keep curriculum, materials and assessments complete before requesting publication.'],
                ['title' => 'Teach', 'description' => 'Use recorded enrollments, progress and reviews to understand learner activity.'],
                ['title' => 'Improve', 'description' => 'Return to course content and supporting resources when the evidence shows a gap.'],
            ],
        ],
        'mentor' => [
            'eyebrow' => 'Mentor workspace',
            'title' => 'Guide each assigned learner with clear context',
            'description' => 'Move from relationship context to sessions, career practice and recorded follow-up without losing ownership boundaries.',
            'items' => [
                ['label' => 'Relationships', 'title' => 'Review assigned mentees', 'description' => 'Open the mentoring dashboard for the active and completed relationships assigned to you.', 'route' => 'mentorship.dashboard', 'action' => 'View mentees', 'icon' => 'fas fa-people-arrows-left-right'],
                ['label' => 'Sessions', 'title' => 'Plan mentoring sessions', 'description' => 'Review scheduled and completed sessions connected to your mentoring relationships.', 'route' => 'mentorship.sessions', 'action' => 'Manage sessions', 'icon' => 'fas fa-calendar-check'],
                ['label' => 'Practice', 'title' => 'Support interview preparation', 'description' => 'Use the existing mock-interview workspace to review recorded interview practice.', 'route' => 'admin.interview', 'action' => 'Open interviews', 'icon' => 'fas fa-microphone-lines'],
                ['label' => 'Mentorship', 'title' => 'Open the mentoring hub', 'description' => 'Return to the wider mentorship tools and guidance available for your role.', 'route' => 'mentorship.hub', 'action' => 'Open mentorship hub', 'icon' => 'fas fa-compass'],
            ],
            'steps' => [
                ['title' => 'Review', 'description' => 'Start with the learner relationship, recorded goals and recent activity available to you.'],
                ['title' => 'Meet', 'description' => 'Use scheduled sessions to focus the conversation and keep follow-up connected.'],
                ['title' => 'Follow through', 'description' => 'Return to recorded outcomes, support needs and the next agreed action.'],
            ],
        ],
        'content_editor' => [
            'eyebrow' => 'Editorial workspace',
            'title' => 'Publish useful content with a reliable review rhythm',
            'description' => 'Keep drafting, moderation, publishing configuration and recorded content performance close together.',
            'items' => [
                ['label' => 'Library', 'title' => 'Manage posts', 'description' => 'Review drafts, scheduled work and published posts in the existing editorial list.', 'route' => 'admin.blog.posts.index', 'action' => 'Open content library', 'icon' => 'fas fa-newspaper'],
                ['label' => 'Create', 'title' => 'Write a new post', 'description' => 'Start a new article and use the established authoring and review workflow.', 'route' => 'admin.blog.posts.create', 'action' => 'Create a post', 'icon' => 'fas fa-pen-to-square'],
                ['label' => 'Moderation', 'title' => 'Review comments', 'description' => 'Open the comment queue to inspect and moderate recorded reader responses.', 'route' => 'admin.blog.comments.index', 'action' => 'Review comments', 'icon' => 'fas fa-comments'],
                ['label' => 'Configuration', 'title' => 'Check publishing settings', 'description' => 'Review the blog settings that govern the existing editorial surface.', 'route' => 'admin.blog.settings', 'action' => 'Open blog settings', 'icon' => 'fas fa-sliders'],
            ],
            'steps' => [
                ['title' => 'Draft', 'description' => 'Start with a clear learner need and keep the main point visible in the structure.'],
                ['title' => 'Review', 'description' => 'Check status, metadata, links and the editorial queue before publishing.'],
                ['title' => 'Maintain', 'description' => 'Use recorded views, engagement and moderation needs to guide follow-up work.'],
            ],
        ],
        'affiliate_ambassador' => [
            'eyebrow' => 'Ambassador workspace',
            'title' => 'Share responsibly and track recorded referrals',
            'description' => 'Use approved links and copy, then separate confirmed referral and commission data from unavailable traffic analytics.',
            'items' => [
                ['label' => 'Share', 'title' => 'Use marketing tools', 'description' => 'Copy your authenticated referral link, code and the approved sharing text available to your account.', 'route' => 'affiliate.tools', 'action' => 'Open marketing tools', 'icon' => 'fas fa-share-nodes'],
                ['label' => 'Analytics', 'title' => 'Review recorded performance', 'description' => 'Inspect the referral analytics recorded by BootKode without relying on unsupported click estimates.', 'route' => 'affiliate.analytics', 'action' => 'View analytics', 'icon' => 'fas fa-chart-column'],
                ['label' => 'Commissions', 'title' => 'Check commission records', 'description' => 'Review recorded commission transactions and their current statuses.', 'route' => 'affiliate.commissions', 'action' => 'View commissions', 'icon' => 'fas fa-coins'],
                ['label' => 'Reports', 'title' => 'Open detailed reports', 'description' => 'Use the reporting workspace for the referral and earnings data currently stored for your account.', 'route' => 'affiliate.reports', 'action' => 'Open reports', 'icon' => 'fas fa-file-lines'],
            ],
            'steps' => [
                ['title' => 'Choose', 'description' => 'Use the authenticated link or code shown in your own marketing tools.'],
                ['title' => 'Share', 'description' => 'Keep claims factual and direct people to the relevant BootKode learning surface.'],
                ['title' => 'Review', 'description' => 'Use recorded referrals and commissions; click analytics remains unavailable until connected.'],
            ],
        ],
        'academy_admin' => [
            'eyebrow' => 'Academy operations',
            'title' => 'Keep people, learning and support moving',
            'description' => 'Reach the academy’s core operating queues quickly while retaining the detailed metrics and charts below.',
            'items' => [
                ['label' => 'People', 'title' => 'Manage users', 'description' => 'Review learner, instructor and staff accounts through the authorised user workspace.', 'route' => 'all-users', 'action' => 'Open user directory', 'icon' => 'fas fa-users-gear'],
                ['label' => 'Courses', 'title' => 'Review the course catalogue', 'description' => 'Inspect stored courses, their current statuses and the existing management controls.', 'route' => 'all-course', 'action' => 'Manage courses', 'icon' => 'fas fa-book'],
                ['label' => 'Approvals', 'title' => 'Process course reviews', 'description' => 'Use the established approval queue for course publication decisions.', 'route' => 'course-approvals', 'action' => 'Open approval queue', 'icon' => 'fas fa-clipboard-check'],
                ['label' => 'Credentials', 'title' => 'Manage certificates', 'description' => 'Review certificate requests and existing certificate records in their dedicated workflow.', 'route' => 'admin.certificates.manage', 'action' => 'Open certificates', 'icon' => 'fas fa-certificate'],
                ['label' => 'Support', 'title' => 'Respond to support needs', 'description' => 'Open the support queue for tickets recorded by learners and other platform users.', 'route' => 'support.tickets', 'action' => 'Open support queue', 'icon' => 'fas fa-headset'],
                ['label' => 'Learning', 'title' => 'Review learning analytics', 'description' => 'Use recorded progress and completion data to understand academy learning activity.', 'route' => 'learning.analytics', 'action' => 'View learning analytics', 'icon' => 'fas fa-chart-line'],
            ],
            'steps' => [
                ['title' => 'Monitor', 'description' => 'Start with pending approvals, support demand and the recorded academy signals below.'],
                ['title' => 'Review', 'description' => 'Use the established course, certificate and user workflows for each decision.'],
                ['title' => 'Support', 'description' => 'Resolve recorded blockers and return to analytics to confirm the current state.'],
            ],
        ],
        'super_admin' => [
            'eyebrow' => 'Platform control',
            'title' => 'Move from platform signals to the right control surface',
            'description' => 'Use the dedicated operational routes for people, courses, credentials, activity and configuration instead of acting from summary cards.',
            'items' => [
                ['label' => 'People', 'title' => 'Manage platform users', 'description' => 'Review roles and account records through the authorised user-management workspace.', 'route' => 'user-management', 'action' => 'Open user management', 'icon' => 'fas fa-users-gear'],
                ['label' => 'Courses', 'title' => 'Review all courses', 'description' => 'Inspect the stored course catalogue and current publishing states.', 'route' => 'all-course', 'action' => 'Open course management', 'icon' => 'fas fa-book-open'],
                ['label' => 'Approvals', 'title' => 'Process course approvals', 'description' => 'Use the established review queue for course approval and publication decisions.', 'route' => 'course-approvals', 'action' => 'Review course approvals', 'icon' => 'fas fa-list-check'],
                ['label' => 'Credentials', 'title' => 'Manage certificates', 'description' => 'Review certificate requests and existing credential records without bypassing their workflow.', 'route' => 'admin.certificates.manage', 'action' => 'Open certificates', 'icon' => 'fas fa-award'],
                ['label' => 'Audit', 'title' => 'Review user activity', 'description' => 'Open the recorded activity surface when an operational signal needs more context.', 'route' => 'user.activity', 'action' => 'View activity', 'icon' => 'fas fa-clock-rotate-left'],
                ['label' => 'Configuration', 'title' => 'Open platform settings', 'description' => 'Review existing platform configuration through the dedicated settings workspace.', 'route' => 'settings', 'action' => 'Open settings', 'icon' => 'fas fa-gears'],
            ],
            'steps' => [
                ['title' => 'Verify', 'description' => 'Confirm that a summary signal is recorded and current before acting on it.'],
                ['title' => 'Route', 'description' => 'Open the dedicated authorised workflow for the affected record or service.'],
                ['title' => 'Resolve', 'description' => 'Complete the action there, then return to the dashboard for the updated state.'],
            ],
        ],
    ];

    $workspace = $workspaces[$role] ?? null;
    $sectionId = 'dashboard-workspace-guide-'.str($role)->slug();
@endphp

@if($workspace)
    <section class="bk-workspace-guide" aria-labelledby="{{ $sectionId }}" data-dashboard-workspace="{{ $role }}">
        <div class="bk-workspace-guide-header">
            <div>
                <p class="bk-eyebrow">{{ $workspace['eyebrow'] }}</p>
                <h2 id="{{ $sectionId }}" class="bk-workspace-guide-title">{{ $workspace['title'] }}</h2>
                <p class="bk-workspace-guide-description">{{ $workspace['description'] }}</p>
            </div>
            <span class="bk-workspace-guide-count">{{ count($workspace['items']) }} work areas</span>
        </div>

        <div class="bk-workspace-guide-grid">
            @foreach($workspace['items'] as $item)
                <article class="bk-workspace-guide-card">
                    <span class="bk-workspace-guide-icon" aria-hidden="true"><i class="{{ $item['icon'] }}"></i></span>
                    <div class="min-w-0">
                        <p class="bk-workspace-guide-label">{{ $item['label'] }}</p>
                        <h3 class="bk-workspace-guide-card-title">{{ $item['title'] }}</h3>
                        <p class="bk-workspace-guide-card-description">{{ $item['description'] }}</p>
                        <a href="{{ route($item['route']) }}" class="bk-workspace-guide-link" wire:navigate>
                            {{ $item['action'] }} <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="bk-workspace-guide-workflow">
            <div>
                <p class="bk-workspace-guide-label">Suggested workflow</p>
                <h3 class="bk-workspace-guide-workflow-title">A clear way to use this dashboard</h3>
            </div>
            <ol class="bk-workspace-guide-steps">
                @foreach($workspace['steps'] as $step)
                    <li class="bk-workspace-guide-step">
                        <span class="bk-workspace-guide-step-number" aria-hidden="true">{{ $loop->iteration }}</span>
                        <span><strong>{{ $step['title'] }}</strong><span>{{ $step['description'] }}</span></span>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endif
