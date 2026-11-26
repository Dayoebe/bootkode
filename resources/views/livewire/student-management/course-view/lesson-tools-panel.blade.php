<aside class="space-y-4 min-[1360px]:sticky min-[1360px]:top-20" aria-labelledby="lesson-tools-heading">
    <section class="rounded-lg border border-themed-primary bg-themed-secondary p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-themed-secondary">Learning tools</p>
                <h2 id="lesson-tools-heading" class="mt-1 text-base font-semibold text-themed-primary">Keep your place</h2>
            </div>
            <button type="button" wire:click="toggleBookmark" wire:loading.attr="disabled" wire:target="toggleBookmark"
                class="bk-topbar-icon-button {{ $isBookmarked ? 'border-action bg-action/10 text-action' : '' }}"
                aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                aria-label="{{ $isBookmarked ? 'Remove lesson bookmark' : 'Bookmark this lesson' }}">
                <i class="{{ $isBookmarked ? 'fas' : 'far' }} fa-bookmark" aria-hidden="true"></i>
            </button>
        </div>

        <form wire:submit="saveNotes" class="mt-4">
            <label for="lesson-notes-{{ $lesson->id }}" class="text-sm font-semibold text-themed-primary">Private note</label>
            <p id="lesson-notes-help-{{ $lesson->id }}" class="mt-1 text-xs leading-5 text-themed-secondary">Saved only to your account. Clear it and save to remove it.</p>
            <textarea id="lesson-notes-{{ $lesson->id }}" wire:model="lessonNotes" rows="7" maxlength="5000"
                class="bk-input mt-3 min-h-36 resize-y text-sm" placeholder="Capture a key idea, question, or next action…"
                aria-invalid="@error('lessonNotes') true @else false @enderror"
                aria-describedby="lesson-notes-help-{{ $lesson->id }} @error('lessonNotes') lesson-notes-error-{{ $lesson->id }} @enderror"></textarea>
            @error('lessonNotes')
                <p id="lesson-notes-error-{{ $lesson->id }}" class="bk-field-error mt-2" role="alert">{{ $message }}</p>
            @enderror
            <button type="submit" class="bk-button bk-button-primary mt-3 w-full" wire:loading.attr="disabled" wire:target="saveNotes">
                <span wire:loading.remove wire:target="saveNotes"><i class="fas fa-check" aria-hidden="true"></i> Save note</span>
                <span wire:loading wire:target="saveNotes"><i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i> Saving…</span>
            </button>
            <p class="mt-2 min-h-5 text-xs text-themed-secondary" role="status" aria-live="polite">{{ $statusMessage }}</p>
        </form>
    </section>

    @php
        $documentCount = count($lesson->getDocumentsArray());
        $linkCount = count($lesson->getExternalLinksArray());
        $assessmentCount = $lesson->assessments->count();
    @endphp
    @if($documentCount || $linkCount || $assessmentCount)
        <section class="rounded-lg border border-themed-primary bg-themed-secondary p-4 shadow-sm" aria-labelledby="lesson-materials-summary">
            <h2 id="lesson-materials-summary" class="text-sm font-semibold text-themed-primary">In this lesson</h2>
            <dl class="mt-3 space-y-2 text-sm">
                @if($assessmentCount)
                    <div class="flex items-center justify-between gap-3"><dt class="text-themed-secondary">Assessments</dt><dd class="font-semibold text-themed-primary">{{ $assessmentCount }}</dd></div>
                @endif
                @if($documentCount)
                    <div class="flex items-center justify-between gap-3"><dt class="text-themed-secondary">Downloads</dt><dd class="font-semibold text-themed-primary">{{ $documentCount }}</dd></div>
                @endif
                @if($linkCount)
                    <div class="flex items-center justify-between gap-3"><dt class="text-themed-secondary">External resources</dt><dd class="font-semibold text-themed-primary">{{ $linkCount }}</dd></div>
                @endif
            </dl>
        </section>
    @endif

    @if(Route::has('ai.learning.coach'))
        <a href="{{ route('ai.learning.coach') }}" wire:navigate class="block rounded-lg border border-themed-primary bg-themed-secondary p-4 shadow-sm transition hover:border-action">
            <span class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-action/10 text-action"><i class="fas fa-sparkles" aria-hidden="true"></i></span>
                <span>
                    <span class="block text-sm font-semibold text-themed-primary">Open AI learning coach</span>
                    <span class="mt-1 block text-xs leading-5 text-themed-secondary">Ask for guidance using the course access already attached to your account.</span>
                </span>
            </span>
        </a>
    @endif
</aside>
