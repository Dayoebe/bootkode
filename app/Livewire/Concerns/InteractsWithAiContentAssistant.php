<?php

namespace App\Livewire\Concerns;

use App\Exceptions\AiProviderException;
use App\Services\Ai\AiContentAssistantService;
use App\Services\Ai\AiManager;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        if (! $this->aiAssistValueIsEmpty($current) && ! $this->aiAssistConfirmReplace) {
            throw ValidationException::withMessages(['aiAssist' => 'Confirm that you want to replace the existing field content.']);
        }

        $value = $this->transformAiAssistValue($field, $this->aiAssistDraft[$field], $definition);
        $this->{$field} = $value;
        unset($this->aiAssistDraft[$field], $this->aiAssistSelected[$field]);
        $this->dispatch('ai-assist-applied', field: $field, value: $value, richText: (bool) ($definition['rich_text'] ?? false));
        $this->dispatch('notify', message: $definition['label'].' draft applied. Review it before saving.', type: 'success');

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
            return array_values(array_filter((array) $value, fn (mixed $item) => filled($item)));
        }
        if (($definition['format'] ?? null) === 'slug') {
            return Str::slug((string) $value);
        }

        return $this->aiAssistPlainText((string) $value);
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
        ];

        return collect($actions)->mapWithKeys(fn (string $action) => [$action => $labels[$action] ?? Str::headline($action)])->all();
    }

    private function resetAiAssistState(): void
    {
        $this->aiAssistOpen = false;
        $this->aiAssistInstructions = '';
        $this->aiAssistDraft = [];
        $this->aiAssistSelected = [];
        $this->aiAssistConfirmReplace = false;
        $this->aiAssistError = null;
        $this->resetErrorBag(['aiAssist', 'aiAssistInstructions', 'aiAssistAction']);
    }
}
