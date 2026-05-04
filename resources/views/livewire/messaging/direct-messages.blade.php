@php
    $authUser = auth()->user();
    $authId = $authUser?->id;
    $activeOtherParticipant = $activeConversation?->otherParticipantFor($authUser);
@endphp

<div
    class="mx-auto max-w-7xl"
    wire:poll.2000ms="pollMessages"
    x-data="{
        conversationsOpen: false,
        desktopConversationsOpen: true,
        draft: '',
        sending: false,
        newMessageCount: 0,
        pinnedToBottom: true,
        scrollMessages() {
            this.$nextTick(() => {
                const el = this.$refs.messages;
                if (!el) return;

                el.scrollTop = el.scrollHeight;
                this.pinnedToBottom = true;
            });
        },
        isNearBottom() {
            const el = this.$refs.messages;
            if (!el) return true;

            return el.scrollHeight - el.scrollTop - el.clientHeight < 180;
        },
        handleMessagesScroll() {
            this.pinnedToBottom = this.isNearBottom();

            if (this.pinnedToBottom) {
                this.newMessageCount = 0;
            }
        },
        handleIncomingMessage(count) {
            if (this.pinnedToBottom || this.isNearBottom()) {
                this.newMessageCount = 0;
                this.scrollMessages();
                return;
            }

            this.newMessageCount += Number(count || 1);
        },
        viewLatestMessage() {
            this.newMessageCount = 0;
            this.scrollMessages();
        },
        resizeComposer() {
            this.$nextTick(() => {
                const el = this.$refs.composer;
                if (!el) return;
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 144) + 'px';
            });
        },
        async sendDraft() {
            const body = this.draft.trim();
            if (!body || this.sending) return;

            this.sending = true;
            this.draft = '';
            this.resizeComposer();

            try {
                await this.$wire.sendMessage(body);
                this.scrollMessages();
                this.$nextTick(() => this.$refs.composer?.focus());
            } catch (error) {
                this.draft = body;
                this.resizeComposer();
            } finally {
                this.sending = false;
            }
        }
    }"
    x-init="scrollMessages()"
    x-on:conversation-opened.window="conversationsOpen = false; newMessageCount = 0; scrollMessages()"
    x-on:incoming-message.window="handleIncomingMessage($event.detail.count)">
    @error('messageBody')
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $message }}
        </div>
    @enderror

    <div class="overflow-hidden rounded-xl border border-themed-primary bg-themed-secondary shadow-xl">
        <div class="flex h-[calc(100dvh-9rem)] min-h-[34rem] max-h-[54rem] min-w-0">
            <aside
                x-show="desktopConversationsOpen"
                x-transition.opacity.duration.150ms
                class="hidden w-80 shrink-0 border-r border-themed-primary bg-themed-secondary lg:flex lg:flex-col">
                @include('livewire.messaging.partials.conversation-list', [
                    'conversations' => $conversations,
                    'activeConversationId' => $activeConversationId,
                    'authUser' => $authUser,
                    'availableCourses' => $availableCourses,
                    'desktop' => true,
                ])
            </aside>

            <div x-cloak x-show="conversationsOpen" class="fixed inset-0 z-50 lg:hidden">
                <div class="absolute inset-0 bg-black/50" @click="conversationsOpen = false"></div>

                <aside
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="absolute inset-y-0 left-0 flex w-[min(22rem,92vw)] flex-col border-r border-themed-primary bg-themed-secondary shadow-2xl">
                    @include('livewire.messaging.partials.conversation-list', [
                        'conversations' => $conversations,
                        'activeConversationId' => $activeConversationId,
                        'authUser' => $authUser,
                        'availableCourses' => $availableCourses,
                        'desktop' => false,
                    ])
                </aside>
            </div>

            <section class="relative flex min-h-0 min-w-0 flex-1 flex-col bg-themed-primary">
                <header class="flex min-h-[4.5rem] items-center justify-between gap-3 border-b border-themed-primary bg-themed-secondary px-3 py-3 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            type="button"
                            @click="window.innerWidth >= 1024 ? desktopConversationsOpen = !desktopConversationsOpen : conversationsOpen = true"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary transition hover:bg-themed-secondary"
                            aria-label="Toggle conversations">
                            <i class="fas fa-bars"></i>
                        </button>

                        @if ($activeConversation)
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($activeOtherParticipant?->name ?? 'U', 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <h1 class="truncate text-base font-semibold text-themed-primary sm:text-lg">
                                    {{ $activeOtherParticipant?->name ?? 'Unknown user' }}
                                </h1>
                                <p class="truncate text-xs text-themed-secondary sm:text-sm">
                                    {{ $activeConversation->course?->title }}
                                </p>
                            </div>
                        @else
                            <div class="min-w-0">
                                <h1 class="truncate text-base font-semibold text-themed-primary sm:text-lg">Chat</h1>
                                <p class="truncate text-xs text-themed-secondary sm:text-sm">Course messages</p>
                            </div>
                        @endif
                    </div>

                    @if ($activeConversation?->course)
                        <a href="{{ route('course.view', $activeConversation->course->slug) }}"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary transition hover:bg-themed-secondary sm:w-auto sm:px-3"
                            wire:navigate
                            aria-label="Open course">
                            <i class="fas fa-book-open sm:mr-2"></i>
                            <span class="hidden text-sm font-semibold sm:inline">Course</span>
                        </a>
                    @endif
                </header>

                <div class="relative min-h-0 flex-1">
                    <div
                        class="h-full overflow-y-auto px-3 py-4 sm:px-5"
                        x-ref="messages"
                        x-init="scrollMessages()"
                        x-on:scroll.passive="handleMessagesScroll()">
                        @if ($activeConversation)
                            <div class="mx-auto flex max-w-3xl flex-col gap-4">
                                @forelse ($messages as $directMessage)
                                    @php
                                        $isMine = (int) $directMessage->sender_id === (int) $authId;
                                    @endphp

                                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                        <div class="max-w-[88%] sm:max-w-[76%]">
                                            <div class="{{ $isMine ? 'rounded-l-xl rounded-tr-xl bg-blue-600 text-white' : 'rounded-r-xl rounded-tl-xl border border-themed-primary bg-themed-secondary text-themed-primary' }} px-4 py-3 shadow-sm">
                                                <p class="break-words whitespace-pre-line text-sm leading-6">{{ $directMessage->body }}</p>
                                            </div>

                                            <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px] {{ $isMine ? 'justify-end text-themed-tertiary' : 'text-themed-secondary' }}">
                                                <span class="max-w-[9rem] truncate">{{ $directMessage->sender?->name }}</span>
                                                <span>{{ $directMessage->created_at->format('M j, g:i A') }}</span>
                                                @if ($isMine && $directMessage->read_at)
                                                    <span>Read</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex min-h-[22rem] items-center justify-center text-center">
                                        <div>
                                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-themed-tertiary">
                                                <i class="fas fa-comment-dots text-xl text-themed-secondary"></i>
                                            </div>
                                            <p class="mt-4 text-sm font-semibold text-themed-primary">Start the conversation</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        @else
                            <div class="flex h-full items-center justify-center p-6 text-center">
                                <div class="max-w-sm">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-themed-secondary shadow">
                                        <i class="fas fa-comments text-2xl text-themed-secondary"></i>
                                    </div>
                                    <h2 class="mt-4 text-lg font-semibold text-themed-primary">Choose a conversation</h2>
                                    <button
                                        type="button"
                                        @click="conversationsOpen = true"
                                        class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 lg:hidden">
                                        <i class="fas fa-comments"></i>
                                        Conversations
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($activeConversation)
                        <div
                            x-cloak
                            x-show="newMessageCount > 0"
                            x-transition.opacity.duration.150ms
                            class="pointer-events-none absolute inset-x-0 bottom-4 z-10 flex justify-center px-4">
                            <button
                                type="button"
                                @click="viewLatestMessage()"
                                class="pointer-events-auto inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-lg ring-1 ring-white/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <i class="fas fa-arrow-down text-[11px]"></i>
                                <span x-text="newMessageCount === 1 ? '1 new message' : `${newMessageCount} new messages`"></span>
                                <span class="underline underline-offset-2">View</span>
                            </button>
                        </div>
                    @endif
                </div>

                @if ($activeConversation)
                    <form x-on:submit.prevent="sendDraft()" class="shrink-0 border-t border-themed-primary bg-themed-secondary p-3 sm:p-4">
                        <label for="messageBody" class="sr-only">Message</label>
                        <div class="mx-auto flex max-w-3xl items-end gap-2 sm:gap-3">
                            <textarea id="messageBody" x-ref="composer" x-model="draft" rows="1" maxlength="2000"
                                x-on:input="resizeComposer()"
                                x-on:keydown.enter.exact.prevent="sendDraft()"
                                placeholder="Message"
                                class="max-h-36 min-h-11 flex-1 resize-none overflow-y-auto rounded-lg border-themed-primary bg-themed-tertiary px-3 py-2.5 text-sm text-themed-primary placeholder-themed-tertiary focus:border-blue-500 focus:ring-blue-500"></textarea>

                            <button type="submit"
                                x-bind:disabled="sending || draft.trim().length === 0"
                                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto sm:px-5">
                                <i class="fas fa-paper-plane sm:mr-2"></i>
                                <span class="hidden sm:inline">Send</span>
                            </button>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</div>
