<div>
    <!-- Session Creation/Edit Modal -->
    @if($showSessionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 transition-colors duration-300">
            <div class="bg-themed-secondary rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-themed-primary transition-colors duration-300">
                <div class="p-6 border-b border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">
                            @if($modalType === 'create')
                                Schedule New Session
                            @elseif($modalType === 'edit')
                                Edit Session
                            @else
                                Session Details
                            @endif
                        </h3>
                        <button wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                @if($modalType === 'view')
                    <!-- View Session Details -->
                    <div class="p-6 space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Session Title</h4>
                                <p class="text-themed-secondary transition-colors duration-300">{{ $currentSession->title }}</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Type</h4>
                                <span class="bg-accent-primary/10 text-accent-primary px-3 py-1 rounded-full text-sm border border-accent-primary/30 transition-colors duration-300">
                                    {{ ucwords(str_replace('_', ' ', $currentSession->type)) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Scheduled Date</h4>
                                <p class="text-themed-secondary transition-colors duration-300">{{ $currentSession->scheduled_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Duration</h4>
                                <p class="text-themed-secondary transition-colors duration-300">{{ $currentSession->duration_minutes ?? 60 }} minutes</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Description</h4>
                            <p class="text-themed-secondary transition-colors duration-300">{{ $currentSession->description }}</p>
                        </div>

                        @if($currentSession->agenda)
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Agenda</h4>
                                <p class="text-themed-secondary whitespace-pre-line transition-colors duration-300">{{ $currentSession->agenda }}</p>
                            </div>
                        @endif

                        @if($currentSession->status === 'completed')
                            <div class="border-t border-themed-primary pt-6 transition-colors duration-300">
                                <h4 class="font-semibold text-themed-primary mb-4 transition-colors duration-300">Session Completed</h4>
                                
                                @if($currentSession->session_notes)
                                    <div class="mb-4">
                                        <h5 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Session Notes</h5>
                                        <p class="text-themed-secondary whitespace-pre-line transition-colors duration-300">{{ $currentSession->session_notes }}</p>
                                    </div>
                                @endif

                                @if($currentSession->action_items && count($currentSession->action_items) > 0)
                                    <div class="mb-4">
                                        <h5 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Action Items</h5>
                                        <ul class="list-disc list-inside text-themed-secondary space-y-1 transition-colors duration-300">
                                            @foreach($currentSession->action_items as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($currentSession->mentor_rating || $currentSession->mentee_rating)
                                    <div class="grid md:grid-cols-2 gap-4">
                                        @if($currentSession->mentor_rating)
                                            <div>
                                                <h5 class="font-medium text-themed-primary mb-1 transition-colors duration-300">Mentor Rating</h5>
                                                <div class="text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $currentSession->mentor_rating ? '' : 'text-themed-tertiary' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endif

                                        @if($currentSession->mentee_rating)
                                            <div>
                                                <h5 class="font-medium text-themed-primary mb-1 transition-colors duration-300">Mentee Rating</h5>
                                                <div class="text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= $currentSession->mentee_rating ? '' : 'text-themed-tertiary' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                            <button wire:click="closeModal" 
                                class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                                Close
                            </button>
                            @if($currentSession->status === 'scheduled' && auth()->id() === $currentSession->mentorship->mentor_id)
                                <button wire:click="completeSession({{ $currentSession->id }})"
                                    class="px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                                    Complete Session
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Create/Edit Session Form -->
                    <form wire:submit="submitSession" class="p-6 space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Session Title</label>
                                <input wire:model="sessionTitle" type="text" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="e.g., React Fundamentals Discussion">
                                @error('sessionTitle') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Session Type</label>
                                <select wire:model="sessionType" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                    <option value="general">General Session</option>
                                    <option value="code_review">Code Review</option>
                                    <option value="project_guidance">Project Guidance</option>
                                    <option value="career_advice">Career Advice</option>
                                    <option value="mock_interview">Mock Interview</option>
                                </select>
                                @error('sessionType') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Description</label>
                            <textarea wire:model="sessionDescription" rows="4" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="Describe what will be covered in this session..."></textarea>
                            @error('sessionDescription') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Scheduled Date & Time</label>
                                <input wire:model="scheduledAt" type="datetime-local" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                @error('scheduledAt') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Duration (minutes)</label>
                                <select wire:model="duration" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                    <option value="30">30 minutes</option>
                                    <option value="45">45 minutes</option>
                                    <option value="60">1 hour</option>
                                    <option value="90">1.5 hours</option>
                                    <option value="120">2 hours</option>
                                </select>
                                @error('duration') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Agenda (Optional)</label>
                            <textarea wire:model="agenda" rows="3" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="Session agenda or topics to discuss..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Meeting Link (Optional)</label>
                            <input wire:model="meetingLink" type="url" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="https://zoom.us/j/...">
                        </div>

                        <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                            <button type="button" wire:click="closeModal" 
                                class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-6 py-3 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                                {{ $modalType === 'create' ? 'Schedule Session' : 'Update Session' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- Session Completion Modal -->
    @if($showCompletionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 transition-colors duration-300">
            <div class="bg-themed-secondary rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-themed-primary transition-colors duration-300">
                <div class="p-6 border-b border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Complete Session</h3>
                        <button wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit="submitSessionCompletion" class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Session Notes *</label>
                        <textarea wire:model="sessionNotes" rows="5" 
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                            placeholder="What was discussed? Key topics covered? Any insights shared?"></textarea>
                        @error('sessionNotes') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Action Items -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Action Items</label>
                        @foreach($actionItems as $index => $item)
                            <div class="flex mb-2">
                                <input wire:model="actionItems.{{ $index }}" type="text" 
                                    class="flex-1 px-4 py-2 border border-themed-primary rounded-l-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="Next steps or tasks for the mentee...">
                                <button type="button" wire:click="removeActionItem({{ $index }})" 
                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-lg transition-colors">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        @endforeach
                        <button type="button" wire:click="addActionItem" 
                            class="mt-2 px-4 py-2 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Action Item
                        </button>
                    </div>

                    <!-- Feedback -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Mentor Feedback</label>
                            <textarea wire:model="mentorFeedback" rows="3" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="Feedback for the mentee..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Mentee Feedback</label>
                            <textarea wire:model="menteeFeedback" rows="3" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="Feedback from the mentee..."></textarea>
                        </div>
                    </div>

                    <!-- Ratings -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Rate the Mentor (1-5)</label>
                            <select wire:model="mentorRating" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">No rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Rate the Mentee (1-5)</label>
                            <select wire:model="menteeRating" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">No rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                    </div>

                    <!-- File Attachments -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Session Attachments (Optional)</label>
                        <input wire:model="attachments" type="file" multiple 
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                        <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">Upload any relevant files, screenshots, or documents</p>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                        <button type="button" wire:click="closeModal" 
                            class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                            Complete Session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Code Review Modal -->
    @if($showCodeReviewModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 transition-colors duration-300">
            <div class="bg-themed-secondary rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto border border-themed-primary transition-colors duration-300">
                <div class="p-6 border-b border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">
                            @if($modalType === 'create')
                                Request Code Review
                            @elseif($modalType === 'edit')
                                Edit Code Review
                            @else
                                Code Review Details
                            @endif
                        </h3>
                        <button wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                @if($modalType === 'view')
                    <!-- View Code Review Details -->
                    <div class="p-6 space-y-6">
                        <!-- Header Info -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Title</h4>
                                <p class="text-themed-secondary transition-colors duration-300">{{ $currentCodeReview->title }}</p>
                            </div>
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Status & Priority</h4>
                                <div class="flex space-x-2">
                                    <span class="bg-{{ $currentCodeReview->status === 'pending' ? 'yellow' : ($currentCodeReview->status === 'completed' ? 'green' : 'blue') }}-100 dark:bg-{{ $currentCodeReview->status === 'pending' ? 'yellow' : ($currentCodeReview->status === 'completed' ? 'green' : 'blue') }}-900/30 text-{{ $currentCodeReview->status === 'pending' ? 'yellow' : ($currentCodeReview->status === 'completed' ? 'green' : 'blue') }}-800 dark:text-{{ $currentCodeReview->status === 'pending' ? 'yellow' : ($currentCodeReview->status === 'completed' ? 'green' : 'blue') }}-300 px-3 py-1 rounded-full text-sm border border-{{ $currentCodeReview->status === 'pending' ? 'yellow' : ($currentCodeReview->status === 'completed' ? 'green' : 'blue') }}-200 dark:border-{{ $currentCodeReview->status === 'pending' ? 'yellow' : ($currentCodeReview->status === 'completed' ? 'green' : 'blue') }}-800 transition-colors duration-300">
                                        {{ ucfirst($currentCodeReview->status) }}
                                    </span>
                                    <span class="bg-themed-tertiary text-themed-secondary px-3 py-1 rounded-full text-sm border border-themed-primary transition-colors duration-300">
                                        {{ ucfirst($currentCodeReview->priority) }} Priority
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Description</h4>
                            <p class="text-themed-secondary whitespace-pre-line transition-colors duration-300">{{ $currentCodeReview->description }}</p>
                        </div>

                        <!-- Technologies -->
                        @if($currentCodeReview->technologies && count($currentCodeReview->technologies) > 0)
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Technologies</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($currentCodeReview->technologies as $tech)
                                        <span class="bg-accent-primary/10 text-accent-primary px-3 py-1 rounded-full text-sm border border-accent-primary/30 transition-colors duration-300">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Repository Info -->
                        @if($currentCodeReview->repository_url || $currentCodeReview->pull_request_url)
                            <div class="grid md:grid-cols-2 gap-6">
                                @if($currentCodeReview->repository_url)
                                    <div>
                                        <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Repository</h4>
                                        <a href="{{ $currentCodeReview->repository_url }}" target="_blank" 
                                           class="text-accent-primary hover:text-accent-secondary break-all transition-colors">
                                            {{ $currentCodeReview->repository_url }}
                                            <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    </div>
                                @endif

                                @if($currentCodeReview->pull_request_url)
                                    <div>
                                        <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Pull Request</h4>
                                        <a href="{{ $currentCodeReview->pull_request_url }}" target="_blank" 
                                           class="text-accent-primary hover:text-accent-secondary break-all transition-colors">
                                            {{ $currentCodeReview->pull_request_url }}
                                            <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Specific Questions -->
                        @if($currentCodeReview->specific_questions)
                            <div>
                                <h4 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">Specific Questions</h4>
                                <p class="text-themed-secondary whitespace-pre-line transition-colors duration-300">{{ $currentCodeReview->specific_questions }}</p>
                            </div>
                        @endif

                        <!-- Review Feedback (if completed) -->
                        @if($currentCodeReview->status === 'completed' && $currentCodeReview->review_feedback)
                            <div class="border-t border-themed-primary pt-6 transition-colors duration-300">
                                <h4 class="font-semibold text-themed-primary mb-4 transition-colors duration-300">Review Feedback</h4>
                                
                                <div class="bg-green-100/50 dark:bg-green-900/30 border border-green-200/50 dark:border-green-800 rounded-lg p-6 mb-4 transition-colors duration-300">
                                    <p class="text-themed-secondary whitespace-pre-line transition-colors duration-300">{{ $currentCodeReview->review_feedback }}</p>
                                </div>

                                @if($currentCodeReview->suggestions && count($currentCodeReview->suggestions) > 0)
                                    <div class="mb-4">
                                        <h5 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Suggestions</h5>
                                        <ul class="list-disc list-inside text-themed-secondary space-y-1 transition-colors duration-300">
                                            @foreach($currentCodeReview->suggestions as $suggestion)
                                                <li>{{ $suggestion }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($currentCodeReview->code_quality_score)
                                    <div class="mb-4">
                                        <h5 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Code Quality Score</h5>
                                        <div class="flex items-center">
                                            <div class="text-2xl font-bold text-accent-primary transition-colors duration-300">{{ $currentCodeReview->code_quality_score }}/10</div>
                                            <div class="ml-4 flex-1 bg-themed-tertiary rounded-full h-3 transition-colors duration-300">
                                                <div class="bg-accent-primary h-3 rounded-full transition-colors duration-300" style="width: {{ ($currentCodeReview->code_quality_score / 10) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($currentCodeReview->improvement_areas && count($currentCodeReview->improvement_areas) > 0)
                                    <div>
                                        <h5 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Areas for Improvement</h5>
                                        <ul class="list-disc list-inside text-themed-secondary space-y-1 transition-colors duration-300">
                                            @foreach($currentCodeReview->improvement_areas as $area)
                                                <li>{{ $area }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                            <button wire:click="closeModal" 
                                class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                                Close
                            </button>
                            @if($currentCodeReview->status === 'pending' && auth()->id() === $currentCodeReview->mentorship->mentor_id)
                                <button wire:click="startCodeReview({{ $currentCodeReview->id }})"
                                    class="px-6 py-3 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                                    Start Review
                                </button>
                            @elseif($currentCodeReview->status === 'in_review' && auth()->id() === $currentCodeReview->reviewed_by)
                                <button onclick="document.getElementById('feedback-form').style.display = 'block'"
                                    class="px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                                    Provide Feedback
                                </button>
                            @endif
                        </div>

                        <!-- Hidden Feedback Form -->
                        @if($currentCodeReview->status === 'in_review' && auth()->id() === $currentCodeReview->reviewed_by)
                            <div id="feedback-form" class="hidden border-t border-themed-primary pt-6 transition-colors duration-300">
                                <form wire:submit="submitCodeReviewFeedback" class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Review Feedback *</label>
                                        <textarea wire:model="reviewFeedback" rows="6" 
                                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                            placeholder="Provide detailed feedback on the code..."></textarea>
                                        @error('reviewFeedback') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Suggestions -->
                                    <div>
                                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Suggestions</label>
                                        @foreach($suggestions as $index => $suggestion)
                                            <div class="flex mb-2">
                                                <textarea wire:model="suggestions.{{ $index }}" rows="2"
                                                    class="flex-1 px-4 py-2 border border-themed-primary rounded-l-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                                    placeholder="Suggestion for improvement..."></textarea>
                                                <button type="button" wire:click="removeSuggestion({{ $index }})" 
                                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-lg transition-colors">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addSuggestion" 
                                            class="mt-2 px-4 py-2 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                                            <i class="fas fa-plus mr-2"></i>Add Suggestion
                                        </button>
                                    </div>

                                    <!-- Code Quality Score -->
                                    <div>
                                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Code Quality Score (1-10)</label>
                                        <select wire:model="codeQualityScore" 
                                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                            <option value="">No score</option>
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ $i <= 3 ? 'Needs Work' : ($i <= 6 ? 'Good' : ($i <= 8 ? 'Very Good' : 'Excellent')) }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Improvement Areas -->
                                    <div>
                                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Areas for Improvement</label>
                                        @foreach($improvementAreas as $index => $area)
                                            <div class="flex mb-2">
                                                <input wire:model="improvementAreas.{{ $index }}" type="text" 
                                                    class="flex-1 px-4 py-2 border border-themed-primary rounded-l-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                                    placeholder="Area that needs improvement...">
                                                <button type="button" wire:click="removeImprovementArea({{ $index }})" 
                                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-lg transition-colors">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                        <button type="button" wire:click="addImprovementArea" 
                                            class="mt-2 px-4 py-2 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                                            <i class="fas fa-plus mr-2"></i>Add Area
                                        </button>
                                    </div>

                                    <div class="flex justify-end space-x-4">
                                        <button type="button" onclick="document.getElementById('feedback-form').style.display = 'none'"
                                            class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                                            Cancel
                                        </button>
                                        <button type="submit" 
                                            class="px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                                            Submit Review
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Create/Edit Code Review Form -->
                    <form wire:submit="submitCodeReview" class="p-6 space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Review Title</label>
                                <input wire:model="reviewTitle" type="text" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="e.g., React Component Structure Review">
                                @error('reviewTitle') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Priority</label>
                                <select wire:model="priority" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                    <option value="low">Low Priority</option>
                                    <option value="medium">Medium Priority</option>
                                    <option value="high">High Priority</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                @error('priority') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Description</label>
                            <textarea wire:model="reviewDescription" rows="4" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="Describe what needs to be reviewed and any specific concerns..."></textarea>
                            @error('reviewDescription') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Technologies -->
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Technologies Used</label>
                            @foreach($technologies as $index => $technology)
                                <div class="flex mb-2">
                                    <input wire:model="technologies.{{ $index }}" type="text" 
                                        class="flex-1 px-4 py-2 border border-themed-primary rounded-l-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                        placeholder="e.g., React, Node.js, Python">
                                    <button type="button" wire:click="removeTechnology({{ $index }})" 
                                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-lg transition-colors">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addTechnology" 
                                class="mt-2 px-4 py-2 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Technology
                            </button>
                            @error('technologies') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Repository Information -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Repository URL</label>
                                <input wire:model="repositoryUrl" type="url" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="https://github.com/username/repo">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Branch Name</label>
                                <input wire:model="branchName" type="text" 
                                    class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="main">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Pull Request URL (Optional)</label>
                            <input wire:model="pullRequestUrl" type="url" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="https://github.com/username/repo/pull/123">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Specific Questions (Optional)</label>
                            <textarea wire:model="specificQuestions" rows="3" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="Any specific questions or areas you'd like the reviewer to focus on..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                            <button type="button" wire:click="closeModal" 
                                class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                                Cancel
                            </button>
                            <button type="submit" 
                                class="px-6 py-3 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                                {{ $modalType === 'create' ? 'Submit Review Request' : 'Update Review Request' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <!-- Review/Rating Modal -->
    @if($showReviewModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 transition-colors duration-300">
            <div class="bg-themed-secondary rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto border border-themed-primary transition-colors duration-300">
                <div class="p-6 border-b border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Leave a Review</h3>
                        <button wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit="submitReview" class="p-6 space-y-6">
                    <!-- Overall Rating -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Overall Rating *</label>
                        <select wire:model="overallRating" 
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                            <option value="">Select rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                        @error('overallRating') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Detailed Ratings -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Communication</label>
                            <select wire:model="communicationRating" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">No rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Expertise</label>
                            <select wire:model="expertiseRating" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">No rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Helpfulness</label>
                            <select wire:model="helpfulnessRating" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">No rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Professionalism</label>
                            <select wire:model="professionalismRating" 
                                class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">No rating</option>
                                <option value="1">1 - Poor</option>
                                <option value="2">2 - Fair</option>
                                <option value="3">3 - Good</option>
                                <option value="4">4 - Very Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Review Text *</label>
                        <textarea wire:model="reviewText" rows="5" 
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                            placeholder="Share your experience and feedback..."></textarea>
                        @error('reviewText') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pros and Cons -->
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">What went well?</label>
                            @foreach($pros as $index => $pro)
                                <div class="flex mb-2">
                                    <input wire:model="pros.{{ $index }}" type="text" 
                                        class="flex-1 px-4 py-2 border border-themed-primary rounded-l-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                        placeholder="Something positive...">
                                    <button type="button" wire:click="removePro({{ $index }})" 
                                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-lg transition-colors">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addPro" 
                                class="mt-2 px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Pro
                            </button>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Areas for improvement</label>
                            @foreach($cons as $index => $con)
                                <div class="flex mb-2">
                                    <input wire:model="cons.{{ $index }}" type="text" 
                                        class="flex-1 px-4 py-2 border border-themed-primary rounded-l-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                        placeholder="Something to improve...">
                                    <button type="button" wire:click="removeCon({{ $index }})" 
                                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-r-lg transition-colors">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addCon" 
                                class="mt-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Con
                            </button>
                        </div>
                    </div>

                    <!-- Recommendation -->
                    <div>
                        <label class="flex items-center">
                            <input wire:model="wouldRecommend" type="checkbox" class="rounded border-themed-primary text-accent-primary bg-themed-secondary transition-colors duration-300">
                            <span class="ml-2 text-sm text-themed-primary transition-colors duration-300">I would recommend this mentor to others</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                        <button type="button" wire:click="closeModal" 
                            class="px-6 py-3 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-6 py-3 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 flex items-center justify-center z-50 transition-colors duration-300">
        <div class="bg-themed-secondary rounded-lg p-6 shadow-xl border border-themed-primary transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-accent-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-themed-primary font-medium transition-colors duration-300">Processing...</span>
            </div>
        </div>
    </div>
</div>