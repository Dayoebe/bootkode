<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-content dark:text-white">Cohort Management</h2>
            <p class="text-sm text-content-muted dark:text-content-muted">Assign learners to cohorts, attach courses, track completion, and export reports.</p>
        </div>
        <button wire:click="openCreateModal"
                class="inline-flex items-center rounded-md bg-action px-4 py-2 text-sm font-medium text-action-foreground transition-colors hover:bg-action">
            <i class="fas fa-plus mr-2" aria-hidden="true"></i>
            New Cohort
        </button>
    </div>

    <div class="rounded-lg border border-line bg-surface p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div>
                <label class="mb-1 block text-sm font-medium text-content dark:text-gray-300">Search</label>
                <input aria-label="Cohort or institution" type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cohort or institution"
                       class="w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-content dark:text-gray-300">Institution</label>
                <select aria-label="Institution filter" wire:model.live="institutionFilter" class="w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="all">All institutions</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-content dark:text-gray-300">Status</label>
                <select aria-label="Status filter" wire:model.live="statusFilter" class="w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="all">All statuses</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-content dark:text-gray-300">Per page</label>
                <select aria-label="Per page" wire:model.live="perPage" class="w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-line bg-surface shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <x-ui.table label="Cohort Management records"><table class="min-w-full divide-y divide-line dark:divide-gray-700">
                <thead class="bg-surface-muted dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted dark:text-content-muted">Cohort</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted dark:text-content-muted">Members</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted dark:text-content-muted">Courses</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted dark:text-content-muted">Completion</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted dark:text-content-muted">Dates</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-content-muted dark:text-content-muted">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line bg-surface dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($cohorts as $cohort)
                        @php $stats = $cohort->getCompletionStats(); @endphp
                        <tr class="hover:bg-surface-muted dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-content dark:text-white">{{ $cohort->name }}</div>
                                <div class="text-sm text-content-muted dark:text-content-muted">{{ $cohort->institution->name }}</div>
                                <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cohort->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-content dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $cohort->status_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-content dark:text-white">
                                {{ number_format($cohort->members_count) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-content dark:text-white">
                                {{ number_format($cohort->courses_count) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-content dark:text-white">{{ $stats['completion_rate'] }}%</span>
                                    <span class="text-xs text-content-muted dark:text-content-muted">{{ $stats['completed'] }}/{{ $stats['expected'] }}</span>
                                </div>
                                <div class="mt-1 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div class="h-2 rounded-full bg-action dark:bg-action" style="width: {{ min(100, $stats['completion_rate']) }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-content-muted dark:text-content-muted">{{ $stats['average_progress'] }}% average progress</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-content-muted dark:text-content-muted">
                                <div>{{ $cohort->starts_at?->format('M d, Y') ?? 'No start' }}</div>
                                <div>{{ $cohort->ends_at?->format('M d, Y') ?? 'No end' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button wire:click="openManageModal({{ $cohort->id }})" class="text-brand hover:text-brand dark:text-brand" title="Manage assignments">
                                        <i class="fas fa-layer-group" aria-hidden="true"></i>
                                    </button>
                                    <button wire:click="openReportModal({{ $cohort->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400" title="View report">
                                        <i class="fas fa-chart-line" aria-hidden="true"></i>
                                    </button>
                                    <button wire:click="exportCohortReport({{ $cohort->id }})" class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400" title="Export report">
                                        <i class="fas fa-download" aria-hidden="true"></i>
                                    </button>
                                    <button wire:click="openEditModal({{ $cohort->id }})" class="text-brand hover:text-brand dark:text-brand" title="Edit cohort">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </button>
                                    @if($cohort->status === 'active')
                                        <button wire:click="archiveCohort({{ $cohort->id }})" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400" title="Archive">
                                            <i class="fas fa-archive" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <button wire:click="activateCohort({{ $cohort->id }})" class="text-brand hover:text-brand dark:text-brand" title="Activate">
                                            <i class="fas fa-play" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-layer-group mb-4 text-4xl text-gray-300 dark:text-content-muted" aria-hidden="true"></i>
                                <h3 class="text-lg font-medium text-content dark:text-white">No cohorts yet</h3>
                                <p class="mt-1 text-sm text-content-muted dark:text-content-muted">Create a cohort to assign courses to a group of students.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></x-ui.table>
        </div>

        @if($cohorts->hasPages())
            <div class="border-t border-line px-4 py-3 dark:border-gray-700">
                {{ $cohorts->links() }}
            </div>
        @endif
    </div>

    @if($showCohortModal)
        <div class="fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50 dark:bg-black dark:bg-opacity-70" wire:click="closeModals" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModals()" role="dialog" aria-modal="true" aria-label="Cohort Management dialog" tabindex="-1">
            <div class="relative top-16 mx-auto w-11/12 rounded-md border border-line bg-surface p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:w-2/3 lg:w-1/2" wire:click.stop>
                <div class="flex items-center justify-between border-b border-line pb-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-content dark:text-white">{{ $editingCohortId ? 'Edit Cohort' : 'New Cohort' }}</h3>
                    <button aria-label="Close dialog" wire:click="closeModals" class="text-content-muted hover:text-content-muted dark:hover:text-gray-300"><i class="fas fa-times text-xl" aria-hidden="true"></i></button>
                </div>

                <form wire:submit.prevent="saveCohort" class="mt-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300">Institution</label>
                        <select aria-label="Institution id" wire:model.live="form.institution_id" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <option value="">Select institution</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                            @endforeach
                        </select>
                        @error('form.institution_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300">Cohort name</label>
                        <input aria-label="Name" type="text" wire:model="form.name" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('form.name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300">Description</label>
                        <textarea aria-label="Description" wire:model="form.description" rows="3" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Start date</label>
                            <input aria-label="Starts at" type="date" wire:model="form.starts_at" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">End date</label>
                            <input aria-label="Ends at" type="date" wire:model="form.ends_at" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Status</label>
                            <select aria-label="Status" wire:model="form.status" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 border-t border-line pt-6 dark:border-gray-700">
                        <button type="button" wire:click="closeModals" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-content hover:bg-gray-300 dark:bg-gray-600 dark:text-white">Cancel</button>
                        <button type="submit" class="rounded-md bg-action px-4 py-2 text-sm font-medium text-action-foreground hover:bg-action">Save Cohort</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showManageModal && $selectedCohort)
        <div class="fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50 dark:bg-black dark:bg-opacity-70" wire:click="closeModals" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModals()" role="dialog" aria-modal="true" aria-label="Cohort Management dialog" tabindex="-1">
            <div class="relative top-10 mx-auto w-11/12 rounded-md border border-line bg-surface p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:w-4/5" wire:click.stop>
                <div class="flex items-center justify-between border-b border-line pb-4 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-semibold text-content dark:text-white">Manage {{ $selectedCohort->name }}</h3>
                        <p class="text-sm text-content-muted dark:text-content-muted">{{ $selectedCohort->institution->name }}</p>
                    </div>
                    <button aria-label="Close dialog" wire:click="closeModals" class="text-content-muted hover:text-content-muted dark:hover:text-gray-300"><i class="fas fa-times text-xl" aria-hidden="true"></i></button>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <h4 class="mb-3 font-medium text-content dark:text-white">Members</h4>
                        <div class="max-h-80 overflow-y-auto rounded-md border border-line p-3 dark:border-gray-700">
                            @forelse($availableMembers as $member)
                                <label class="flex items-center justify-between gap-3 border-b border-line py-2 last:border-0 dark:border-gray-700">
                                    <span>
                                        <span class="block text-sm font-medium text-content dark:text-white">{{ $member->user?->name ?? 'Unknown user' }}</span>
                                        <span class="text-xs text-content-muted dark:text-content-muted">{{ $member->user?->email }} · {{ $member->role_name }} · {{ $member->status_name }}</span>
                                    </span>
                                    <input type="checkbox" wire:model="selectedMembers" value="{{ $member->id }}" class="rounded border-line text-brand">
                                </label>
                            @empty
                                <p class="text-sm text-content-muted dark:text-content-muted">No institution members are available yet.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-3 font-medium text-content dark:text-white">Courses</h4>
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-content dark:text-gray-300">Due date for assigned courses</label>
                            <input aria-label="Assignment due date" type="date" wire:model="assignmentDueDate" class="mt-1 w-full rounded-md border-line dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="max-h-72 overflow-y-auto rounded-md border border-line p-3 dark:border-gray-700">
                            @forelse($availableCourses as $course)
                                <label class="flex items-center justify-between gap-3 border-b border-line py-2 last:border-0 dark:border-gray-700">
                                    <span>
                                        <span class="block text-sm font-medium text-content dark:text-white">{{ $course->title }}</span>
                                        <span class="text-xs text-content-muted dark:text-content-muted">{{ ucfirst($course->difficulty_level ?? 'general') }}</span>
                                    </span>
                                    <input type="checkbox" wire:model="selectedCourses" value="{{ $course->id }}" class="rounded border-line text-brand">
                                </label>
                            @empty
                                <p class="text-sm text-content-muted dark:text-content-muted">No published courses are available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3 border-t border-line pt-6 dark:border-gray-700">
                    <button wire:click="closeModals" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-content hover:bg-gray-300 dark:bg-gray-600 dark:text-white">Cancel</button>
                    <button wire:click="saveAssignments" class="rounded-md bg-action px-4 py-2 text-sm font-medium text-action-foreground hover:bg-action">
                        Save and Enroll Cohort
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showReportModal && $selectedCohort)
        <div class="fixed inset-0 z-50 h-full w-full overflow-y-auto bg-gray-600 bg-opacity-50 dark:bg-black dark:bg-opacity-70" wire:click="closeModals" x-data="{ dialogOpen: true }" x-trap.inert.noscroll="dialogOpen" x-on:keydown.escape.window="dialogOpen = false; $wire.closeModals()" role="dialog" aria-modal="true" aria-label="Cohort Management dialog" tabindex="-1">
            <div class="relative top-10 mx-auto w-11/12 rounded-md border border-line bg-surface p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:w-4/5" wire:click.stop>
                <div class="flex items-center justify-between border-b border-line pb-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-content dark:text-white">{{ $selectedCohort->name }} Report</h3>
                    <button aria-label="Close dialog" wire:click="closeModals" class="text-content-muted hover:text-content-muted dark:hover:text-gray-300"><i class="fas fa-times text-xl" aria-hidden="true"></i></button>
                </div>
                <div class="mt-6 overflow-x-auto">
                    <x-ui.table label="Cohort Management records"><table class="min-w-full divide-y divide-line text-sm dark:divide-gray-700">
                        <thead class="bg-surface-muted dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium uppercase text-content-muted">Student</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium uppercase text-content-muted">Course</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium uppercase text-content-muted">Progress</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-medium uppercase text-content-muted">Completed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line dark:divide-gray-700">
                            @forelse($reportRows as $row)
                                <tr>
                                    <td class="px-4 py-2 text-content dark:text-white">
                                        {{ $row['student'] }}
                                        <div class="text-xs text-content-muted">{{ $row['email'] }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-content dark:text-gray-300">{{ $row['course'] }}</td>
                                    <td class="px-4 py-2 text-content dark:text-gray-300">{{ $row['progress'] }}</td>
                                    <td class="px-4 py-2 text-content dark:text-gray-300">{{ $row['completed'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-content-muted">Assign members and courses to produce a report.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table></x-ui.table>
                </div>
                <div class="mt-6 flex justify-end space-x-3 border-t border-line pt-6 dark:border-gray-700">
                    <button wire:click="exportCohortReport({{ $selectedCohort->id }})" class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
                        <i class="fas fa-download mr-2" aria-hidden="true"></i>Export CSV
                    </button>
                    <button wire:click="closeModals" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-content hover:bg-gray-300 dark:bg-gray-600 dark:text-white">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
