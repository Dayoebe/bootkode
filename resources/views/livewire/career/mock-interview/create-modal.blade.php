@if($showCreateForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Create form" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showCreateForm&#x27;, false)">
        <div class="bg-themed-secondary border border-themed-primary rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden my-8">
            <div class="flex justify-between items-center p-6 border-b border-themed-primary bg-surface-muted  ">
                <h2 class="text-2xl font-bold text-themed-primary">
                    {{ $editingInterviewId ? 'Edit Interview' : 'Create New Interview' }}
                </h2>
                <button wire:click="resetForm"
                    class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit.prevent="createInterview" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Interview Title -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Interview Title *</label>
                            <input aria-label="e.g., Frontend Developer Technical Interview" wire:model="title" type="text"
                                placeholder="e.g., Frontend Developer Technical Interview"
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type & Format -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Interview Type *</label>
                                <select aria-label="Type" wire:model.live="type"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                    <option value="technical">Technical</option>
                                    <option value="behavioral">Behavioral</option>
                                    <option value="case_study">Case Study</option>
                                    <option value="system_design">System Design</option>
                                    <option value="coding">Coding</option>
                                    <option value="hr">HR</option>
                                    <option value="custom">Custom</option>
                                </select>
                                @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Format *</label>
                                <select aria-label="Format" wire:model="format"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                    <option value="text">Text Only</option>
                                    <option value="voice">Voice Recording</option>
                                    <option value="video">Video Recording</option>
                                    <option value="mixed">Mixed Format</option>
                                </select>
                                @error('format') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Difficulty & Duration -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Difficulty Level *</label>
                                <select aria-label="Difficulty level" wire:model="difficulty_level"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                                @error('difficulty_level') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Duration (minutes) *</label>
                                <input aria-label="Estimated duration minutes" wire:model="estimated_duration_minutes" type="number"
                                    min="15" max="180" step="15"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                @error('estimated_duration_minutes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Job Details -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Job Role</label>
                                <input aria-label="e.g., Senior Frontend Developer" wire:model="job_role" type="text"
                                    placeholder="e.g., Senior Frontend Developer"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                @error('job_role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Industry</label>
                                    <input aria-label="e.g., Technology" wire:model="industry" type="text"
                                        placeholder="e.g., Technology"
                                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                    @error('industry') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Company Type</label>
                                    <input aria-label="e.g., Startup" wire:model="company_type" type="text"
                                        placeholder="e.g., Startup"
                                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                    @error('company_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Scheduling -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Schedule For (Optional)</label>
                            <input aria-label="Scheduled at" wire:model="scheduled_at" type="datetime-local"
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                            @error('scheduled_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Description</label>
                            <textarea aria-label="Describe the interview focus and objectives" wire:model="description" rows="4"
                                placeholder="Describe the interview focus and objectives..."
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all resize-none"></textarea>
                            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Course Association -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Associated Course (Optional)</label>
                            <select aria-label="Course id" wire:model="course_id"
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                <option value="">No Course Association</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Premium Features -->
                        @if($aiAnalysisEnabled)
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-4">Premium Features</label>
                                <div class="space-y-3 bg-themed-tertiary p-4 rounded-lg">
                                    @foreach(['ai_feedback', 'video_recording', 'detailed_analytics', 'custom_questions', 'unlimited_retakes'] as $feature)
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                wire:click="togglePremiumFeature('{{ $feature }}')"
                                                {{ in_array($feature, $premium_features) ? 'checked' : '' }}
                                                class="w-5 h-5 text-brand bg-themed-secondary border-themed-primary rounded focus:ring-brand transition-all">
                                            <span class="ml-3 text-sm text-themed-primary">
                                                {{ str_replace('_', ' ', ucwords($feature)) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Custom Questions -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Custom Questions</label>

                            <!-- Add Question Form -->
                            <div class="flex space-x-2 mb-4">
                                <input aria-label="Enter your custom question" wire:model="newQuestion" type="text"
                                    placeholder="Enter your custom question..."
                                    class="flex-1 px-4 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                <select aria-label="Question type" wire:model="questionType"
                                    class="px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-brand focus:border-transparent transition-all">
                                    <option value="behavioral">Behavioral</option>
                                    <option value="technical">Technical</option>
                                    <option value="situational">Situational</option>
                                </select>
                                <button type="button" wire:click="addCustomQuestion"
                                    class="bg-action hover:bg-action/90 text-action-foreground px-4 py-2 rounded-lg transition-colors">
                                    Add
                                </button>
                            </div>

                            <!-- Custom Questions List -->
                            @if(count($custom_questions) > 0)
                                <div class="space-y-2 max-h-40 overflow-y-auto bg-themed-tertiary p-3 rounded-lg">
                                    @foreach($custom_questions as $index => $question)
                                        <div class="flex items-center justify-between p-2 bg-themed-secondary rounded border border-themed-primary">
                                            <div class="flex-1">
                                                <p class="text-sm text-themed-primary">{{ $question['question'] }}</p>
                                                <span class="text-xs text-themed-secondary">{{ ucfirst($question['type']) }}</span>
                                            </div>
                                            <button type="button"
                                                wire:click="removeCustomQuestion({{ $index }})"
                                                class="text-red-600 hover:text-red-700 p-1 transition-colors" aria-label="Close">
                                                <i aria-hidden="true" class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-themed-primary">
                    <button type="button" wire:click="resetForm"
                        class="px-6 py-3 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-action   text-action-foreground px-8 py-3 rounded-lg hover:shadow-sm transition-all font-semibold">
                        <span wire:loading.remove wire:target="createInterview">
                            {{ $editingInterviewId ? 'Update Interview' : 'Create Interview' }}
                        </span>
                        <span wire:loading wire:target="createInterview">
                            <i aria-hidden="true" class="fas fa-spinner fa-spin mr-2"></i> Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
