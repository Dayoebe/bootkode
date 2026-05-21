@php
    $statusClasses = [
        'verified' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'ready' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'needs_work' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        'stale' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'not_checked' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="bg-themed-secondary border border-themed-primary rounded-lg p-6 shadow-sm">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide">Course Quality Control</p>
                <h1 class="text-2xl md:text-3xl font-bold text-themed-primary mt-1">Editorial QA before courses go public</h1>
                <p class="text-themed-secondary mt-2 max-w-3xl">Score completeness, scan broken media, check assessment coverage, schedule content reviews, and control public quality labels.</p>
            </div>

            <a href="{{ route('course-approvals') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-themed-tertiary border border-themed-primary text-themed-primary hover:bg-themed-secondary text-sm font-semibold">
                <i class="fas fa-check-circle mr-2"></i> Course Approvals
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
        <button type="button" wire:click="$set('statusFilter', 'all')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-blue-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">All courses</span>
                <i class="fas fa-book text-blue-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($stats['total']) }}</p>
        </button>
        <button type="button" wire:click="$set('statusFilter', 'not_checked')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-gray-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Not checked</span>
                <i class="fas fa-circle-question text-gray-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($stats['not_checked']) }}</p>
        </button>
        <button type="button" wire:click="$set('statusFilter', 'needs_work')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-orange-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Needs work</span>
                <i class="fas fa-triangle-exclamation text-orange-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($stats['needs_work']) }}</p>
        </button>
        <button type="button" wire:click="$set('statusFilter', 'ready')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-green-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Ready</span>
                <i class="fas fa-shield-check text-green-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($stats['ready']) }}</p>
        </button>
        <button type="button" wire:click="$set('statusFilter', 'stale')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-amber-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Review due</span>
                <i class="fas fa-calendar-xmark text-amber-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($stats['stale']) }}</p>
        </button>
    </div>

    <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-themed-secondary mb-1">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-themed-secondary text-xs"></i>
                    <input type="search" wire:model.live.debounce.350ms="search"
                           class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary pl-9 pr-3 py-2 text-sm"
                           placeholder="Course title, description, instructor...">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-themed-secondary mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary px-3 py-2 text-sm">
                    <option value="all">All statuses</option>
                    <option value="pending">Pending approval</option>
                    <option value="not_checked">Not checked</option>
                    <option value="needs_work">Needs work</option>
                    <option value="ready">Ready / verified</option>
                    <option value="stale">Review due</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-themed-secondary mb-1">Sort</label>
                <select wire:model.live="sortBy" class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary px-3 py-2 text-sm">
                    <option value="lowest_score">Lowest score</option>
                    <option value="highest_score">Highest score</option>
                    <option value="review_due">Review due</option>
                    <option value="newest">Newest</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead class="bg-themed-tertiary">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Quality</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Checks</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Review Date</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase text-themed-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-themed-primary">
                    @forelse($courses as $course)
                        @php
                            $summary = $course->quality_summary ?? [];
                            $issues = $course->quality_issues ?? [];
                            $status = $course->quality_status ?: 'not_checked';
                            $score = (int) ($course->quality_score ?? 0);
                        @endphp
                        <tr class="align-top">
                            <td class="px-5 py-4 min-w-[260px]">
                                <div class="flex items-start gap-3">
                                    <div class="h-12 w-16 rounded-lg bg-themed-tertiary border border-themed-primary overflow-hidden flex-shrink-0">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-themed-secondary">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-themed-primary">{{ $course->title }}</p>
                                        <p class="text-sm text-themed-secondary mt-1">{{ $course->instructor?->name ?? 'No instructor' }} / {{ $course->category?->name ?? 'Uncategorized' }}</p>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $course->is_approved ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
                                                {{ $course->is_approved ? 'Approved' : 'Pending' }}
                                            </span>
                                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $course->is_published ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                                {{ $course->is_published ? 'Published' : 'Unpublished' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 min-w-[220px]">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl font-black {{ $score >= 85 ? 'text-green-600' : ($score >= 70 ? 'text-blue-600' : ($score >= 50 ? 'text-orange-600' : 'text-red-600')) }}">{{ $score }}</div>
                                    <div class="flex-1">
                                        <div class="h-2 rounded-full bg-themed-tertiary overflow-hidden">
                                            <div class="h-full rounded-full {{ $score >= 85 ? 'bg-green-500' : ($score >= 70 ? 'bg-blue-500' : ($score >= 50 ? 'bg-orange-500' : 'bg-red-500')) }}" style="width: {{ min(100, $score) }}%"></div>
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $statusClasses[$status] ?? $statusClasses['not_checked'] }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                            @if($course->quality_label_text)
                                                <span class="inline-flex px-2 py-1 rounded text-xs font-semibold {{ $course->quality_label_class }}">{{ $course->quality_label_text }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($course->quality_last_checked_at)
                                    <p class="text-xs text-themed-secondary mt-2">Checked {{ $course->quality_last_checked_at->diffForHumans() }}</p>
                                @else
                                    <p class="text-xs text-themed-secondary mt-2">No QA scan yet</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 min-w-[260px]">
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="rounded-lg bg-themed-tertiary border border-themed-primary p-2">
                                        <span class="block text-themed-secondary">Completeness</span>
                                        <strong class="text-themed-primary">{{ $summary['completeness_percent'] ?? 0 }}%</strong>
                                    </div>
                                    <div class="rounded-lg bg-themed-tertiary border border-themed-primary p-2">
                                        <span class="block text-themed-secondary">Assessment</span>
                                        <strong class="text-themed-primary">{{ $summary['assessment_coverage_percent'] ?? 0 }}%</strong>
                                    </div>
                                    <div class="rounded-lg bg-themed-tertiary border border-themed-primary p-2">
                                        <span class="block text-themed-secondary">Broken media</span>
                                        <strong class="{{ ($summary['broken_media_count'] ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $summary['broken_media_count'] ?? 0 }}</strong>
                                    </div>
                                    <div class="rounded-lg bg-themed-tertiary border border-themed-primary p-2">
                                        <span class="block text-themed-secondary">Lessons</span>
                                        <strong class="text-themed-primary">{{ $summary['lessons_count'] ?? $course->lessons_count }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm min-w-[180px]">
                                @if($course->quality_review_due_at)
                                    <p class="font-semibold {{ $course->quality_review_due_at->isPast() ? 'text-amber-600' : 'text-themed-primary' }}">{{ $course->quality_review_due_at->format('M j, Y') }}</p>
                                    <p class="text-xs text-themed-secondary mt-1">{{ $course->quality_review_due_at->diffForHumans() }}</p>
                                @else
                                    <p class="font-semibold text-orange-600">Not scheduled</p>
                                    <p class="text-xs text-themed-secondary mt-1">Mark reviewed to set due date</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right min-w-[300px]">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <button type="button" wire:click="runQualityCheck({{ $course->id }})" class="px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">
                                        <i class="fas fa-clipboard-check mr-1"></i> Run QA
                                    </button>
                                    <button type="button" wire:click="runQualityCheck({{ $course->id }}, true)" class="px-3 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold">
                                        <i class="fas fa-photo-film mr-1"></i> Check Media
                                    </button>
                                    <button type="button" wire:click="markReviewed({{ $course->id }})" class="px-3 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold">
                                        <i class="fas fa-calendar-check mr-1"></i> Reviewed
                                    </button>
                                    <button type="button" wire:click="togglePublicLabel({{ $course->id }})" class="px-3 py-2 rounded-lg bg-themed-tertiary hover:bg-themed-secondary border border-themed-primary text-themed-primary text-xs font-semibold">
                                        <i class="fas fa-tag mr-1"></i> {{ $course->quality_public_label_enabled ? 'Hide' : 'Show' }}
                                    </button>
                                    @if(!$course->is_approved)
                                        <button type="button" wire:click="approveIfReady({{ $course->id }})" class="px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    @endif
                                    <button type="button" wire:click="toggleDetails({{ $course->id }})" class="px-3 py-2 rounded-lg bg-themed-tertiary hover:bg-themed-secondary border border-themed-primary text-themed-primary text-xs font-semibold">
                                        <i class="fas fa-list-ul mr-1"></i> Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @if($expandedCourseId === $course->id)
                            <tr>
                                <td colspan="5" class="px-5 py-4 bg-themed-tertiary">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
                                            <h3 class="font-bold text-themed-primary mb-3">QA Issues</h3>
                                            @forelse($issues as $issue)
                                                <div class="flex items-start gap-3 py-2 border-b border-themed-primary last:border-b-0">
                                                    <span class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-full text-xs {{ ($issue['severity'] ?? '') === 'critical' ? 'bg-red-100 text-red-700' : (($issue['severity'] ?? '') === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                                        <i class="fas fa-{{ ($issue['severity'] ?? '') === 'critical' ? 'xmark' : 'info' }}"></i>
                                                    </span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-themed-primary">{{ ucfirst(str_replace('_', ' ', $issue['code'] ?? 'issue')) }}</p>
                                                        <p class="text-sm text-themed-secondary">{{ $issue['message'] ?? '' }}</p>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-themed-secondary">No issues recorded. Run QA if this course has not been checked recently.</p>
                                            @endforelse
                                        </div>

                                        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
                                            <h3 class="font-bold text-themed-primary mb-3">Latest Media Results</h3>
                                            @php $mediaResults = $course->latestQualityCheck?->media_results ?? []; @endphp
                                            @forelse(array_slice($mediaResults, 0, 8) as $media)
                                                <div class="py-2 border-b border-themed-primary last:border-b-0">
                                                    <div class="flex items-center justify-between gap-3">
                                                        <p class="text-sm font-semibold text-themed-primary">{{ $media['location'] ?? 'Media' }}</p>
                                                        <span class="text-xs font-semibold {{ ($media['status'] ?? '') === 'broken' ? 'text-red-600' : (($media['status'] ?? '') === 'unchecked' ? 'text-amber-600' : 'text-green-600') }}">{{ ucfirst($media['status'] ?? 'unknown') }}</span>
                                                    </div>
                                                    <p class="text-xs text-themed-secondary break-all mt-1">{{ \Illuminate\Support\Str::limit($media['value'] ?? '', 120) }}</p>
                                                    <p class="text-xs text-themed-secondary mt-1">{{ $media['detail'] ?? '' }}</p>
                                                </div>
                                            @empty
                                                <p class="text-sm text-themed-secondary">No media results yet.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-themed-secondary">
                                <i class="fas fa-clipboard-check text-3xl text-green-500 mb-3"></i>
                                <p>No courses match this quality filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
            <div class="px-5 py-4 border-t border-themed-primary">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>
