<div
    x-data="{ open: @entangle('aiAssistOpen').live, returnFocus: null }"
    x-init="$watch('open', value => { if (value) { returnFocus = document.activeElement; $nextTick(() => $refs.heading?.focus()) } else if (returnFocus) { $nextTick(() => returnFocus?.focus()) } })"
    x-cloak
    x-show="open"
    x-on:keydown.escape.window="$wire.closeAiAssist()"
    x-on:ai-assist-applied.window="if ($event.detail.richText) window.applyBootKodeAiText($event.detail.field, $event.detail.value)"
    class="fixed inset-0 z-[80] flex items-end justify-center p-0 sm:items-center sm:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="ai-assist-title">
    <button type="button" class="absolute inset-0 bg-slate-950/60" wire:click="closeAiAssist" aria-label="Close AI Assist"></button>

    <section
        x-trap.inert.noscroll="open"
        x-on:click.stop
        class="relative flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-t-2xl border border-line bg-surface shadow-2xl sm:rounded-2xl dark:bg-gray-900">
        <header class="flex items-start justify-between gap-4 border-b border-line px-5 py-4 sm:px-6">
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-brand">BootKode AI Assist</p>
                <h2 id="ai-assist-title" x-ref="heading" tabindex="-1" class="text-xl font-bold text-content focus:outline-none dark:text-white">
                    {{ $aiAssistMode === 'form' ? 'Draft '.$aiAssistFieldLabel : 'Assist with '.$aiAssistFieldLabel }}
                </h2>
                <p class="mt-1 text-sm text-content-muted">Generated content stays in preview until you explicitly apply it. Nothing is saved or published here.</p>
            </div>
            <button type="button" wire:click="closeAiAssist" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-content-muted hover:bg-surface-muted focus-visible:ring-2 focus-visible:ring-brand/40" aria-label="Close AI Assist">
                <i class="fas fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <div class="min-h-0 flex-1 space-y-5 overflow-y-auto px-5 py-5 sm:px-6">
            @if (! $aiAssistReady)
                <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100" role="status">
                    AI Assist is unavailable. An administrator must enable and configure the server-side AI provider. Your form remains fully usable.
                </div>
            @else
                @if ($aiAssistMode === 'field')
                    <div>
                        <label for="ai-assist-action" class="mb-2 block text-sm font-semibold text-content dark:text-white">Action</label>
                        <select id="ai-assist-action" wire:model="aiAssistAction" class="w-full rounded-lg border border-line bg-surface px-3 py-2.5 text-content focus:ring-2 focus:ring-brand/30 dark:bg-gray-800 dark:text-white">
                            @foreach ($aiAssistActions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label for="ai-assist-instructions" class="mb-2 block text-sm font-semibold text-content dark:text-white">
                        {{ $aiAssistMode === 'form' ? 'Topic or brief' : 'Additional instructions (optional)' }}
                    </label>
                    <textarea id="ai-assist-instructions" wire:model="aiAssistInstructions" rows="3" maxlength="1600"
                        aria-describedby="ai-assist-instructions-help ai-assist-instructions-error" aria-invalid="{{ $errors->has('aiAssistInstructions') ? 'true' : 'false' }}"
                        class="w-full rounded-lg border border-line bg-surface px-3 py-2.5 text-content focus:ring-2 focus:ring-brand/30 dark:bg-gray-800 dark:text-white"
                        placeholder="{{ $aiAssistMode === 'form' ? 'Describe the topic, audience, style and desired result…' : 'Add audience, tone or constraints…' }}"></textarea>
                    <p id="ai-assist-instructions-help" class="mt-1 text-xs text-content-muted">Do not include credentials, private learner data, or unsupported claims.</p>
                    @error('aiAssistInstructions') <p class="mt-1 text-sm text-red-600" id="ai-assist-instructions-error">{{ $message }}</p> @enderror
                </div>

                <button type="button" wire:click="generateAiAssist" wire:loading.attr="disabled" wire:target="generateAiAssist"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-action px-4 py-2.5 font-semibold text-action-foreground hover:bg-action/90 focus-visible:ring-2 focus-visible:ring-brand/40 disabled:cursor-wait disabled:opacity-60">
                    <span wire:loading.remove wire:target="generateAiAssist"><i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i> {{ $aiAssistDraft ? 'Regenerate draft' : 'Generate draft' }}</span>
                    <span wire:loading wire:target="generateAiAssist"><i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Generating…</span>
                </button>
            @endif

            <div aria-live="polite" aria-atomic="true">
                @if ($aiAssistError)
                    <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/40 dark:text-red-100" role="alert">{{ $aiAssistError }}</div>
                @endif
                @error('aiAssist')
                    <div class="rounded-xl border border-red-300 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-950/40 dark:text-red-100" role="alert">{{ $message }}</div>
                @enderror
            </div>

            @if ($aiAssistDraft)
                <div class="space-y-4" aria-label="Generated content preview">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-bold text-content dark:text-white">Review the generated draft</h3>
                        <label class="flex items-start gap-2 text-sm text-content-muted">
                            <input type="checkbox" wire:model="aiAssistConfirmReplace" class="mt-0.5 rounded border-line text-brand focus:ring-brand/30">
                            <span>Allow applying over fields that already contain content</span>
                        </label>
                    </div>

                    @foreach ($aiAssistDraft as $field => $value)
                        <article class="rounded-xl border border-line bg-surface-muted/60 p-4" wire:key="ai-draft-{{ $field }}">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                <label class="flex items-center gap-2 font-semibold text-content dark:text-white">
                                    @if ($aiAssistMode === 'form')
                                        <input type="checkbox" wire:model="aiAssistSelected.{{ $field }}" class="rounded border-line text-brand focus:ring-brand/30">
                                    @endif
                                    {{ str($field)->headline() }}
                                </label>
                                <button type="button" wire:click="applyAiAssistField(@js($field))" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-brand px-3 py-2 text-sm font-semibold text-brand hover:bg-brand/10 focus-visible:ring-2 focus-visible:ring-brand/40">
                                    <i class="fas fa-arrow-down" aria-hidden="true"></i> Apply this field
                                </button>
                            </div>
                            @if (is_array($value))
                                <ul class="list-disc space-y-1 pl-5 text-sm text-content dark:text-gray-200">
                                    @foreach ($value as $item)<li>{{ $item }}</li>@endforeach
                                </ul>
                            @else
                                <div class="max-h-64 overflow-y-auto whitespace-pre-wrap text-sm leading-6 text-content dark:text-gray-200">{{ $value }}</div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        <footer class="flex flex-wrap justify-end gap-3 border-t border-line px-5 py-4 sm:px-6">
            <button type="button" wire:click="closeAiAssist" class="min-h-11 rounded-lg border border-line px-4 py-2.5 font-semibold text-content hover:bg-surface-muted dark:text-white">Cancel</button>
            @if ($aiAssistMode === 'form' && $aiAssistDraft)
                <button type="button" wire:click="applySelectedAiAssist" class="min-h-11 rounded-lg bg-action px-4 py-2.5 font-semibold text-action-foreground hover:bg-action/90">Apply selected fields</button>
            @endif
        </footer>
    </section>
</div>
