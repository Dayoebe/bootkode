<div class="space-y-6 {{ $standalone ? 'mx-auto max-w-5xl p-4 sm:p-6' : '' }}">
    @if($standalone)
        <x-ui.page-header title="Lesson assessments" :description="$lesson->title" />
    @else
        <h2 class="text-xl font-semibold text-content">Lesson assessments</h2>
        <a class="bk-button bk-button-secondary" href="{{ route('lesson.assessments', $lesson) }}">Open assessment workspace</a>
    @endif
    <p role="status" aria-live="polite" class="text-sm text-content-muted">{{ $message }}</p>
    <x-input-error :messages="$errors->get('submission')" role="alert" />
    @if($results)
        <x-ui.panel title="Recorded result">
            <p class="text-xl font-semibold text-content">{{ ($results['pending_review'] ?? false) ? 'Pending instructor review' : ($results['passed'] ? 'Passed' : 'Not passed') }}</p>
            @unless($results['pending_review'] ?? false)<p class="mt-2 text-content">{{ $results['percentage'] }}% · {{ $results['total_points'] }} / {{ $results['max_points'] }} points</p>@endunless
            <p class="my-3 text-sm text-content-muted">Attempt {{ $results['attempt_number'] }}. Submitted attempts are retained.</p>
            @include('livewire.cbt.partials.attempt-responses')
            <x-secondary-button class="mt-4" wire:click="backToAssessmentList">Back to assessments</x-secondary-button>
        </x-ui.panel>
    @elseif($examSessionId)
        <x-ui.panel title="Your attempt">
            <p class="text-sm text-content-muted" x-data="{ deadline: @js($deadline), remaining: '', timer: null, update() { const s = Math.max(0, Math.ceil((Date.parse(this.deadline) - Date.now()) / 1000)); this.remaining = Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0'); }, init() { this.update(); this.timer = setInterval(() => this.update(), 1000); }, destroy() { clearInterval(this.timer); } }">Time remaining: <span x-text="remaining"></span></p>
            <p class="mt-2 text-sm text-content-muted">The server enforces the deadline. On expiry, submit to record the answers saved before the deadline. Written answers require instructor review.</p>
            <form wire:submit="submitAssessment" class="mt-6 space-y-6">
                @foreach($questions as $index => $question)
                    <fieldset wire:key="question-{{ $question['id'] }}" class="min-w-0 rounded-lg border border-line p-4">
                        <legend class="max-w-full break-words px-1 font-semibold text-content">{{ $index + 1 }}. {{ strip_tags($question['question_text']) }} <span class="text-sm text-content-muted">({{ $question['points'] }} points)</span></legend>
                        @if(in_array($question['question_type'], ['multiple_choice', 'true_false']))
                            @if($question['allows_multiple'])<p class="mb-2 text-sm text-content-muted">Select all correct options. The complete set is required for credit.</p>@endif
                            @foreach($question['options'] as $optionIndex => $option)
                                <label class="flex min-h-11 items-start gap-3 py-2 text-content"><input class="mt-1 focus:ring-2 focus:ring-brand" type="{{ $question['allows_multiple'] ? 'checkbox' : 'radio' }}" wire:model.live="answers.{{ $question['id'] }}" value="{{ $optionIndex }}" name="answer-{{ $question['id'] }}" /><span class="min-w-0 break-words">{{ strip_tags($option) }}</span></label>
                            @endforeach
                        @elseif(isset($question['discussion']))
                            @include('livewire.student-management.course-view.qna-response')
                        @elseif(in_array($question['question_type'], ['matching', 'ordering', 'drag_drop']))
                            @include('livewire.student-management.course-view.structured-question')
                        @else
                            @if(in_array($question['question_type'], ['project_criteria', 'assignment_question']) && !isset($question['submission']))<p class="mb-3 text-sm text-content-muted">This older attempt retains its original written-response format. File upload permissions are not inferred from later question changes.</p>@endif
                            @if(!empty($question['instructions']))<p class="mb-3 whitespace-pre-wrap break-words text-content-muted">{{ $question['instructions'] }}</p>@endif
                            <label class="sr-only" for="response-{{ $question['id'] }}">Your answer to question {{ $index + 1 }}</label>
                            <textarea id="response-{{ $question['id'] }}" wire:model.live.debounce.700ms="answers.{{ $question['id'] }}" maxlength="10000" rows="6" class="w-full rounded-lg border-line bg-surface text-content focus:ring-brand" aria-describedby="response-help-{{ $question['id'] }} response-error-{{ $question['id'] }}" aria-invalid="{{ $errors->has('answers.'.$question['id']) ? 'true' : 'false' }}"></textarea>
                            <p id="response-help-{{ $question['id'] }}" class="text-sm text-content-muted">Up to 10,000 characters.@if($question['submission']['word_limit'] ?? null) Recorded word limit: {{ $question['submission']['word_limit'] }}.@endif Your instructor will review this response.@if($question['submission']['required'] ?? false) A response is required.@endif</p>
                            <x-input-error id="response-error-{{ $question['id'] }}" :messages="$errors->get('answers.'.$question['id'])" />
                            @if($question['submission']['enabled'] ?? false)
                                @include('livewire.student-management.course-view.submission-files')
                            @endif
                        @endif
                    </fieldset>
                @endforeach
                <div class="flex flex-wrap gap-3"><x-secondary-button wire:click="$set('showSubmitModal', true)">Review submission</x-secondary-button><x-secondary-button wire:click="backToAssessmentList">Save and leave</x-secondary-button></div>
            </form>
        </x-ui.panel>
    @else
        @forelse($assessments as $assessment)
            @php($status = $statuses[$assessment->id])
            @php($configured = is_numeric($assessment->pass_percentage) && $assessment->pass_percentage >= 1 && $assessment->pass_percentage <= 100)
            <x-ui.panel :title="$assessment->title">
                <p class="break-words text-content-muted">{{ strip_tags($assessment->description ?? '') }}</p>
                <p class="my-3 text-sm text-content-muted">{{ $assessment->estimated_duration_minutes }} minutes · Pass mark {{ $assessment->pass_percentage }}% · {{ $assessment->formatted_max_attempts }}</p>
                @unless($configured)<p class="mb-3 text-content-muted">Your instructor must configure a pass mark before this assessment can start.</p>@endunless
                @if($status)<p class="mb-3 text-content">{{ ($status['pending_review'] ?? false) ? 'Pending instructor review' : ($status['passed'] ? 'Passed' : 'Not passed') }}</p>@endif
                <div class="flex flex-wrap gap-3">
                    @if(in_array($assessment->id, $activeIds))<x-primary-button wire:click="startAssessment({{ $assessment->id }})" wire:loading.attr="disabled">Resume attempt</x-primary-button>
                    @elseif($configured && !($status['pending_review'] ?? false) && $assessment->canUserTakeAssessment(auth()->id())[0])<x-primary-button wire:click="startAssessment({{ $assessment->id }})" wire:loading.attr="disabled">{{ $status ? 'Start another attempt' : 'Start assessment' }}</x-primary-button>@endif
                    @if($assessment->type === 'qna')<a class="bk-button bk-button-secondary" href="{{ route('assessment.discussion', $assessment) }}">Q&amp;A participation</a>@endif
                    @if($status)<x-secondary-button wire:click="showAssessmentResults({{ $assessment->id }})">View result and feedback</x-secondary-button>@endif
                </div>
            </x-ui.panel>
        @empty<x-ui.empty-state title="No assessments for this lesson" description="Published lesson assessments will appear here." />@endforelse
    @endif
    <x-modal name="lesson-assessment-submit" model="showSubmitModal" label="Submit lesson assessment">
        <div class="space-y-4 p-6"><h2 class="text-xl font-semibold text-content">Submit this attempt?</h2><p class="text-content-muted">You cannot change submitted answers. Written responses stay pending until your instructor has reviewed every answer.</p><div class="flex flex-wrap gap-3"><x-primary-button wire:click="submitAssessment" wire:loading.attr="disabled">Submit assessment</x-primary-button><x-secondary-button wire:click="$set('showSubmitModal', false)">Keep working</x-secondary-button></div></div>
    </x-modal>
</div>
