{{-- FILE: resources/views/livewire/mentorship/partial/profile.blade.php --}}

<div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Mentor Profile</h2>
        @if(!$profileId)
            <button wire:click="$dispatch('open-application-modal')"
                class="bg-accent-primary hover:bg-accent-secondary text-white px-6 py-3 rounded-lg transition-colors">
                <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
            </button>
        @else
            <button wire:click="editProfile"
                class="bg-accent-primary hover:bg-accent-secondary text-white px-6 py-3 rounded-lg transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit Profile
            </button>
        @endif
    </div>

    @if($profileId)
        <div class="space-y-6">
            <!-- Profile Summary -->
            <div class="border-b border-themed-primary pb-6 transition-colors duration-300">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-accent-primary to-accent-secondary rounded-full flex items-center justify-center text-white text-3xl font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-themed-primary transition-colors duration-300">{{ auth()->user()->name }}</h3>
                        <p class="text-lg text-themed-secondary transition-colors duration-300">{{ auth()->user()->mentorProfile->experience_label ?? 'Mentor' }}</p>
                        <div class="flex items-center mt-2">
                            <div class="text-yellow-400 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= floor(auth()->user()->mentorProfile->rating ?? 0) ? '' : 'text-themed-tertiary' }}"></i>
                                @endfor
                            </div>
                            <span class="text-sm text-themed-secondary transition-colors duration-300">({{ auth()->user()->mentorProfile->total_reviews ?? 0 }} reviews)</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-themed-primary transition-colors duration-300">
                            {{ auth()->user()->mentorProfile->current_mentees ?? 0 }}/{{ $maxMentees }}
                        </div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Active Mentees</div>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Specializations</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($specializations ?? [] as $spec)
                            @if($spec)
                                <span class="bg-accent-primary/10 text-accent-primary px-3 py-1 rounded-full text-sm border border-accent-primary/30 transition-colors duration-300">{{ $spec }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div>
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Skills</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($skills ?? [] as $skill)
                            @if($skill)
                                <span class="bg-green-100/50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-3 py-1 rounded-full text-sm border border-green-200/50 dark:border-green-800 transition-colors duration-300">{{ $skill }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="md:col-span-2">
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Bio</h4>
                    <p class="text-themed-secondary leading-relaxed transition-colors duration-300">
                        {{ $bio ?: 'No bio provided yet. Click "Edit Profile" to add your bio.' }}
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Experience</h4>
                    <p class="text-themed-secondary transition-colors duration-300">{{ $yearsExperience }}+ years • {{ ucfirst($experienceLevel) }} Level</p>
                </div>

                <div>
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Availability</h4>
                    <p class="text-themed-secondary transition-colors duration-300">
                        @if($isAvailable)
                            <span class="text-green-600 dark:text-green-400"><i class="fas fa-check-circle mr-1"></i>Available for new mentees</span>
                        @else
                            <span class="text-red-600 dark:text-red-400"><i class="fas fa-pause-circle mr-1"></i>Currently unavailable</span>
                        @endif
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Pricing</h4>
                    <p class="text-themed-secondary transition-colors duration-300">
                        @if($hourlyRate > 0)
                            ${{ number_format($hourlyRate, 2) }}/hour
                        @else
                            Free mentoring
                        @endif
                        @if($offersFreeSessions)
                            <span class="text-green-600 dark:text-green-400 text-sm ml-2">• Offers free sessions</span>
                        @endif
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Contact Links</h4>
                    <div class="flex space-x-4">
                        @if($linkedinProfile)
                            <a href="{{ $linkedinProfile }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                <i class="fab fa-linkedin text-xl"></i>
                            </a>
                        @endif
                        @if($githubProfile)
                            <a href="{{ $githubProfile }}" target="_blank" class="text-themed-primary hover:text-accent-primary transition-colors">
                                <i class="fab fa-github text-xl"></i>
                            </a>
                        @endif
                        @if($portfolioUrl)
                            <a href="{{ $portfolioUrl }}" target="_blank" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors">
                                <i class="fas fa-globe text-xl"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mentoring Approach -->
            @if($mentoringApproach)
                <div class="border-t border-themed-primary pt-6 transition-colors duration-300">
                    <h4 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Mentoring Approach</h4>
                    <p class="text-themed-secondary leading-relaxed transition-colors duration-300">{{ $mentoringApproach }}</p>
                </div>
            @endif

            <!-- Recent Reviews -->
            @if(count($recentReviews) > 0)
                <div class="border-t border-themed-primary pt-6 transition-colors duration-300">
                    <h4 class="font-semibold text-themed-primary mb-4 transition-colors duration-300">Recent Reviews</h4>
                    <div class="space-y-4">
                        @foreach($recentReviews as $review)
                            <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-accent-primary to-accent-secondary rounded-full flex items-center justify-center text-white text-sm font-bold">
                                            {{ substr($review->reviewer->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-themed-primary transition-colors duration-300">{{ $review->reviewer->name }}</p>
                                            <div class="flex items-center">
                                                <div class="text-yellow-400 text-sm mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star {{ $i <= floor($review->overall_rating) ? '' : 'text-themed-tertiary' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-themed-secondary transition-colors duration-300">{{ $review->created_at->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-themed-secondary transition-colors duration-300">{{ \Illuminate\Support\Str::limit($review->review_text, 150) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-6xl text-themed-tertiary mb-4 transition-colors duration-300">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3 class="text-xl font-semibold text-themed-primary mb-2 transition-colors duration-300">No mentor profile yet</h3>
            <p class="text-themed-secondary mb-6 transition-colors duration-300">Create your mentor profile to start accepting mentees</p>
            <button wire:click="$dispatch('open-application-modal')"
                class="bg-accent-primary hover:bg-accent-secondary text-white px-6 py-3 rounded-lg transition-colors">
                <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
            </button>
        </div>
    @endif
</div>

<!-- Profile Edit Modal -->
@if($showProfileModal)
    <div class="fixed inset-0 bg-black/50 dark:bg-black/75 overflow-y-auto h-full w-full z-50 transition-colors duration-300">
        <div class="relative top-20 mx-auto p-5 border border-themed-primary w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-themed-secondary max-h-[85vh] overflow-y-auto transition-colors duration-300">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Edit Mentor Profile</h3>
                    <button wire:click="closeModal" class="text-themed-secondary hover:text-themed-primary transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveProfile" class="space-y-6">
                    <!-- Bio -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Bio *</label>
                        <textarea wire:model="bio" rows="4"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                            placeholder="Tell mentees about yourself..."></textarea>
                        @error('bio') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Specializations -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Specializations *</label>
                        @foreach($specializations as $index => $specialization)
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" wire:model="specializations.{{ $index }}"
                                    class="flex-1 px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="e.g., Web Development">
                                @if(count($specializations) > 1)
                                    <button type="button" wire:click="removeSpecialization({{ $index }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        <button type="button" wire:click="addSpecialization"
                            class="text-accent-primary hover:text-accent-secondary text-sm transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add Specialization
                        </button>
                        @error('specializations') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Skills -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Skills</label>
                        @foreach($skills as $index => $skill)
                            <div class="flex items-center space-x-2 mb-2">
                                <input type="text" wire:model="skills.{{ $index }}"
                                    class="flex-1 px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                    placeholder="e.g., JavaScript, Python">
                                @if(count($skills) > 1)
                                    <button type="button" wire:click="removeSkill({{ $index }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        <button type="button" wire:click="addSkill"
                            class="text-accent-primary hover:text-accent-secondary text-sm transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add Skill
                        </button>
                    </div>

                    <!-- Experience Level and Years -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Experience Level *</label>
                            <select wire:model="experienceLevel"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                                <option value="">Select Level</option>
                                <option value="junior">Junior</option>
                                <option value="mid">Mid Level</option>
                                <option value="senior">Senior</option>
                                <option value="expert">Expert</option>
                            </select>
                            @error('experienceLevel') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Years of Experience *</label>
                            <input type="number" wire:model="yearsExperience" min="0" max="50"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                            @error('yearsExperience') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Mentoring Settings -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Maximum Mentees *</label>
                            <input type="number" wire:model="maxMentees" min="1" max="20"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                            @error('maxMentees') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Hourly Rate (USD)</label>
                            <input type="number" wire:model="hourlyRate" min="0" max="500" step="0.01"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300"
                                placeholder="0 for free mentoring">
                        </div>
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Timezone *</label>
                        <select wire:model="timezone"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time</option>
                            <option value="America/Chicago">Central Time</option>
                            <option value="America/Denver">Mountain Time</option>
                            <option value="America/Los_Angeles">Pacific Time</option>
                            <option value="Europe/London">London</option>
                            <option value="Europe/Paris">Paris</option>
                            <option value="Asia/Tokyo">Tokyo</option>
                        </select>
                        @error('timezone') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Mentoring Approach -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Mentoring Approach *</label>
                        <textarea wire:model="mentoringApproach" rows="3"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                            placeholder="Describe your mentoring style..."></textarea>
                        @error('mentoringApproach') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Communication Preferences -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Communication Preferences</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['Video Calls', 'Voice Calls', 'Text Chat', 'Email', 'Screen Sharing', 'Code Reviews'] as $preference)
                                <label class="flex items-center">
                                    <input type="checkbox"
                                        wire:click="toggleCommunicationPreference('{{ $preference }}')" 
                                        {{ in_array($preference, $communicationPreferences) ? 'checked' : '' }}
                                        class="rounded border-themed-primary text-accent-primary shadow-sm focus:border-accent-primary focus:ring focus:ring-accent-primary/20 bg-themed-secondary transition-colors duration-300">
                                    <span class="ml-2 text-sm text-themed-primary transition-colors duration-300">{{ $preference }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">LinkedIn</label>
                            <input type="url" wire:model="linkedinProfile"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="https://linkedin.com/in/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">GitHub</label>
                            <input type="url" wire:model="githubProfile"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="https://github.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Portfolio</label>
                            <input type="url" wire:model="portfolioUrl"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary transition-colors duration-300"
                                placeholder="https://yourportfolio.com">
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="offersFreeSessions"
                                    class="rounded border-themed-primary text-accent-primary shadow-sm focus:border-accent-primary focus:ring focus:ring-accent-primary/20 bg-themed-secondary transition-colors duration-300">
                                <span class="ml-2 text-sm text-themed-primary transition-colors duration-300">Offers Free Sessions</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isAvailable"
                                    class="rounded border-themed-primary text-accent-primary shadow-sm focus:border-accent-primary focus:ring focus:ring-accent-primary/20 bg-themed-secondary transition-colors duration-300">
                                <span class="ml-2 text-sm text-themed-primary transition-colors duration-300">Available for New Mentees</span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-themed-primary transition-colors duration-300">
                        <button type="button" wire:click="closeModal"
                            class="px-6 py-2 border border-themed-primary rounded-lg text-themed-primary hover:bg-themed-tertiary transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-accent-primary hover:bg-accent-secondary text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif