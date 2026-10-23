<div class="grid min-h-[70vh] gap-5 xl:grid-cols-[320px_minmax(0,1fr)]">
    <aside class="rounded-lg border border-themed-primary bg-themed-secondary p-4 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div><p class="text-xs font-bold uppercase tracking-wider text-brand">BootKode AI</p><h2 class="text-lg font-bold text-themed-primary">Conversations</h2></div>
            <button type="button" wire:click="newConversation" class="rounded-lg bg-action px-3 py-2 text-sm font-semibold text-action-foreground"><i class="fas fa-plus mr-1"></i> New</button>
        </div>
        <label class="mt-4 block text-xs font-semibold uppercase text-themed-secondary" for="mentor-course">Course context</label>
        <select id="mentor-course" wire:model="selectedCourseId" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
            <option value="">General coding help</option>
            @foreach($courses as $course)<option value="{{ $course->id }}">{{ $course->title }}</option>@endforeach
        </select>
        <div class="mt-4 space-y-2">
            @forelse($conversations as $conversation)
                <button type="button" wire:click="selectConversation({{ $conversation->id }})" class="w-full rounded-lg border p-3 text-left transition {{ $activeConversationId === $conversation->id ? 'border-brand bg-action/10' : 'border-themed-primary hover:border-brand' }}">
                    <span class="block truncate text-sm font-semibold text-themed-primary">{{ $conversation->title }}</span>
                    <span class="mt-1 block truncate text-xs text-themed-tertiary">{{ $conversation->course?->title ?? 'General' }} · {{ ($conversation->last_message_at ?? $conversation->created_at)->diffForHumans() }}</span>
                </button>
            @empty
                <p class="rounded-lg border border-dashed border-themed-primary p-5 text-center text-sm text-themed-secondary">Start a private conversation when you are ready.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $conversations->links() }}</div>
    </aside>

    <section class="flex min-h-[70vh] flex-col overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary shadow-sm">
        <header class="border-b border-themed-primary p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><h1 class="text-xl font-bold text-themed-primary bk-page-title">{{ $activeConversation?->title ?? 'AI Coding Mentor' }}</h1><p class="mt-1 text-sm text-themed-secondary">Teaching-first guidance grounded in course material you may access.</p></div>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $providerStatus['ready'] ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $providerStatus['ready'] ? 'Provider ready' : ($providerStatus['reason'] === 'disabled' ? 'AI disabled' : 'Setup required') }}</span>
            </div>
            @if($activeConversation)
                <div class="mt-3 flex gap-2">
                    <input wire:model="newTitle" aria-label="New conversation title" placeholder="Rename conversation" class="min-w-0 flex-1 rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                    <button wire:click="renameConversation" type="button" class="rounded-lg border border-themed-primary px-3 py-2 text-sm text-themed-primary">Rename</button>
                    <button wire:click="deleteConversation" wire:confirm="Delete this private conversation and its messages?" type="button" class="rounded-lg border border-red-200 px-3 py-2 text-sm text-red-600">Delete</button>
                </div>
                @error('newTitle')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            @endif
        </header>

        <div class="flex-1 space-y-5 overflow-y-auto p-5" x-data x-on:mentor-answered.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)">
            @forelse($activeConversation?->messages ?? [] as $message)
                <article class="{{ $message->role === 'user' ? 'ml-auto bg-action text-action-foreground' : 'mr-auto border border-themed-primary bg-themed-tertiary text-themed-primary' }} max-w-3xl rounded-lg px-4 py-3">
                    <p class="mb-2 text-xs font-bold uppercase tracking-wider opacity-70">{{ $message->role === 'user' ? 'You' : 'BootKode AI' }}</p>
                    <div class="whitespace-pre-wrap break-words text-sm leading-6">{{ $message->content }}</div>
                </article>
            @empty
                <div class="mx-auto max-w-xl py-16 text-center"><i class="fas fa-code text-4xl text-brand"></i><h3 class="mt-4 text-xl font-bold text-themed-primary">Learn by working through the problem</h3><p class="mt-2 text-sm text-themed-secondary">Ask for an explanation, a hint, debugging guidance, practice questions, or help understanding an authorized lesson. Submitted code is analyzed as text and is never executed.</p></div>
            @endforelse
        </div>

        <form wire:submit="send" class="border-t border-themed-primary p-5">
            @if($providerError)<div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">{{ $providerError }}</div>@endif
            @if(!$providerStatus['ready'])<div class="mb-3 rounded-lg border border-themed-primary bg-themed-tertiary p-3 text-sm text-themed-secondary">BootKode remains available, but AI replies require an administrator to enable and configure the provider.</div>@endif
            <textarea aria-label="Describe what you are trying to understand. Include code or the exact error when useful" wire:model="question" rows="4" class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-4 py-3 text-sm text-themed-primary" placeholder="Describe what you are trying to understand. Include code or the exact error when useful."></textarea>
            @error('question')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            <div class="mt-3 flex items-center justify-between gap-3"><p class="text-xs text-themed-tertiary">Do not submit passwords, API keys, or private personal data.</p><button type="submit" wire:loading.attr="disabled" @disabled(!$providerStatus['ready']) class="rounded-lg bg-action px-5 py-2.5 text-sm font-semibold text-action-foreground disabled:cursor-not-allowed disabled:opacity-50"><span wire:loading.remove wire:target="send">Send</span><span wire:loading wire:target="send">Thinking…</span></button></div>
        </form>
    </section>
</div>
