<?php

namespace App\Livewire\Concerns;

use App\Exceptions\AiProviderException;
use App\Services\Ai\AiContentAssistantService;
use App\Services\Ai\AiManager;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

trait InteractsWithAiContentAssistant
{
    public bool $aiAssistOpen = false;
    public string $aiAssistMode = 'field';
    public string $aiAssistField = '';
    public string $aiAssistFieldLabel = '';
    public string $aiAssistAction = '';
    public string $aiAssistInstructions = '';
    public array $aiAssistActions = [];
    public array $aiAssistDraft = [];
    public array $aiAssistSelected = [];
    public string $aiAssistApplyMode = 'replace';
    public bool $aiAssistCanAppend = false;
    public bool $aiAssistConfirmReplace = false;
    public ?string $aiAssistError = null;
    public bool $aiAssistReady = false;

    abstract protected function aiAssistFields(): array;

    abstract protected function aiAssistContext(): array;

    abstract protected function aiAssistFormName(): string;

    public function openAiAssist(string $field): void
    {
        $this->authorizeAiAssistRequest();
        $definition = $this->aiAssistDefinition($field);
        $this->resetAiAssistState();
        $this->aiAssistMode = 'field';
        $this->aiAssistField = $field;
        $this->aiAssistFieldLabel = (string) ($definition['label'] ?? Str::headline($field));
        $this->aiAssistActions = $this->actionLabels($definition['actions'] ?? ['generate', 'improve', 'rewrite']);
        $this->aiAssistAction = (string) array_key_first($this->aiAssistActions);
        $this->aiAssistCanAppend = (bool) ($definition['appendable'] ?? false)
            && ! $this->aiAssistValueIsEmpty($this->{$field});
        $this->aiAssistApplyMode = $this->aiAssistCanAppend ? 'append' : 'replace';
        $this->aiAssistReady = (bool) app(AiManager::class)->status()['ready'];
        $this->aiAssistOpen = true;
    }

    public function openAiAssistForm(): void
    {
        $this->authorizeAiAssistRequest();
        $this->resetAiAssistState();
        $this->aiAssistMode = 'form';
        $this->aiAssistFieldLabel = $this->aiAssistFormName();
        $this->aiAssistReady = (bool) app(AiManager::class)->status()['ready'];
        $this->aiAssistOpen = true;
    }

    public function closeAiAssist(): void
    {
        $this->aiAssistOpen = false;
        $this->resetErrorBag('aiAssist');
    }

    public function generateAiAssist(AiContentAssistantService $assistant): void
    {
        $this->authorizeAiAssistRequest();
        $this->aiAssistError = null;
        $this->aiAssistDraft = [];
        $this->aiAssistSelected = [];

        try {
            if ($this->aiAssistMode === 'form') {
                $this->validate(['aiAssistInstructions' => ['required', 'string', 'min:3', 'max:1600']]);
                $this->aiAssistDraft = $assistant->generateForm(
                    auth()->user(),
                    $this->aiAssistFormName(),
                    $this->aiAssistFields(),
                    $this->aiAssistContext(),
                    $this->aiAssistInstructions,
                );
                $this->aiAssistSelected = array_fill_keys(array_keys($this->aiAssistDraft), true);
            } else {
                $definition = $this->aiAssistDefinition($this->aiAssistField);
                $allowedActions = array_keys($this->actionLabels($definition['actions'] ?? []));
                $this->validate([
                    'aiAssistAction' => ['required', Rule::in($allowedActions)],
                    'aiAssistApplyMode' => ['required', Rule::in(['append', 'replace'])],
                    'aiAssistInstructions' => ['nullable', 'string', 'max:1200'],
                ]);
                $this->aiAssistDraft = [$this->aiAssistField => $assistant->generateField(
                    auth()->user(),
                    $this->aiAssistFormName(),
                    $this->aiAssistField,
                    $definition,
                    $this->aiAssistAction,
                    $this->{$this->aiAssistField},
                    $this->aiAssistContext(),
                    $this->aiAssistInstructions,
                    $this->aiAssistApplyMode,
                )];
                $this->aiAssistSelected = [$this->aiAssistField => true];
            }
        } catch (AiProviderException $exception) {
            $this->aiAssistError = $exception->getMessage();
        }
    }

    public function applyAiAssistField(string $field): void
    {
        $this->authorizeAiAssistRequest();
        $definition = $this->aiAssistDefinition($field);
        if (! array_key_exists($field, $this->aiAssistDraft)) {
            throw ValidationException::withMessages(['aiAssist' => 'Generate a draft before applying it.']);
        }

        $current = $this->{$field};
        $isAppending = $this->aiAssistMode === 'field'
            && $this->aiAssistApplyMode === 'append'
            && (bool) ($definition['appendable'] ?? false)
            && ! $this->aiAssistValueIsEmpty($current);
        if (! $this->aiAssistValueIsEmpty($current) && ! $isAppending && ! $this->aiAssistConfirmReplace) {
            throw ValidationException::withMessages(['aiAssist' => 'Confirm that you want to replace the existing field content.']);
        }

        $proposal = $this->transformAiAssistValue($field, $this->aiAssistDraft[$field], $definition);
        $value = $isAppending
            ? $this->appendAiAssistValue($current, $proposal, $definition)
            : $proposal;
        $this->{$field} = $value;
        if ($field === 'title'
            && method_exists($this, 'updatedTitle')
            && ! ($this->aiAssistMode === 'form' && ($this->aiAssistSelected['slug'] ?? false))) {
            $this->updatedTitle($value);
        }
        unset($this->aiAssistDraft[$field], $this->aiAssistSelected[$field]);
        $this->dispatch('ai-assist-applied', field: $field, value: $value, richText: (bool) ($definition['rich_text'] ?? false), mode: $isAppending ? 'append' : 'replace');
        $this->dispatch('notify', message: $definition['label'].($isAppending ? ' content added.' : ' draft applied.').' Review it before saving.', type: 'success');

        if ($this->aiAssistMode === 'field') {
            $this->closeAiAssist();
        }
    }

    public function applySelectedAiAssist(): void
    {
        $this->authorizeAiAssistRequest();
        $fields = array_keys(array_filter($this->aiAssistSelected));
        foreach ($fields as $field) {
            $this->aiAssistDefinition($field);
            if (array_key_exists($field, $this->aiAssistDraft)
                && ! $this->aiAssistValueIsEmpty($this->{$field})
                && ! $this->aiAssistConfirmReplace) {
                throw ValidationException::withMessages(['aiAssist' => 'Confirm that you want to replace fields which already contain content.']);
            }
        }
        foreach ($fields as $field) {
            if (array_key_exists($field, $this->aiAssistDraft)) {
                $this->applyAiAssistField($field);
            }
        }
    }

    protected function authorizeAiAssistRequest(): void
    {
        abort_unless(auth()->check(), 403);
    }

    protected function transformAiAssistValue(string $field, mixed $value, array $definition): mixed
    {
        if (($definition['type'] ?? 'string') === 'list') {
            return collect((array) $value)
                ->filter(fn (mixed $item) => is_scalar($item))
                ->map(fn (mixed $item) => Str::limit($this->aiAssistPlainText((string) $item), (int) ($definition['item_max'] ?? 500), ''))
                ->filter()
                ->take((int) ($definition['max_items'] ?? 10))
                ->values()
                ->all();
        }
        if (($definition['format'] ?? null) === 'slug') {
            return Str::slug((string) $value);
        }
        if ($definition['rich_text'] ?? false) {
            return $this->aiAssistMarkdownToHtml((string) $value);
        }

        return $this->aiAssistPlainText((string) $value);
    }

    public function aiAssistDraftIsRichText(string $field): bool
    {
        return (bool) ($this->aiAssistDefinition($field)['rich_text'] ?? false);
    }

    public function aiAssistDraftLabel(string $field): string
    {
        return (string) ($this->aiAssistDefinition($field)['label'] ?? Str::headline($field));
    }

    public function aiAssistPreviewHtml(string $field, mixed $value): string
    {
        if (! $this->aiAssistDraftIsRichText($field) || ! is_scalar($value)) {
            return '';
        }

        return $this->aiAssistMarkdownToHtml((string) $value);
    }

    protected function aiAssistValueIsEmpty(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->flatten()->filter(fn (mixed $item) => filled($item))->isEmpty();
        }

        return blank($value) || trim(strip_tags((string) $value)) === '';
    }

    private function aiAssistPlainText(string $value): string
    {
        $withoutExecutableBlocks = preg_replace('/<(script|style|iframe)\b[^>]*>.*?<\/\1\s*>/is', '', $value) ?? '';

        return trim(strip_tags($withoutExecutableBlocks));
    }

    private function aiAssistMarkdownToHtml(string $value): string
    {
        $markdown = preg_replace('/<(script|style|iframe)\b[^>]*>.*?<\/\1\s*>/is', '', $value) ?? '';
        $html = Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]);

        return trim(Purifier::clean($html, [
            'HTML.Allowed' => 'h2,h3,h4,p,br,strong,em,ul,ol,li,a[href|target],blockquote,pre,code',
            'AutoFormat.RemoveEmpty' => true,
        ]));
    }

    private function appendAiAssistValue(mixed $current, mixed $proposal, array $definition): mixed
    {
        if (($definition['type'] ?? 'string') === 'list') {
            return collect((array) $current)
                ->merge((array) $proposal)
                ->filter(fn (mixed $item) => filled($item))
                ->unique(fn (mixed $item) => mb_strtolower(trim((string) $item)))
                ->take((int) ($definition['max_items'] ?? 10))
                ->values()
                ->all();
        }

        if ($definition['rich_text'] ?? false) {
            return rtrim((string) $current)."\n".$proposal;
        }

        return rtrim((string) $current)."\n\n".$proposal;
    }

    private function aiAssistDefinition(string $field): array
    {
        $definitions = $this->aiAssistFields();
        abort_unless(array_key_exists($field, $definitions), 422);

        return $definitions[$field];
    }

    private function actionLabels(array $actions): array
    {
        $labels = [
            'generate' => 'Generate content',
            'suggest' => 'Suggest alternatives',
            'improve' => 'Improve clarity',
            'rewrite' => 'Rewrite',
            'expand' => 'Expand',
            'shorten' => 'Shorten',
            'grammar' => 'Fix grammar and spelling',
            'professional' => 'Make more professional',
            'seo' => 'Improve for search',
            'continue' => 'Continue writing',
            'add' => 'Add a new section or detail',
        ];

        return collect($actions)->mapWithKeys(fn (string $action) => [$action => $labels[$action] ?? Str::headline($action)])->all();
    }

    private function resetAiAssistState(): void
    {
        $this->aiAssistOpen = false;
        $this->aiAssistInstructions = '';
        $this->aiAssistDraft = [];
        $this->aiAssistSelected = [];
        $this->aiAssistApplyMode = 'replace';
        $this->aiAssistCanAppend = false;
        $this->aiAssistConfirmReplace = false;
        $this->aiAssistError = null;
        $this->resetErrorBag(['aiAssist', 'aiAssistInstructions', 'aiAssistAction']);
    }
}
