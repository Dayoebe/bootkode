<div class="space-y-6">
    @if (session('message'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <section class="rounded-lg border border-themed-primary bg-themed-secondary p-5 md:p-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-accent-themed-primary/10 px-3 py-1 text-xs font-semibold text-accent-themed-primary">
                    <i class="fas fa-calendar-check"></i>
                    Mentorship operations
                </div>
                <h1 class="text-2xl font-bold text-themed-primary md:text-3xl">Scheduling, notes, payouts, and progress</h1>
                <p class="mt-2 max-w-3xl text-themed-secondary">
                    Book sessions from active mentorships, enforce mentor availability, capture notes after each meeting, and credit billable mentor payouts.
                </p>
            </div>

            <button type="button" wire:click="createSession"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-accent-themed-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-themed-secondary">
                <i class="fas fa-plus"></i>
                Book session
            </button>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <div class="text-sm text-themed-tertiary">Upcoming</div>
            <div class="mt-2 text-3xl font-bold text-themed-primary">{{ $stats['upcoming'] }}</div>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <div class="text-sm text-themed-tertiary">Need notes</div>
            <div class="mt-2 text-3xl font-bold text-themed-primary">{{ $stats['needs_notes'] }}</div>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <div class="text-sm text-themed-tertiary">Completed</div>
            <div class="mt-2 text-3xl font-bold text-themed-primary">{{ $stats['completed'] }}</div>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <div class="text-sm text-themed-tertiary">Pending payout</div>
            <div class="mt-2 text-3xl font-bold text-themed-primary">₦{{ number_format((float) $stats['pending_payout'], 2) }}</div>
        </div>
    </section>

    <section class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <select wire:model.live="dateFilter"
                class="rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                <option value="upcoming">Upcoming and active</option>
                <option value="this_week">This week</option>
                <option value="this_month">This month</option>
                <option value="past">Past and completed</option>
            </select>

            <select wire:model.live="statusFilter"
                class="rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                <option value="">All statuses</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="missed">Missed</option>
            </select>

            <div class="flex items-center justify-end text-sm text-themed-secondary">
                {{ $sessions->total() }} session{{ $sessions->total() === 1 ? '' : 's' }}
            </div>
        </div>
    </section>

    @if ($activeMentorships->isNotEmpty())
        <section class="grid grid-cols-1 gap-4 xl:grid-cols-3">
            @foreach ($activeMentorships->take(3) as $mentorship)
                <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-xs font-semibold uppercase text-themed-tertiary">
                                {{ auth()->id() === $mentorship->mentor_id ? 'Mentee' : 'Mentor' }}
                            </div>
                            <h3 class="truncate font-semibold text-themed-primary">
                                {{ auth()->id() === $mentorship->mentor_id ? $mentorship->mentee->name : $mentorship->mentor->name }}
                            </h3>
                            <div class="mt-2 text-xs text-themed-secondary">
                                Goals {{ $mentorship->goal_completion_percentage }}% complete
                            </div>
                        </div>
                        <button type="button" wire:click="createSession({{ $mentorship->id }})"
                            class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-themed-secondary bg-themed-primary px-3 py-2 text-xs font-semibold text-themed-primary transition hover:border-accent-themed-primary">
                            <i class="fas fa-calendar-plus"></i>
                            Book
                        </button>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-themed-tertiary">
                        <div class="h-2 rounded-full bg-accent-themed-primary" style="width: {{ $mentorship->progress_percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    <section class="space-y-4">
        @forelse ($sessions as $session)
            @php
                $isMentor = auth()->id() === $session->mentorship->mentor_id;
                $otherPerson = $isMentor ? $session->mentorship->mentee : $session->mentorship->mentor;
                $statusClass = match($session->status) {
                    'completed' => 'bg-green-500/10 text-green-600',
                    'in_progress' => 'bg-blue-500/10 text-blue-600',
                    'cancelled', 'missed' => 'bg-red-500/10 text-red-600',
                    default => 'bg-accent-themed-primary/10 text-accent-themed-primary',
                };
            @endphp
            <article class="rounded-lg border border-themed-primary bg-themed-secondary p-4 transition hover:border-accent-themed-primary">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="mb-2 flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ ucwords(str_replace('_', ' ', $session->status)) }}
                            </span>
                            @if ($session->is_billable)
                                <span class="rounded-full bg-themed-tertiary px-2 py-1 text-xs font-semibold text-themed-secondary">
                                    ₦{{ number_format((float) $session->session_cost, 2) }} {{ $session->payment_status === 'paid' ? 'paid' : 'pending' }}
                                </span>
                            @else
                                <span class="rounded-full bg-green-500/10 px-2 py-1 text-xs font-semibold text-green-600">Free</span>
                            @endif
                        </div>
                        <h3 class="truncate text-lg font-semibold text-themed-primary">{{ $session->title }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-themed-secondary">{{ $session->description }}</p>
                        <div class="mt-3 flex flex-wrap gap-3 text-xs text-themed-tertiary">
                            <span><i class="fas fa-user mr-1"></i>{{ $otherPerson->name }}</span>
                            <span><i class="fas fa-calendar mr-1"></i>{{ $session->scheduled_at->format('M j, Y') }}</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $session->scheduled_at->format('g:i A') }} · {{ $session->duration_minutes ?? 60 }} min</span>
                            <span><i class="fas fa-video mr-1"></i>{{ ucwords(str_replace('_', ' ', $session->format ?? 'video')) }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 lg:justify-end">
                        <button type="button" wire:click="viewSession({{ $session->id }})"
                            class="rounded-lg border border-themed-secondary bg-themed-primary px-3 py-2 text-sm font-medium text-themed-primary transition hover:border-accent-themed-primary">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>

                        @if ($session->status === 'scheduled')
                            <button type="button" wire:click="editSession({{ $session->id }})"
                                class="rounded-lg border border-themed-secondary bg-themed-primary px-3 py-2 text-sm font-medium text-themed-primary transition hover:border-accent-themed-primary">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            @if ($isMentor || auth()->user()->isAcademyAdmin() || auth()->user()->isSuperAdmin())
                                <button type="button" wire:click="startSession({{ $session->id }})"
                                    class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
                                    Start
                                </button>
                                <button type="button" wire:click="completeSession({{ $session->id }})"
                                    class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-green-700">
                                    Notes
                                </button>
                                <button type="button" wire:click="cancelSession({{ $session->id }})"
                                    class="rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700">
                                    Cancel
                                </button>
                            @endif
                        @elseif ($session->status === 'in_progress' && ($isMentor || auth()->user()->isAcademyAdmin() || auth()->user()->isSuperAdmin()))
                            <button type="button" wire:click="completeSession({{ $session->id }})"
                                class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-green-700">
                                Add notes
                            </button>
                        @elseif ($session->status === 'completed' && $session->is_billable && $session->payment_status !== 'paid' && ($isMentor || auth()->user()->isAcademyAdmin() || auth()->user()->isSuperAdmin()))
                            <button type="button" wire:click="payoutSession({{ $session->id }})"
                                class="rounded-lg bg-accent-themed-primary px-3 py-2 text-sm font-medium text-white transition hover:bg-accent-themed-secondary">
                                Pay mentor
                            </button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-lg border-2 border-dashed border-themed-secondary bg-themed-secondary p-10 text-center">
                <i class="fas fa-calendar-alt mb-3 text-4xl text-themed-tertiary"></i>
                <h3 class="text-lg font-semibold text-themed-primary">No sessions found</h3>
                <p class="mt-1 text-themed-secondary">Book a session from an active mentorship to start tracking operations.</p>
                <button type="button" wire:click="createSession"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-themed-secondary">
                    <i class="fas fa-plus"></i>
                    Book session
                </button>
            </div>
        @endforelse

        @if ($sessions->hasPages())
            <div>{{ $sessions->links() }}</div>
        @endif
    </section>

    @if ($showSessionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg border border-themed-primary bg-themed-secondary shadow-xl">
                <div class="flex items-center justify-between border-b border-themed-primary p-5">
                    <h2 class="text-xl font-bold text-themed-primary">{{ $editingSessionId ? 'Edit session' : 'Book session' }}</h2>
                    <button type="button" wire:click="closeModal" class="grid h-9 w-9 place-items-center rounded-lg bg-themed-tertiary text-themed-secondary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitSession" class="space-y-5 p-5">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-themed-primary">Mentorship</label>
                        <select wire:model="mentorshipId"
                            class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                            <option value="">Choose active mentorship</option>
                            @foreach ($activeMentorships as $mentorship)
                                <option value="{{ $mentorship->id }}">
                                    {{ $mentorship->mentor->name }} with {{ $mentorship->mentee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('mentorshipId') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Title</label>
                            <input type="text" wire:model="sessionTitle"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                            @error('sessionTitle') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Type</label>
                            <select wire:model="sessionType"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                                @foreach ($sessionTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('sessionType') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-themed-primary">Description</label>
                        <textarea wire:model="sessionDescription" rows="3"
                            class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"></textarea>
                        @error('sessionDescription') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Date and time</label>
                            <input type="datetime-local" wire:model="scheduledAt"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                            @error('scheduledAt') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Duration</label>
                            <select wire:model="duration"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="90">90 minutes</option>
                                <option value="120">120 minutes</option>
                            </select>
                            @error('duration') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Format</label>
                            <select wire:model="sessionFormat"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                                @foreach ($sessionFormats as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('sessionFormat') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-themed-primary">Agenda</label>
                        <textarea wire:model="agenda" rows="3"
                            class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"></textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-themed-primary">Meeting link</label>
                        <input type="url" wire:model="meetingLink"
                            class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"
                            placeholder="https://...">
                        @error('meetingLink') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 border-t border-themed-primary pt-5">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-medium text-themed-primary hover:bg-themed-tertiary">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-themed-secondary">
                            {{ $editingSessionId ? 'Save changes' : 'Book session' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showCompletionModal && $selectedSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg border border-themed-primary bg-themed-secondary shadow-xl">
                <div class="flex items-center justify-between border-b border-themed-primary p-5">
                    <div>
                        <h2 class="text-xl font-bold text-themed-primary">Complete session</h2>
                        <p class="text-sm text-themed-secondary">{{ $selectedSession->title }}</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="grid h-9 w-9 place-items-center rounded-lg bg-themed-tertiary text-themed-secondary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitCompletion" class="space-y-5 p-5">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-themed-primary">Session notes</label>
                        <textarea wire:model="sessionNotes" rows="5"
                            class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"
                            placeholder="What was discussed, decided, and learned?"></textarea>
                        @error('sessionNotes') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-themed-primary">Action items</label>
                        <div class="space-y-2">
                            @foreach ($actionItems as $index => $item)
                                <div class="flex gap-2">
                                    <input type="text" wire:model="actionItems.{{ $index }}"
                                        class="flex-1 rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"
                                        placeholder="Next step">
                                    <button type="button" wire:click="removeActionItem({{ $index }})"
                                        class="grid h-10 w-10 place-items-center rounded-lg bg-red-600 text-white">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" wire:click="addActionItem"
                            class="mt-3 inline-flex items-center gap-2 rounded-lg border border-themed-primary px-3 py-2 text-sm font-medium text-themed-primary">
                            <i class="fas fa-plus"></i>
                            Add action item
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Mentor feedback</label>
                            <textarea wire:model="mentorFeedback" rows="3"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"></textarea>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Mentee feedback</label>
                            <textarea wire:model="menteeFeedback" rows="3"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Actual duration</label>
                            <input type="number" wire:model="actualDuration" min="15" max="360"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Mentor rating</label>
                            <select wire:model="mentorRating"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                                <option value="">No rating</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-themed-primary">Mentee rating</label>
                            <select wire:model="menteeRating"
                                class="w-full rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                                <option value="">No rating</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="rounded-lg border border-themed-primary bg-themed-primary p-3 text-sm text-themed-secondary">
                        @if ($selectedSession->is_billable)
                            Completing this session credits ₦{{ number_format((float) $selectedSession->session_cost, 2) }} to the mentor wallet once.
                        @else
                            This is a free session. It will update progress and session history without payout.
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 border-t border-themed-primary pt-5">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-medium text-themed-primary hover:bg-themed-tertiary">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Complete session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDetailsModal && $selectedSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg border border-themed-primary bg-themed-secondary shadow-xl">
                <div class="flex items-center justify-between border-b border-themed-primary p-5">
                    <h2 class="text-xl font-bold text-themed-primary">Session details</h2>
                    <button type="button" wire:click="closeModal" class="grid h-9 w-9 place-items-center rounded-lg bg-themed-tertiary text-themed-secondary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-5 p-5">
                    <div>
                        <h3 class="text-lg font-semibold text-themed-primary">{{ $selectedSession->title }}</h3>
                        <p class="mt-1 text-sm text-themed-secondary">{{ $selectedSession->description }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="rounded-lg border border-themed-primary bg-themed-primary p-3">
                            <div class="text-xs text-themed-tertiary">Scheduled</div>
                            <div class="font-medium text-themed-primary">{{ $selectedSession->scheduled_at->format('M j, Y g:i A') }}</div>
                        </div>
                        <div class="rounded-lg border border-themed-primary bg-themed-primary p-3">
                            <div class="text-xs text-themed-tertiary">People</div>
                            <div class="font-medium text-themed-primary">{{ $selectedSession->mentorship->mentor->name }} and {{ $selectedSession->mentorship->mentee->name }}</div>
                        </div>
                    </div>

                    @if ($selectedSession->agenda)
                        <div>
                            <div class="mb-1 text-sm font-medium text-themed-primary">Agenda</div>
                            <p class="whitespace-pre-line text-sm text-themed-secondary">{{ $selectedSession->agenda }}</p>
                        </div>
                    @endif

                    @if ($selectedSession->session_notes)
                        <div>
                            <div class="mb-1 text-sm font-medium text-themed-primary">Notes</div>
                            <p class="whitespace-pre-line text-sm text-themed-secondary">{{ $selectedSession->session_notes }}</p>
                        </div>
                    @endif

                    @if ($selectedSession->action_items)
                        <div>
                            <div class="mb-2 text-sm font-medium text-themed-primary">Action items</div>
                            <ul class="space-y-2">
                                @foreach ($selectedSession->action_items as $item)
                                    <li class="rounded-lg bg-themed-primary px-3 py-2 text-sm text-themed-secondary">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($selectedSession->meeting_link)
                        <a href="{{ $selectedSession->meeting_link }}" target="_blank"
                            class="inline-flex items-center gap-2 rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-themed-secondary">
                            <i class="fas fa-external-link-alt"></i>
                            Open meeting link
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
