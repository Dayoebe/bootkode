@php
    $authUser = auth()->user();
    $authId = $authUser?->id;
@endphp

<div class="mx-auto max-w-7xl" wire:poll.5000ms="pollMessages">
    <div class="mb-6 overflow-hidden rounded-xl border border-themed-primary bg-themed-secondary shadow-lg">
        <div class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-secondary">Mentorship</p>
                <h1 class="mt-1 text-2xl font-semibold text-themed-primary">Instructor Messages</h1>
                <p class="mt-1 text-sm text-themed-secondary">Course conversations between students and instructors.</p>
            </div>

            <div class="relative w-full md:max-w-sm">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-themed-tertiary"></i>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Search conversations"
                    class="w-full rounded-lg border-themed-primary bg-themed-tertiary py-2.5 pl-10 pr-3 text-sm text-themed-primary placeholder-themed-tertiary focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>
    </div>

    @error('messageBody')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    <div class="grid min-h-[680px] overflow-hidden rounded-xl border border-themed-primary bg-themed-secondary shadow-xl lg:grid-cols-[22rem_minmax(0,1fr)]">
        <aside class="border-b border-themed-primary bg-themed-secondary lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between border-b border-themed-primary px-4 py-3">
                <h2 class="text-sm font-semibold text-themed-primary">Conversations</h2>
                <span class="rounded-full bg-themed-tertiary px-2.5 py-1 text-xs font-semibold text-themed-secondary">
                    {{ $conversations->count() }}
                </span>
            </div>

            <div class="max-h-[34rem] overflow-y-auto lg:max-h-[calc(680px-49px)]">
                @forelse ($conversations as $conversation)
                    @php
                        $otherParticipant = $conversation->otherParticipantFor($authUser);
                        $isActive = (int) $activeConversationId === (int) $conversation->id;
                    @endphp

                    <button wire:click="selectConversation({{ $conversation->id }})"
                        class="flex w-full gap-3 border-b border-themed-primary px-4 py-3 text-left transition hover:bg-themed-tertiary {{ $isActive ? 'bg-themed-tertiary' : '' }}">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-sm font-semibold text-blue-700">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($otherParticipant?->name ?? 'U', 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-themed-primary">
                                        {{ $otherParticipant?->name ?? 'Unknown user' }}
                                    </p>
                                    <p class="truncate text-xs text-themed-secondary">{{ $conversation->course?->title }}</p>
                                </div>

                                @if ($conversation->unread_count > 0)
                                    <span class="rounded-full bg-blue-600 px-2 py-0.5 text-[11px] font-semibold text-white">
                                        {{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}
                                    </span>
                                @endif
                            </div>

                            <p class="mt-2 truncate text-xs text-themed-secondary">
                                {{ $conversation->last_message_preview ?: 'No messages yet' }}
                            </p>
                            <p class="mt-1 text-[11px] text-themed-tertiary">
                                {{ ($conversation->last_message_at ?? $conversation->updated_at)?->diffForHumans() }}
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="p-6 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-themed-tertiary">
                            <i class="fas fa-comments text-themed-secondary"></i>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-themed-primary">No conversations yet</p>
                        <p class="mt-1 text-xs leading-5 text-themed-secondary">Start from an enrolled course below.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="flex min-h-[680px] flex-col bg-themed-primary">
            @if ($activeConversation)
                @php
                    $otherParticipant = $activeConversation->otherParticipantFor($authUser);
                @endphp

                <header class="border-b border-themed-primary bg-themed-secondary px-5 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($otherParticipant?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h2 class="truncate text-lg font-semibold text-themed-primary">{{ $otherParticipant?->name ?? 'Unknown user' }}</h2>
                                <p class="truncate text-sm text-themed-secondary">{{ $activeConversation->course?->title }}</p>
                            </div>
                        </div>

                        @if ($activeConversation->course)
                            <a href="{{ route('course.view', $activeConversation->course->slug) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-themed-primary bg-themed-tertiary px-3 py-2 text-sm font-semibold text-themed-primary transition hover:bg-themed-secondary"
                                wire:navigate>
                                <i class="fas fa-book-open"></i>
                                Open Course
                            </a>
                        @endif
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto px-4 py-5 sm:px-6" x-data x-ref="messages">
                    <div class="space-y-4">
                        @forelse ($messages as $directMessage)
                            @php
                                $isMine = (int) $directMessage->sender_id === (int) $authId;
                            @endphp

                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[85%] sm:max-w-[70%]">
                                    <div class="rounded-lg px-4 py-3 shadow-sm {{ $isMine ? 'bg-blue-600 text-white' : 'border border-themed-primary bg-themed-secondary text-themed-primary' }}">
                                        <p class="whitespace-pre-line text-sm leading-6">{{ $directMessage->body }}</p>
                                    </div>
                                    <div class="mt-1 flex items-center gap-2 text-[11px] {{ $isMine ? 'justify-end text-themed-tertiary' : 'text-themed-secondary' }}">
                                        <span>{{ $directMessage->sender?->name }}</span>
                                        <span>{{ $directMessage->created_at->format('M j, g:i A') }}</span>
                                        @if ($isMine && $directMessage->read_at)
                                            <span>Read</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex h-full min-h-[320px] items-center justify-center text-center">
                                <div>
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-themed-tertiary">
                                        <i class="fas fa-comment-dots text-xl text-themed-secondary"></i>
                                    </div>
                                    <p class="mt-4 text-sm font-semibold text-themed-primary">Start the conversation</p>
                                    <p class="mt-1 text-sm text-themed-secondary">Send a message about this course.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <form wire:submit.prevent="sendMessage" class="border-t border-themed-primary bg-themed-secondary p-4">
                    <label for="messageBody" class="sr-only">Message</label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <textarea id="messageBody" wire:model.defer="messageBody" rows="3" maxlength="2000"
                            placeholder="Write your message"
                            class="min-h-[5rem] flex-1 resize-none rounded-lg border-themed-primary bg-themed-tertiary text-sm text-themed-primary placeholder-themed-tertiary focus:border-blue-500 focus:ring-blue-500"></textarea>

                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <i class="fas fa-paper-plane"></i>
                            <span wire:loading.remove wire:target="sendMessage">Send</span>
                            <span wire:loading wire:target="sendMessage">Sending</span>
                        </button>
                    </div>
                </form>
            @else
                <div class="flex flex-1 items-center justify-center p-6 text-center">
                    <div class="max-w-md">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-themed-secondary shadow">
                            <i class="fas fa-comments text-2xl text-themed-secondary"></i>
                        </div>
                        <h2 class="mt-4 text-lg font-semibold text-themed-primary">Choose a conversation</h2>
                        <p class="mt-2 text-sm leading-6 text-themed-secondary">Your course instructor messages will appear here.</p>
                    </div>
                </div>
            @endif
        </section>
    </div>

    @if ($availableCourses->isNotEmpty())
        <section class="mt-6 rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-lg">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-secondary">Enrolled Courses</p>
                    <h2 class="mt-1 text-lg font-semibold text-themed-primary">Start A Course Chat</h2>
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($availableCourses as $course)
                    <div class="rounded-lg border border-themed-primary bg-themed-tertiary p-4">
                        <p class="line-clamp-2 text-sm font-semibold text-themed-primary">{{ $course->title }}</p>
                        <p class="mt-2 truncate text-xs text-themed-secondary">
                            {{ $course->instructor?->name ?? 'Instructor' }}
                        </p>
                        <button wire:click="startConversationForCourse({{ $course->id }})"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
                            <i class="fas fa-comment"></i>
                            Message
                        </button>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
