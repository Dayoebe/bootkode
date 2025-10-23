<!-- Personal Section -->
@if($activeSection === 'personal')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-user text-accent-themed-primary mr-3"></i>
                Personal Information
            </h2>
            <button wire:click="generateAISuggestions('personal')"
                class="bg-accent-themed-secondary hover:bg-accent-themed-primary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-magic mr-2"></i>AI Suggest
            </button>
        </div>

        <form wire:submit.prevent="savePersonalInfo" class="space-y-6">
            <!-- Profile Image -->
            <div class="flex items-center space-x-6">
                @if($resume->profile_image_url)
                    <img src="{{ $resume->profile_image_url }}" alt="Profile"
                        class="w-24 h-24 rounded-full object-cover border-4 border-themed-primary">
                @else
                    <div class="w-24 h-24 rounded-full bg-themed-tertiary flex items-center justify-center">
                        <i class="fas fa-user text-themed-secondary text-2xl"></i>
                    </div>
                @endif
                <div>
                    <input type="file" wire:model="profileImageUpload" accept="image/*" class="hidden" id="profile-image">
                    <label for="profile-image"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg cursor-pointer text-sm transition-colors inline-block">
                        <i class="fas fa-camera mr-2"></i>Change Photo
                    </label>
                    <p class="text-xs text-themed-tertiary mt-1">JPG, PNG up to 2MB</p>
                </div>
            </div>

            <!-- Basic Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Full Name *</label>
                    <input type="text" wire:model="personalForm.full_name"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    @error('personalForm.full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Professional Title *</label>
                    <input type="text" wire:model="personalForm.professional_title"
                        placeholder="e.g., Senior Software Engineer"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    @error('personalForm.professional_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Email Address *</label>
                    <input type="email" wire:model="personalForm.email"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    @error('personalForm.email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Phone Number</label>
                    <input type="tel" wire:model="personalForm.phone"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Location</label>
                    <input type="text" wire:model="personalForm.location" placeholder="City, State, Country"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Website</label>
                    <input type="url" wire:model="personalForm.website" placeholder="https://yourwebsite.com"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">LinkedIn</label>
                    <input type="url" wire:model="personalForm.linkedin" placeholder="https://linkedin.com/in/yourprofile"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">GitHub</label>
                    <input type="url" wire:model="personalForm.github" placeholder="https://github.com/yourusername"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                </div>
            </div>





            <!-- Professional Summary -->
            <div>
                <label class="block text-sm font-medium text-themed-primary mb-2">Professional Summary *</label>
                <textarea wire:model="personalForm.professional_summary" rows="4"
                    placeholder="Write a compelling summary of your professional background, key achievements, and career goals..."
                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                @error('personalForm.professional_summary') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-save mr-2"></i>Save Personal Information
            </button>
        </form>
    </div>
@endif

<!-- Experience Section -->
@if($activeSection === 'experience')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-briefcase text-accent-themed-primary mr-3"></i>
                Work Experience
            </h2>
            <button wire:click="generateAISuggestions('experience')"
                class="bg-accent-themed-secondary hover:bg-accent-themed-primary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-magic mr-2"></i>AI Enhance
            </button>
        </div>

        <!-- Add/Edit Experience Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingExperienceIndex !== null ? 'Edit Experience' : 'Add New Experience' }}
            </h3>

            <form wire:submit.prevent="{{ $editingExperienceIndex !== null ? 'updateExperience' : 'addExperience' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Job Title *</label>
                        <input type="text" wire:model="experienceForm.position" placeholder="e.g., Senior Software Engineer"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('experienceForm.position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Company *</label>
                        <input type="text" wire:model="experienceForm.company" placeholder="Company Name"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('experienceForm.company') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Start Date *</label>
                        <input type="date" wire:model="experienceForm.start_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('experienceForm.start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">End Date</label>
                        <input type="date" wire:model="experienceForm.end_date" {{ isset($experienceForm['current']) && $experienceForm['current'] ? 'disabled' : '' }}
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all disabled:opacity-50">
                        <label class="flex items-center mt-2">
                            <input type="checkbox" wire:model="experienceForm.current"
                                class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary">
                            <span class="ml-2 text-sm text-themed-secondary">Currently working here</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-1">Job Description</label>
                    <textarea wire:model="experienceForm.description" rows="4"
                        placeholder="Describe your responsibilities, achievements, and impact in this role..."
                        class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingExperienceIndex !== null ? 'Update Experience' : 'Add Experience' }}
                    </button>

                    @if($editingExperienceIndex !== null)
                        <button type="button" wire:click="resetExperienceForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Experience List -->
        @if($resume->work_experience && count($resume->work_experience) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Experience</h3>
                @foreach($resume->work_experience as $index => $experience)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $experience['position'] ?? 'N/A' }}</h4>
                                <p class="text-accent-themed-primary font-medium">{{ $experience['company'] ?? 'N/A' }}</p>
                                <p class="text-sm text-themed-secondary mt-1">
                                    {{ isset($experience['start_date']) ? \Carbon\Carbon::parse($experience['start_date'])->format('M Y') : '' }}
                                    -
                                    @if(isset($experience['current']) && $experience['current'])
                                        Present
                                    @elseif(isset($experience['end_date']))
                                        {{ \Carbon\Carbon::parse($experience['end_date'])->format('M Y') }}
                                    @else
                                        Present
                                    @endif
                                </p>
                                @if(!empty($experience['description']))
                                    <p class="text-themed-primary mt-3 leading-relaxed">{{ $experience['description'] }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editExperience({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deleteExperience({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this experience?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- Education Section -->
@if($activeSection === 'education')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-graduation-cap text-accent-themed-primary mr-3"></i>
                Education
            </h2>
            <button wire:click="generateAISuggestions('education')"
                class="bg-accent-themed-secondary hover:bg-accent-themed-primary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-magic mr-2"></i>AI Enhance
            </button>
        </div>

        <!-- Add/Edit Education Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingEducationIndex !== null ? 'Edit Education' : 'Add Education' }}
            </h3>

            <form wire:submit.prevent="{{ $editingEducationIndex !== null ? 'updateEducation' : 'addEducation' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Degree *</label>
                        <input type="text" wire:model="educationForm.degree" placeholder="e.g., Bachelor of Science"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('educationForm.degree') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Field of Study</label>
                        <input type="text" wire:model="educationForm.field_of_study" placeholder="e.g., Computer Science"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-themed-primary mb-1">Institution *</label>
                        <input type="text" wire:model="educationForm.institution" placeholder="University Name"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('educationForm.institution') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Location</label>
                        <input type="text" wire:model="educationForm.location" placeholder="City, Country"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">GPA (Optional)</label>
                        <input type="number" wire:model="educationForm.gpa" step="0.01" min="0" max="4" placeholder="3.8"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Start Date *</label>
                        <input type="date" wire:model="educationForm.start_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('educationForm.start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">End Date</label>
                        <input type="date" wire:model="educationForm.end_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-1">Description/Achievements</label>
                    <textarea wire:model="educationForm.description" rows="3"
                        placeholder="Notable achievements, relevant coursework, honors, etc."
                        class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingEducationIndex !== null ? 'Update Education' : 'Add Education' }}
                    </button>

                    @if($editingEducationIndex !== null)
                        <button type="button" wire:click="resetEducationForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Education List -->
        @if($resume->education && count($resume->education) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Education</h3>
                @foreach($resume->education as $index => $education)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $education['degree'] ?? 'N/A' }}</h4>
                                @if(!empty($education['field_of_study']))
                                    <p class="text-themed-secondary">{{ $education['field_of_study'] }}</p>
                                @endif
                                <p class="text-accent-themed-primary font-medium">{{ $education['institution'] ?? 'N/A' }}</p>
                                <div class="flex items-center space-x-4 text-sm text-themed-secondary mt-1">
                                    <span>
                                        {{ isset($education['start_date']) ? \Carbon\Carbon::parse($education['start_date'])->format('M Y') : '' }}
                                        @if(isset($education['end_date']))
                                            - {{ \Carbon\Carbon::parse($education['end_date'])->format('M Y') }}
                                        @endif
                                    </span>
                                    @if(!empty($education['gpa']))
                                        <span>GPA: {{ $education['gpa'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($education['description']))
                                    <p class="text-themed-primary mt-3 leading-relaxed">{{ $education['description'] }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editEducation({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deleteEducation({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this education entry?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- Skills Section -->
@if($activeSection === 'skills')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-cogs text-accent-themed-primary mr-3"></i>
                Skills & Expertise
            </h2>
            <button wire:click="generateAISuggestions('skills')"
                class="bg-accent-themed-secondary hover:bg-accent-themed-primary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-magic mr-2"></i>AI Suggest
            </button>
        </div>

        <!-- Add Skill Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingSkillIndex !== null ? 'Edit Skill' : 'Add New Skill' }}
            </h3>

            <form wire:submit.prevent="{{ $editingSkillIndex !== null ? 'updateSkill' : 'addSkill' }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Skill Name *</label>
                        <input type="text" wire:model="skillForm.name" placeholder="e.g., JavaScript, Project Management"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('skillForm.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Category</label>
                        <select wire:model="skillForm.category"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                            <option value="">Select Category</option>
                            <option value="Technical">Technical</option>
                            <option value="Programming">Programming</option>
                            <option value="Design">Design</option>
                            <option value="Management">Management</option>
                            <option value="Communication">Communication</option>
                            <option value="Languages">Languages</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Proficiency Level *
                            (1-100)</label>
                        <input type="range" wire:model="skillForm.proficiency" min="1" max="100"
                            class="w-full h-2 bg-black rounded-lg appearance-none cursor-pointer accent-accent-themed-primary">
                        <div class="text-center text-sm text-themed-secondary mt-1">{{ $skillForm['proficiency'] ?? 50 }}%
                        </div>
                        @error('skillForm.proficiency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingSkillIndex !== null ? 'Update Skill' : 'Add Skill' }}
                    </button>

                    @if($editingSkillIndex !== null)
                        <button type="button" wire:click="resetSkillForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>
        <!-- Skills List -->
        @if($resume->skills && count($resume->skills) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Skills</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($resume->skills as $index => $skill)
                        <div
                            class="bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-semibold text-themed-primary">{{ $skill['name'] ?? 'N/A' }}</h4>
                                    @if(!empty($skill['category']))
                                        <p class="text-sm text-themed-secondary">{{ $skill['category'] }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-1">
                                    <button wire:click="editSkill({{ $index }})"
                                        class="text-accent-themed-primary hover:text-accent-themed-secondary p-1 transition-colors">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button wire:click="deleteSkill({{ $index }})"
                                        onclick="return confirm('Are you sure you want to delete this skill?')"
                                        class="text-red-600 hover:text-red-800 p-1 transition-colors">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-themed-secondary">Proficiency</span>
                                    <span class="font-medium text-themed-primary">{{ $skill['proficiency'] ?? 50 }}%</span>
                                </div>
                                <div class="w-full bg-themed-tertiary rounded-full h-2 mt-1">
                                    <div class="bg-accent-themed-primary h-2 rounded-full transition-all duration-300"
                                        style="width: {{ $skill['proficiency'] ?? 50 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

<!-- Projects Section -->
@if($activeSection === 'projects')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-project-diagram text-accent-themed-primary mr-3"></i>
                Projects
            </h2>
            <button wire:click="generateAISuggestions('projects')"
                class="bg-accent-themed-secondary hover:bg-accent-themed-primary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-magic mr-2"></i>AI Enhance
            </button>
        </div>

        <!-- Add/Edit Project Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingProjectIndex !== null ? 'Edit Project' : 'Add Project' }}
            </h3>

            <form wire:submit.prevent="{{ $editingProjectIndex !== null ? 'updateProject' : 'addProject' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-themed-primary mb-1">Project Name *</label>
                        <input type="text" wire:model="projectForm.name" placeholder="e.g., E-commerce Web Application"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('projectForm.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Project URL</label>
                        <input type="url" wire:model="projectForm.url" placeholder="https://project-demo.com"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">GitHub URL</label>
                        <input type="url" wire:model="projectForm.github_url" placeholder="https://github.com/username/repo"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Start Date</label>
                        <input type="date" wire:model="projectForm.start_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">End Date</label>
                        <input type="date" wire:model="projectForm.end_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-themed-primary mb-1">Technologies Used</label>
                        <input type="text" wire:model="projectForm.technologies" placeholder="React, Node.js, MongoDB, AWS"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        <p class="text-xs text-themed-secondary mt-1">Separate technologies with commas</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-1">Project Description</label>
                    <textarea wire:model="projectForm.description" rows="4"
                        placeholder="Describe the project, your role, key features, and achievements..."
                        class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingProjectIndex !== null ? 'Update Project' : 'Add Project' }}
                    </button>

                    @if($editingProjectIndex !== null)
                        <button type="button" wire:click="resetProjectForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Projects List -->
        @if($resume->projects && count($resume->projects) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Projects</h3>
                @foreach($resume->projects as $index => $project)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $project['name'] ?? 'N/A' }}</h4>
                                <div class="flex items-center space-x-4 mt-2">
                                    @if(!empty($project['url']))
                                        <a href="{{ $project['url'] }}" target="_blank"
                                            class="text-accent-themed-primary hover:text-accent-themed-secondary text-sm transition-colors">
                                            <i class="fas fa-external-link-alt mr-1"></i>Live Demo
                                        </a>
                                    @endif
                                    @if(!empty($project['github_url']))
                                        <a href="{{ $project['github_url'] }}" target="_blank"
                                            class="text-themed-secondary hover:text-themed-primary text-sm transition-colors">
                                            <i class="fab fa-github mr-1"></i>GitHub
                                        </a>
                                    @endif
                                </div>
                                @if(!empty($project['technologies']))
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach(explode(',', $project['technologies']) as $tech)
                                            <span
                                                class="px-2 py-1 text-xs bg-accent-themed-primary bg-opacity-20 text-accent-themed-primary rounded-full">{{ trim($tech) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if(!empty($project['description']))
                                    <p class="text-themed-primary mt-3 leading-relaxed">{{ $project['description'] }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editProject({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deleteProject({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this project?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- Certifications Section -->
@if($activeSection === 'certifications')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-certificate text-accent-themed-primary mr-3"></i>
                Certifications
            </h2>
        </div>

        <!-- Add/Edit Certification Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingCertificationIndex !== null ? 'Edit Certification' : 'Add Certification' }}
            </h3>

            <form
                wire:submit.prevent="{{ $editingCertificationIndex !== null ? 'updateCertification' : 'addCertification' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Certification Name *</label>
                        <input type="text" wire:model="certificationForm.name" placeholder="e.g., AWS Solutions Architect"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('certificationForm.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Issuing Organization *</label>
                        <input type="text" wire:model="certificationForm.issuer" placeholder="e.g., Amazon Web Services"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('certificationForm.issuer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Issue Date</label>
                        <input type="date" wire:model="certificationForm.issue_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Expiry Date</label>
                        <input type="date" wire:model="certificationForm.expiry_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Credential ID</label>
                        <input type="text" wire:model="certificationForm.credential_id" placeholder="e.g., AWS-12345"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Credential URL</label>
                        <input type="url" wire:model="certificationForm.credential_url" placeholder="https://..."
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingCertificationIndex !== null ? 'Update Certification' : 'Add Certification' }}
                    </button>

                    @if($editingCertificationIndex !== null)
                        <button type="button" wire:click="resetCertificationForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Certifications List -->
        @if($resume->certifications && count($resume->certifications) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Certifications</h3>
                @foreach($resume->certifications as $index => $certification)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $certification['name'] ?? 'N/A' }}</h4>
                                <p class="text-accent-themed-primary font-medium">{{ $certification['issuer'] ?? 'N/A' }}</p>
                                <div class="flex items-center space-x-4 text-sm text-themed-secondary mt-1">
                                    @if(!empty($certification['issue_date']))
                                        <span>Issued: {{ \Carbon\Carbon::parse($certification['issue_date'])->format('M Y') }}</span>
                                    @endif
                                    @if(!empty($certification['expiry_date']))
                                        <span>Expires: {{ \Carbon\Carbon::parse($certification['expiry_date'])->format('M Y') }}</span>
                                    @endif
                                </div>
                                @if(!empty($certification['credential_id']))
                                    <p class="text-sm text-themed-secondary mt-2">Credential ID: {{ $certification['credential_id'] }}
                                    </p>
                                @endif
                                @if(!empty($certification['credential_url']))
                                    <a href="{{ $certification['credential_url'] }}" target="_blank"
                                        class="text-accent-themed-primary hover:text-accent-themed-secondary text-sm mt-2 inline-block transition-colors">
                                        <i class="fas fa-external-link-alt mr-1"></i>View Credential
                                    </a>
                                @endif
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editCertification({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deleteCertification({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this certification?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- Languages Section -->
@if($activeSection === 'languages')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-language text-accent-themed-primary mr-3"></i>
                Languages
            </h2>
        </div>

        <!-- Add/Edit Language Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingLanguageIndex !== null ? 'Edit Language' : 'Add Language' }}
            </h3>

            <form wire:submit.prevent="{{ $editingLanguageIndex !== null ? 'updateLanguage' : 'addLanguage' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Language *</label>
                        <input type="text" wire:model="languageForm.language" placeholder="e.g., Spanish"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('languageForm.language') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Proficiency Level *</label>
                        <select wire:model="languageForm.proficiency"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                            <option value="">Select Level</option>
                            <option value="Native">Native</option>
                            <option value="Fluent">Fluent</option>
                            <option value="Professional">Professional Working</option>
                            <option value="Conversational">Conversational</option>
                            <option value="Basic">Basic</option>
                        </select>
                        @error('languageForm.proficiency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Certification</label>
                        <input type="text" wire:model="languageForm.certification" placeholder="e.g., DELE B2"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingLanguageIndex !== null ? 'Update Language' : 'Add Language' }}
                    </button>

                    @if($editingLanguageIndex !== null)
                        <button type="button" wire:click="resetLanguageForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Languages List -->
        @if($resume->languages && count($resume->languages) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Languages</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($resume->languages as $index => $language)
                        <div
                            class="bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-themed-primary">{{ $language['language'] ?? 'N/A' }}</h4>
                                    <p class="text-accent-themed-primary text-sm">{{ $language['proficiency'] ?? 'N/A' }}</p>
                                    @if(!empty($language['certification']))
                                        <p class="text-xs text-themed-secondary mt-1">{{ $language['certification'] }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-1">
                                    <button wire:click="editLanguage({{ $index }})"
                                        class="text-accent-themed-primary hover:text-accent-themed-secondary p-1 transition-colors">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button wire:click="deleteLanguage({{ $index }})"
                                        onclick="return confirm('Are you sure you want to delete this language?')"
                                        class="text-red-600 hover:text-red-800 p-1 transition-colors">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

<!-- Volunteer Work Section -->
@if($activeSection === 'volunteer')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-heart text-accent-themed-primary mr-3"></i>
                Volunteer Experience
            </h2>
        </div>

        <!-- Add/Edit Volunteer Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingVolunteerIndex !== null ? 'Edit Volunteer Experience' : 'Add Volunteer Experience' }}
            </h3>

            <form wire:submit.prevent="{{ $editingVolunteerIndex !== null ? 'updateVolunteer' : 'addVolunteer' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Organization *</label>
                        <input type="text" wire:model="volunteerForm.organization" placeholder="e.g., Red Cross"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('volunteerForm.organization') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Role/Position *</label>
                        <input type="text" wire:model="volunteerForm.position" placeholder="e.g., Volunteer Coordinator"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('volunteerForm.position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Start Date *</label>
                        <input type="date" wire:model="volunteerForm.start_date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('volunteerForm.start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">End Date</label>
                        <input type="date" wire:model="volunteerForm.end_date" {{ isset($volunteerForm['current']) && $volunteerForm['current'] ? 'disabled' : '' }}
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all disabled:opacity-50">
                        <label class="flex items-center mt-2">
                            <input type="checkbox" wire:model="volunteerForm.current"
                                class="rounded border-themed-primary text-accent-themed-primary focus:ring-accent-themed-primary">
                            <span class="ml-2 text-sm text-themed-secondary">Currently volunteering</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-1">Description</label>
                    <textarea wire:model="volunteerForm.description" rows="3"
                        placeholder="Describe your volunteer activities, responsibilities, and impact..."
                        class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingVolunteerIndex !== null ? 'Update Volunteer Experience' : 'Add Volunteer Experience' }}
                    </button>

                    @if($editingVolunteerIndex !== null)
                        <button type="button" wire:click="resetVolunteerForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Volunteer List -->
        @if($resume->volunteer && count($resume->volunteer) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Volunteer Experience</h3>
                @foreach($resume->volunteer as $index => $volunteer)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $volunteer['position'] ?? 'N/A' }}</h4>
                                <p class="text-accent-themed-primary font-medium">{{ $volunteer['organization'] ?? 'N/A' }}</p>
                                <p class="text-sm text-themed-secondary mt-1">
                                    {{ isset($volunteer['start_date']) ? \Carbon\Carbon::parse($volunteer['start_date'])->format('M Y') : '' }}
                                    -
                                    @if(isset($volunteer['current']) && $volunteer['current'])
                                        Present
                                    @elseif(isset($volunteer['end_date']))
                                        {{ \Carbon\Carbon::parse($volunteer['end_date'])->format('M Y') }}
                                    @else
                                        Present
                                    @endif
                                </p>
                                @if(!empty($volunteer['description']))
                                    <p class="text-themed-primary mt-3 leading-relaxed">{{ $volunteer['description'] }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editVolunteer({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deleteVolunteer({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this volunteer experience?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- Publications Section -->
@if($activeSection === 'publications')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-book text-accent-themed-primary mr-3"></i>
                Publications & Research
            </h2>
        </div>

        <!-- Add/Edit Publication Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingPublicationIndex !== null ? 'Edit Publication' : 'Add Publication' }}
            </h3>

            <form wire:submit.prevent="{{ $editingPublicationIndex !== null ? 'updatePublication' : 'addPublication' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Title *</label>
                        <input type="text" wire:model="publicationForm.title"
                            placeholder="e.g., Machine Learning Applications in Healthcare"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('publicationForm.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-1">Authors</label>
                            <input type="text" wire:model="publicationForm.authors"
                                placeholder="Smith, J., Doe, A., Johnson, K."
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-1">Publication/Journal</label>
                            <input type="text" wire:model="publicationForm.publication"
                                placeholder="e.g., Nature, IEEE Transactions"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-1">Publication Date</label>
                            <input type="date" wire:model="publicationForm.publish_date"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-1">DOI</label>
                            <input type="text" wire:model="publicationForm.doi" placeholder="10.1000/182"
                                class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">URL</label>
                        <input type="url" wire:model="publicationForm.url" placeholder="https://..."
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingPublicationIndex !== null ? 'Update Publication' : 'Add Publication' }}
                    </button>

                    @if($editingPublicationIndex !== null)
                        <button type="button" wire:click="resetPublicationForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Publications List -->
        @if($resume->publications && count($resume->publications) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Publications</h3>
                @foreach($resume->publications as $index => $publication)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $publication['title'] ?? 'N/A' }}</h4>
                                @if(!empty($publication['authors']))
                                    <p class="text-themed-secondary text-sm">{{ $publication['authors'] }}</p>
                                @endif
                                @if(!empty($publication['publication']))
                                    <p class="text-accent-themed-primary font-medium text-sm">{{ $publication['publication'] }}</p>
                                @endif
                                @if(!empty($publication['publish_date']))
                                    <p class="text-themed-secondary text-sm mt-1">
                                        Published: {{ \Carbon\Carbon::parse($publication['publish_date'])->format('F Y') }}
                                    </p>
                                @endif
                                <div class="flex items-center space-x-4 mt-2">
                                    @if(!empty($publication['url']))
                                        <a href="{{ $publication['url'] }}" target="_blank"
                                            class="text-accent-themed-primary hover:text-accent-themed-secondary text-sm transition-colors">
                                            <i class="fas fa-external-link-alt mr-1"></i>View Publication
                                        </a>
                                    @endif
                                    @if(!empty($publication['doi']))
                                        <span class="text-xs text-themed-secondary">DOI: {{ $publication['doi'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editPublication({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deletePublication({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this publication?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- Awards & Honors Section -->
@if($activeSection === 'awards')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-trophy text-accent-themed-primary mr-3"></i>
                Awards & Honors
            </h2>
        </div>

        <!-- Add/Edit Award Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingAwardIndex !== null ? 'Edit Award' : 'Add Award' }}
            </h3>

            <form wire:submit.prevent="{{ $editingAwardIndex !== null ? 'updateAward' : 'addAward' }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Award Title *</label>
                        <input type="text" wire:model="awardForm.title" placeholder="e.g., Employee of the Year"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('awardForm.title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Issuing Organization *</label>
                        <input type="text" wire:model="awardForm.issuer" placeholder="e.g., Microsoft Corporation"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('awardForm.issuer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Date Received</label>
                        <input type="date" wire:model="awardForm.date"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-1">Description</label>
                    <textarea wire:model="awardForm.description" rows="3"
                        placeholder="Brief description of the award and why it was received..."
                        class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingAwardIndex !== null ? 'Update Award' : 'Add Award' }}
                    </button>

                    @if($editingAwardIndex !== null)
                        <button type="button" wire:click="resetAwardForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Awards List -->
        @if($resume->awards && count($resume->awards) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your Awards & Honors</h3>
                @foreach($resume->awards as $index => $award)
                    <div
                        class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-lg font-semibold text-themed-primary">{{ $award['title'] ?? 'N/A' }}</h4>
                                <p class="text-accent-themed-primary font-medium">{{ $award['issuer'] ?? 'N/A' }}</p>
                                @if(!empty($award['date']))
                                    <p class="text-sm text-themed-secondary mt-1">
                                        {{ \Carbon\Carbon::parse($award['date'])->format('F Y') }}
                                    </p>
                                @endif
                                @if(!empty($award['description']))
                                    <p class="text-themed-primary mt-3 leading-relaxed">{{ $award['description'] }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2 ml-4">
                                <button wire:click="editAward({{ $index }})"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="deleteAward({{ $index }})"
                                    onclick="return confirm('Are you sure you want to delete this award?')"
                                    class="text-red-600 hover:text-red-800 p-2 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

<!-- References Section -->
@if($activeSection === 'references')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-users text-accent-themed-primary mr-3"></i>
                Professional References
            </h2>
        </div>

        <div class="bg-accent-themed-primary/10 border border-accent-themed-primary/30 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-accent-themed-primary mt-1 mr-3"></i>
                <div class="text-sm text-themed-primary">
                    <p class="font-medium">Professional References</p>
                    <p>Include 2-4 professional references who can speak to your work performance and character. Always ask
                        permission before listing someone as a reference.</p>
                </div>
            </div>
        </div>

        <!-- Add/Edit Reference Form -->
        <div class="bg-themed-tertiary rounded-xl p-6">
            <h3 class="text-lg font-semibold text-themed-primary mb-4">
                {{ $editingReferenceIndex !== null ? 'Edit Reference' : 'Add Reference' }}
            </h3>

            <form wire:submit.prevent="{{ $editingReferenceIndex !== null ? 'updateReference' : 'addReference' }}"
                class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Full Name *</label>
                        <input type="text" wire:model="referenceForm.name" placeholder="e.g., John Smith"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                        @error('referenceForm.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Job Title</label>
                        <input type="text" wire:model="referenceForm.position" placeholder="e.g., Senior Manager"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Company</label>
                        <input type="text" wire:model="referenceForm.company" placeholder="e.g., Tech Solutions Inc."
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Relationship</label>
                        <select wire:model="referenceForm.relationship"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                            <option value="">Select Relationship</option>
                            <option value="Direct Supervisor">Direct Supervisor</option>
                            <option value="Manager">Manager</option>
                            <option value="Colleague">Colleague</option>
                            <option value="Client">Client</option>
                            <option value="Mentor">Mentor</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Email</label>
                        <input type="email" wire:model="referenceForm.email" placeholder="john.smith@company.com"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-1">Phone</label>
                        <input type="tel" wire:model="referenceForm.phone" placeholder="+1 (555) 123-4567"
                            class="w-full px-3 py-2 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button type="submit"
                        class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                        {{ $editingReferenceIndex !== null ? 'Update Reference' : 'Add Reference' }}
                    </button>

                    @if($editingReferenceIndex !== null)
                        <button type="button" wire:click="resetReferenceForm"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- References List -->
        @if($resume->references && count($resume->references) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-themed-primary">Your References</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($resume->references as $index => $reference)
                        <div
                            class="bg-themed-secondary border border-themed-primary rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-themed-primary">{{ $reference['name'] ?? 'N/A' }}</h4>
                                    @if(!empty($reference['position']))
                                        <p class="text-themed-secondary text-sm">{{ $reference['position'] }}</p>
                                    @endif
                                    @if(!empty($reference['company']))
                                        <p class="text-accent-themed-primary font-medium text-sm">{{ $reference['company'] }}</p>
                                    @endif
                                    @if(!empty($reference['relationship']))
                                        <p class="text-xs text-themed-secondary mt-1">{{ $reference['relationship'] }}</p>
                                    @endif
                                    <div class="mt-2 space-y-1">
                                        @if(!empty($reference['email']))
                                            <p class="text-xs text-themed-secondary flex items-center">
                                                <i class="fas fa-envelope mr-2 w-3"></i>{{ $reference['email'] }}
                                            </p>
                                        @endif
                                        @if(!empty($reference['phone']))
                                            <p class="text-xs text-themed-secondary flex items-center">
                                                <i class="fas fa-phone mr-2 w-3"></i>{{ $reference['phone'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-col space-y-1">
                                    <button wire:click="editReference({{ $index }})"
                                        class="text-accent-themed-primary hover:text-accent-themed-secondary p-1 transition-colors">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button wire:click="deleteReference({{ $index }})"
                                        onclick="return confirm('Are you sure you want to delete this reference?')"
                                        class="text-red-600 hover:text-red-800 p-1 transition-colors">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!$resume->references || count($resume->references) === 0)
            <div class="text-center py-8">
                <i class="fas fa-users text-themed-tertiary text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-themed-primary mb-2">No References Added</h3>
                <p class="text-themed-secondary">Add professional references to strengthen your resume.</p>
            </div>
        @endif
    </div>
@endif

<!-- Summary Section -->
@if($activeSection === 'summary')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-align-left text-accent-themed-primary mr-3"></i>
                Professional Summary
            </h2>
            <button wire:click="enhanceWithAI('personal', 'professional_summary')"
                class="bg-accent-themed-secondary hover:bg-accent-themed-primary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                <i class="fas fa-magic mr-2"></i>AI Enhance
            </button>
        </div>

        <div class="bg-accent-themed-primary/10 border border-accent-themed-primary/30 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-lightbulb text-accent-themed-primary mt-1 mr-3"></i>
                <div class="text-sm text-themed-primary">
                    <p class="font-medium">Writing Tips</p>
                    <ul class="mt-2 space-y-1">
                        <li>• Keep it concise (2-4 sentences)</li>
                        <li>• Highlight your key strengths and achievements</li>
                        <li>• Tailor it to the job you're applying for</li>
                        <li>• Use action words and quantifiable results</li>
                    </ul>
                </div>
            </div>
        </div>

        <form wire:submit.prevent="savePersonalInfo" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-themed-primary mb-2">Professional Summary *</label>
                <textarea wire:model="personalForm.professional_summary" rows="6"
                    placeholder="Write a compelling summary that highlights your professional background, key achievements, and career goals. Focus on what makes you unique and valuable to potential employers..."
                    class="w-full px-4 py-3 border border-themed-primary rounded-lg bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent transition-all resize-none"></textarea>
                @error('personalForm.professional_summary')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <div class="mt-2 flex justify-between text-sm text-themed-secondary">
                    <span>{{ strlen($personalForm['professional_summary'] ?? '') }} characters</span>
                    <span>Recommended: 300-500 characters</span>
                </div>
            </div>

            @if($aiEnhancedText)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <h4 class="font-medium text-green-900 dark:text-green-300 mb-2">AI Enhanced Version:</h4>
                    <p class="text-green-800 dark:text-green-400 mb-3">{{ $aiEnhancedText }}</p>
                    <button type="button"
                        onclick="document.querySelector('textarea[wire\\:model=\'personalForm.professional_summary\']').value = '{{ addslashes($aiEnhancedText) }}'; @this.set('personalForm.professional_summary', '{{ addslashes($aiEnhancedText) }}');"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition-colors">
                        Use This Version
                    </button>
                </div>
            @endif

            <button type="submit"
                class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-save mr-2"></i>Save Professional Summary
            </button>
        </form>
    </div>
@endif