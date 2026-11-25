<div class="space-y-6" x-data @learning-profile-step.window="$nextTick(() => $refs.stepHeading.focus())">
    <x-ui.page-header title="Your learning profile" description="Tell us where you want to go. You can update your choices whenever your plans change." />

    @if($saved)
        <x-ui.panel>
            <h2 x-ref="stepHeading" tabindex="-1" class="text-xl font-bold text-content">Your profile is saved</h2>
            <p role="status" class="mt-3 text-content-secondary">Your interests, starting point and learning goal are saved privately to your account.</p>
            <p class="mt-3 break-words font-semibold text-content">{{ $form['goal'] }}</p>
            <p class="mt-2 text-sm text-content-secondary">Your knowledge and experience are self-reported. Saving this profile does not verify a skill or award a qualification.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a class="bk-button bk-button-primary" href="{{ route('student.course-catalog') }}">Explore courses</a>
                <a class="bk-button bk-button-secondary" href="{{ route(auth()->user()->getDashboardRouteName()) }}">Go to my dashboard</a>
                <a class="bk-button bk-button-secondary" href="{{ route('learning.profile') }}">Edit my profile</a>
            </div>
        </x-ui.panel>
    @else
        <nav aria-label="Learning profile steps">
            <ol class="grid grid-cols-3 gap-2 text-sm">
                @foreach([1 => 'Interests', 2 => 'Starting point', 3 => 'Goal & preferences'] as $number => $label)
                    <li @if($step === $number) aria-current="step" @endif class="min-w-0 border-b-2 pb-3 {{ $step === $number ? 'border-action font-bold text-content' : 'border-themed-primary text-content-secondary' }}">
                        <span class="block text-xs">Step {{ $number }}</span>{{ $label }}
                    </li>
                @endforeach
            </ol>
        </nav>
        <x-ui.panel>
            <form wire:submit="{{ $step === 3 ? 'save' : 'next' }}" class="space-y-6" novalidate>
                @if($step === 1)
                    <div>
                        <h2 x-ref="stepHeading" tabindex="-1" class="text-xl font-bold text-content">What would you like to learn?</h2>
                        <p class="mt-2 text-content-secondary">Choose any relevant categories, or add your own interests below. These choices are optional.</p>
                    </div>
                    <div>
                        <x-input-label for="category-search" value="Search course categories" />
                        <x-text-input id="category-search" type="search" wire:model.live.debounce.300ms="categorySearch" maxlength="80" class="mt-1 w-full" aria-describedby="category-search-error" :aria-invalid="$errors->has('categorySearch') ? 'true' : 'false'" />
                        <x-input-error id="category-search-error" :messages="$errors->get('categorySearch')" />
                    </div>
                    <fieldset aria-describedby="category-help category-error">
                        <legend class="font-semibold text-content">Available categories</legend>
                        <p id="category-help" class="mt-1 text-sm text-content-secondary">Select up to 20. Searching keeps your earlier selections.</p>
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            @forelse($categories as $category)
                                <label wire:key="interest-{{ $category->id }}" class="flex min-h-11 items-center gap-3 rounded-lg border border-themed-primary p-3 text-content">
                                    <input type="checkbox" wire:model="form.category_ids" value="{{ $category->id }}" class="rounded border-themed-primary text-action focus:ring-action" />
                                    <span class="min-w-0 break-words">{{ $category->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-content-secondary">No categories match. You can add your interest below.</p>
                            @endforelse
                        </div>
                        <x-input-error id="category-error" :messages="collect([$errors->get('form.category_ids'), $errors->get('form.category_ids.*')])->flatten()->all()" />
                    </fieldset>
                    <div>
                        <x-input-label for="custom-topics" value="Other interests (optional)" />
                        <p id="custom-topics-help" class="mt-1 text-sm text-content-secondary">One topic per line, up to 10 topics of 80 characters each. Adding an interest does not create a course.</p>
                        <textarea id="custom-topics" wire:model="form.custom_topics" rows="4" maxlength="1000" class="bk-input mt-2 w-full" aria-describedby="custom-topics-help custom-topics-error" aria-invalid="{{ $errors->has('form.custom_topics') ? 'true' : 'false' }}"></textarea>
                        <x-input-error id="custom-topics-error" :messages="$errors->get('form.custom_topics')" />
                    </div>
                @elseif($step === 2)
                    <div>
                        <h2 x-ref="stepHeading" tabindex="-1" class="text-xl font-bold text-content">Where are you starting from?</h2>
                        <p class="mt-2 text-content-secondary">Everything here is optional and self-reported. Your assessment results remain separate from these claims.</p>
                    </div>
                    <div>
                        <x-input-label for="starting-level" value="Your current level (optional)" />
                        <select id="starting-level" wire:model="form.starting_level" class="bk-input mt-1 w-full" aria-describedby="starting-level-error" aria-invalid="{{ $errors->has('form.starting_level') ? 'true' : 'false' }}">
                            <option value="">Not sure yet</option>
                            @foreach($levels as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        </select>
                        <x-input-error id="starting-level-error" :messages="$errors->get('form.starting_level')" />
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach(['skills' => ['skills', 'Skills you already use'], 'completed_courses' => ['completed-courses', 'Courses you have completed']] as $field => [$id, $label])
                            <div>
                                <x-input-label :for="$id" :value="$label.' (optional)'" />
                                <p id="{{ $id }}-help" class="mt-1 text-sm text-content-secondary">One per line, up to 20 entries of 100 characters each.</p>
                                <textarea id="{{ $id }}" wire:model="form.{{ $field }}" rows="4" maxlength="2000" class="bk-input mt-2 w-full" aria-describedby="{{ $id }}-help {{ $id }}-error" aria-invalid="{{ $errors->has('form.'.$field) ? 'true' : 'false' }}"></textarea>
                                <x-input-error :id="$id.'-error'" :messages="$errors->get('form.'.$field)" />
                            </div>
                        @endforeach
                        @foreach(['experience' => ['experience', 'Professional or other experience'], 'practical_experience' => ['practical-experience', 'Things you have practised or built']] as $field => [$id, $label])
                            <div>
                                <x-input-label :for="$id" :value="$label.' (optional)'" />
                                <p id="{{ $id }}-help" class="mt-1 text-sm text-content-secondary">Up to 2,000 characters. Leave out sensitive personal information.</p>
                                <textarea id="{{ $id }}" wire:model="form.{{ $field }}" rows="4" maxlength="2000" class="bk-input mt-2 w-full" aria-describedby="{{ $id }}-help {{ $id }}-error" aria-invalid="{{ $errors->has('form.'.$field) ? 'true' : 'false' }}"></textarea>
                                <x-input-error :id="$id.'-error'" :messages="$errors->get('form.'.$field)" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>
                        <h2 x-ref="stepHeading" tabindex="-1" class="text-xl font-bold text-content">What would you like to achieve?</h2>
                        <p class="mt-2 text-content-secondary">Set one learning goal. The preferences below are optional.</p>
                    </div>
                    <div>
                        <x-input-label for="learning-goal" value="Your learning goal (required)" />
                        <textarea id="learning-goal" wire:model="form.goal" rows="3" maxlength="255" class="bk-input mt-1 w-full" aria-describedby="learning-goal-help learning-goal-error" aria-invalid="{{ $errors->has('form.goal') ? 'true' : 'false' }}" required></textarea>
                        <p id="learning-goal-help" class="mt-1 text-sm text-content-secondary">For example, learn accounting for a small business or develop professional communication skills. Up to 255 characters.</p>
                        <x-input-error id="learning-goal-error" :messages="$errors->get('form.goal')" />
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="pace" value="Learning pace (optional)" />
                            <select id="pace" wire:model="form.pace" class="bk-input mt-1 w-full" aria-describedby="pace-error" aria-invalid="{{ $errors->has('form.pace') ? 'true' : 'false' }}">
                                <option value="">No preference</option>
                                @foreach($paces as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                            </select>
                            <x-input-error id="pace-error" :messages="$errors->get('form.pace')" />
                        </div>
                        @foreach(['minutes_per_week' => ['minutes-per-week', 'Minutes available per week', 15, 10080], 'target_weeks' => ['target-weeks', 'Desired timeline in weeks', 1, 260]] as $field => [$id, $label, $min, $max])
                            <div>
                                <x-input-label :for="$id" :value="$label.' (optional)'" />
                                <x-text-input :id="$id" type="number" wire:model="form.{{ $field }}" :min="$min" :max="$max" step="1" class="mt-1 w-full" :aria-describedby="$id.'-help '.$id.'-error'" :aria-invalid="$errors->has('form.'.$field) ? 'true' : 'false'" />
                                <p id="{{ $id }}-help" class="mt-1 text-sm text-content-secondary">From {{ $min }} to {{ number_format($max) }}. This is a preference, not a completion promise.</p>
                                <x-input-error :id="$id.'-error'" :messages="$errors->get('form.'.$field)" />
                            </div>
                        @endforeach
                        <div>
                            <x-input-label for="language" value="Preferred language (optional)" />
                            <x-text-input id="language" wire:model="form.language" maxlength="80" class="mt-1 w-full" aria-describedby="language-help language-error" :aria-invalid="$errors->has('form.language') ? 'true' : 'false'" />
                            <p id="language-help" class="mt-1 text-sm text-content-secondary">This records your preference; course language availability varies.</p>
                            <x-input-error id="language-error" :messages="$errors->get('form.language')" />
                        </div>
                    </div>
                    <fieldset aria-describedby="formats-error">
                        <legend class="font-semibold text-content">Preferred formats (optional)</legend>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @foreach($formats as $value => $label)
                                <label class="flex min-h-11 items-center gap-2 rounded-lg border border-themed-primary px-3 py-2 text-content">
                                    <input type="checkbox" wire:model="form.formats" value="{{ $value }}" class="rounded border-themed-primary text-action focus:ring-action" />{{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <x-input-error id="formats-error" :messages="collect([$errors->get('form.formats'), $errors->get('form.formats.*')])->flatten()->all()" />
                    </fieldset>
                    <p class="text-sm text-content-secondary">Your saved preferences help describe the learning experience you want. Available courses may use different formats and languages.</p>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-themed-primary pt-5">
                    <div class="flex flex-wrap gap-3">
                        @if($step > 1)<x-secondary-button wire:click="previous" wire:loading.attr="disabled">Back</x-secondary-button>@endif
                        <x-primary-button wire:loading.attr="disabled">{{ $step === 3 ? 'Save learning profile' : 'Continue' }}</x-primary-button>
                    </div>
                    @if(!$completed)<x-secondary-button wire:click="skip" wire:loading.attr="disabled">Set up later</x-secondary-button>@endif
                </div>
                <p wire:loading role="status" class="text-sm text-content-secondary">Updating your learning profile…</p>
            </form>
        </x-ui.panel>
    @endif
</div>
