<div class="flex min-h-0 flex-1 flex-col">
    <div class="border-b border-themed-primary p-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-themed-primary">Conversations</h2>
                <p class="mt-0.5 text-xs text-themed-secondary">{{ $conversations->count() }} active</p>
            </div>

            @unless ($desktop)
                <button
                    type="button"
                    @click="conversationsOpen = false"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary"
                    aria-label="Close conversations">
                    <i class="fas fa-times"></i>
                </button>
            @endunless
        </div>

        <div class="relative mt-4">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-themed-tertiary"></i>
            <input wire:model.live.debounce.300ms="search" type="search"
                placeholder="Search"
                class="h-10 w-full rounded-lg border-themed-primary bg-themed-tertiary pl-10 pr-3 text-sm text-themed-primary placeholder-themed-tertiary focus:border-blue-500 focus:ring-blue-500">
        </div>
    </div>

    <div class="min-h-0 flex-1 overflow-y-auto">
        @forelse ($conversations as $conversation)
            @php
                $otherParticipant = $conversation->otherParticipantFor($authUser);
                $isActive = (int) $activeConversationId === (int) $conversation->id;
            @endphp

            <button wire:click="selectConversation({{ $conversation->id }})"
                @click="conversationsOpen = false"
                class="flex w-full gap-3 border-b border-themed-primary px-4 py-3 text-left transition hover:bg-themed-tertiary {{ $isActive ? 'bg-themed-tertiary' : '' }}">
                <div class="relative flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-sm font-semibold text-blue-700">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($otherParticipant?->name ?? 'U', 0, 1)) }}
                    @if ($conversation->unread_count > 0)
                        <span class="absolute -right-1 -top-1 rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white">
                            {{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}
                        </span>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-themed-primary">
                                {{ $otherParticipant?->name ?? 'Unknown user' }}
                            </p>
                            <p class="truncate text-xs text-themed-secondary">{{ $conversation->course?->title }}</p>
                        </div>

                        <span class="shrink-0 text-[11px] text-themed-tertiary">
                            {{ ($conversation->last_message_at ?? $conversation->updated_at)?->diffForHumans(null, true) }}
                        </span>
                    </div>

                    <p class="mt-2 truncate text-xs text-themed-secondary">
                        {{ $conversation->last_message_preview ?: 'No messages yet' }}
                    </p>
                </div>
            </button>
        @empty
            <div class="p-6 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-themed-tertiary">
                    <i class="fas fa-comments text-themed-secondary"></i>
                </div>
                <p class="mt-3 text-sm font-semibold text-themed-primary">No conversations</p>
            </div>
        @endforelse
    </div>

    @if ($availableCourses->isNotEmpty())
        <div class="border-t border-themed-primary p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-themed-secondary">Start Chat</p>

            <div class="mt-3 space-y-2">
                @foreach ($availableCourses->take(4) as $course)
                    <button wire:click="startConversationForCourse({{ $course->id }})"
                        @click="conversationsOpen = false"
                        class="flex w-full items-center gap-3 rounded-lg border border-themed-primary bg-themed-tertiary px-3 py-2.5 text-left transition hover:bg-themed-secondary">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-xs font-semibold text-white">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($course->instructor?->name ?? 'I', 0, 1)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-themed-primary">{{ $course->title }}</p>
                            <p class="truncate text-[11px] text-themed-secondary">{{ $course->instructor?->name ?? 'Instructor' }}</p>
                        </div>

                        <i class="fas fa-comment text-xs text-themed-secondary"></i>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
</div>
