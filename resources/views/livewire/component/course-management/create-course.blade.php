<div class="bg-gray-800 p-8 rounded-2xl shadow-2xl text-white animate__animated animate__fadeIn" x-data="{ tooltip: '' }">
    <h2 class="text-3xl font-extrabold text-white mb-6 border-b border-indigo-500 pb-4 animate__animated animate__fadeInDown">
        {{ $course ? 'Edit Course' : 'Create New Course' }}
    </h2>

    <form wire:submit="save" class="space-y-6">
        <!-- Title -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.1s">
            <label for="title" class="block text-sm font-medium text-indigo-200 mb-2">Course Title <span class="text-red-400">*</span></label>
            <input type="text" id="title" wire:model="title"
                   class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md transition-all duration-300 hover:shadow-lg"
                   placeholder="e.g., Introduction to Web Development" aria-label="Course Title" aria-required="true">
            @error('title') <p class="mt-2 text-sm text-red-400 animate__animated animate__shakeX">{{ $message }}</p> @enderror
        </div>

        <!-- Description with Character Counter -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.2s">
            <label for="description" class="block text-sm font-medium text-indigo-200 mb-2">Description</label>
            <textarea id="description" wire:model="description" rows="5"
                      class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md resize-y transition-all duration-300 hover:shadow-lg"
                      placeholder="Provide a detailed description..." aria-label="Course Description" maxlength="2000"></textarea>
            <p class="text-xs text-indigo-300 mt-1">{{ strlen($description) }}/2000 characters</p>
            @error('description') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Category & Difficulty (Responsive Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                <label for="category_id" class="block text-sm font-medium text-indigo-200 mb-2">Category <span class="text-red-400">*</span></label>
                <select id="category_id" wire:model="category_id"
                        class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white shadow-md transition-all duration-300 hover:shadow-lg"
                        aria-label="Select Category" aria-required="true">
                    <option value="">Select a Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="animate__animated animate__fadeIn" style="animation-delay: 0.4s">
                <label for="difficulty_level" class="block text-sm font-medium text-indigo-200 mb-2">Difficulty Level <span class="text-red-400">*</span></label>
                <select id="difficulty_level" wire:model="difficulty_level"
                        class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white shadow-md transition-all duration-300 hover:shadow-lg"
                        aria-label="Select Difficulty Level" aria-required="true">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
                @error('difficulty_level') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Estimated Duration -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.5s">
            <label for="estimated_duration_minutes" class="block text-sm font-medium text-indigo-200 mb-2">Estimated Duration (minutes)</label>
            <input type="number" id="estimated_duration_minutes" wire:model="estimated_duration_minutes"
                   class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md transition-all duration-300 hover:shadow-lg"
                   placeholder="e.g., 180 (for 3 hours)" aria-label="Estimated Duration">
            @error('estimated_duration_minutes') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Thumbnail Upload with Preview -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.6s">
            <label for="thumbnail" class="block text-sm font-medium text-indigo-200 mb-2">Course Thumbnail</label>
            <input type="file" id="thumbnail" wire:model="thumbnail"
                   class="block w-full text-sm text-indigo-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 transition-colors"
                   aria-label="Upload Thumbnail">
            @if ($thumbnail)
                <img src="{{ $thumbnail->temporaryUrl() }}" alt="Thumbnail Preview" class="mt-4 w-48 h-28 object-cover rounded-xl shadow-md animate__animated animate__zoomIn">
            @elseif ($course && $course->thumbnail)
                <img src="{{ Storage::url($course->thumbnail) }}" alt="Existing Thumbnail" class="mt-4 w-48 h-28 object-cover rounded-xl shadow-md">
            @endif
            @error('thumbnail') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Price & Premium Toggle -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate__animated animate__fadeIn" style="animation-delay: 0.7s">
            <div class="flex items-center space-x-4">
                <input type="checkbox" id="is_premium" wire:model="is_premium"
                       class="h-5 w-5 text-purple-600 rounded border-indigo-600 bg-indigo-800/50 focus:ring-purple-500">
                <label for="is_premium" class="text-sm font-medium text-indigo-200">Premium Course</label>
            </div>
            <div x-show="$wire.is_premium">
                <label for="price" class="block text-sm font-medium text-indigo-200 mb-2">Price ($)</label>
                <input type="number" id="price" wire:model="price" step="0.01" min="0"
                       class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md"
                       placeholder="e.g., 49.99" aria-label="Course Price">
                @error('price') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Target Audience -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.8s">
            <label for="target_audience" class="block text-sm font-medium text-indigo-200 mb-2">Target Audience</label>
            <input type="text" id="target_audience" wire:model="target_audience"
                   class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md"
                   placeholder="e.g., Beginners in web dev, NYSC members" aria-label="Target Audience">
            @error('target_audience') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Learning Outcomes (Dynamic) -->
        <div x-data="{ outcomes: @entangle('learning_outcomes') }" class="bg-indigo-800/50 p-4 rounded-xl shadow-md animate__animated animate__fadeIn" style="animation-delay: 0.9s">
            <label class="block text-sm font-medium text-indigo-200 mb-2">Learning Outcomes <i class="fas fa-lightbulb text-yellow-400 ml-2" x-tooltip="What students will learn"></i></label>
            <template x-for="(outcome, index) in outcomes" :key="index">
                <div class="mb-2 flex items-center animate__animated animate__fadeInLeft">
                    <input type="text" x-model="outcomes[index]" placeholder="e.g., Build a full-stack app" class="w-full px-4 py-2 bg-indigo-900/50 border border-indigo-600 rounded-xl text-white">
                    <button type="button" @click="outcomes.splice(index, 1)" class="ml-2 text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="outcomes.push('')" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i> Add Outcome
            </button>
            <button type="button" wire:click="suggestAiContent('learning_outcomes')" class="mt-2 ml-2 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors shadow-sm">
                <i class="fas fa-robot mr-2"></i> AI Suggest
            </button>
            @error('learning_outcomes.*') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Prerequisites (Dynamic) -->
        <div x-data="{ prereqs: @entangle('prerequisites') }" class="bg-indigo-800/50 p-4 rounded-xl shadow-md animate__animated animate__fadeIn" style="animation-delay: 1s">
            <label class="block text-sm font-medium text-indigo-200 mb-2">Prerequisites <i class="fas fa-list-ul text-blue-400 ml-2" x-tooltip="Required prior knowledge"></i></label>
            <template x-for="(prereq, index) in prereqs" :key="index">
                <div class="mb-2 flex items-center animate__animated animate__fadeInLeft">
                    <textarea x-model="prereqs[index]" placeholder="e.g., Basic HTML knowledge" rows="2" class="w-full px-4 py-2 bg-indigo-900/50 border border-indigo-600 rounded-xl text-white resize-none"></textarea>
                    <button type="button" @click="prereqs.splice(index, 1)" class="ml-2 text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="prereqs.push('')" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i> Add Prerequisite
            </button>
            @error('prerequisites.*') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Syllabus Overview -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 1.1s">
            <label for="syllabus_overview" class="block text-sm font-medium text-indigo-200 mb-2">Syllabus Overview</label>
            <textarea id="syllabus_overview" wire:model="syllabus_overview" rows="4"
                      class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md resize-y"
                      placeholder="High-level summary of modules..." aria-label="Syllabus Overview"></textarea>
            @error('syllabus_overview') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- FAQs (Dynamic) -->
        <div x-data="{ faqs: @entangle('faqs') }" class="bg-indigo-800/50 p-4 rounded-xl shadow-md animate__animated animate__fadeIn" style="animation-delay: 1.2s">
            <label class="block text-sm font-medium text-indigo-200 mb-2">FAQs <i class="fas fa-question-circle text-purple-400 ml-2" x-tooltip="Frequently Asked Questions"></i></label>
            <template x-for="(faq, index) in faqs" :key="index">
                <div class="mb-4 animate__animated animate__fadeInLeft">
                    <input type="text" x-model="faqs[index].question" placeholder="Question" class="w-full mb-2 px-4 py-2 bg-indigo-900/50 border border-indigo-600 rounded-xl text-white" aria-label="FAQ Question">
                    <textarea x-model="faqs[index].answer" placeholder="Answer" rows="2" class="w-full px-4 py-2 bg-indigo-900/50 border border-indigo-600 rounded-xl text-white resize-none" aria-label="FAQ Answer"></textarea>
                    <button type="button" @click="faqs.splice(index, 1)" class="mt-1 text-red-400 hover:text-red-600 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="faqs.push({ question: '', answer: '' })" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i> Add FAQ
            </button>
            @error('faqs.*') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Completion Threshold with Tooltip -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 1.3s">
            <label for="completion_rate_threshold" class="block text-sm font-medium text-indigo-200 mb-2">Completion Threshold (%) <i class="fas fa-percentage text-yellow-400 ml-2" x-tooltip="Percentage required for certificate"></i></label>
            <input type="number" id="completion_rate_threshold" wire:model="completion_rate_threshold" min="0" max="100" step="0.01"
                   class="w-full px-4 py-2.5 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:ring-purple-500 focus:border-purple-500 text-white placeholder-indigo-300 shadow-md"
                   placeholder="80.00" aria-label="Completion Threshold">
            @error('completion_rate_threshold') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Publish & Approve Toggles -->
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-6 animate__animated animate__fadeIn" style="animation-delay: 1.4s">
            <div class="flex items-center space-x-4">
                <input type="checkbox" id="is_published" wire:model="is_published"
                       class="h-5 w-5 text-green-500 rounded border-indigo-600 bg-indigo-800/50 focus:ring-green-400" aria-label="Publish Course">
                <label for="is_published" class="text-sm font-medium text-indigo-200">Publish Course Immediately</label>
            </div>
            @if (Auth::user()->isSuperAdmin() || Auth::user()->isAcademyAdmin())
                <div class="flex items-center space-x-4">
                    <input type="checkbox" id="is_approved" wire:model="is_approved"
                           class="h-5 w-5 text-purple-500 rounded border-indigo-600 bg-indigo-800/50 focus:ring-purple-400" aria-label="Approve Course">
                    <label for="is_approved" class="text-sm font-medium text-indigo-200">Approve Course</label>
                </div>
            @endif
        </div>

        <!-- Submit Button with Loading -->
        <div class="flex justify-end pt-4 border-t border-indigo-500 mt-6 animate__animated animate__fadeIn" style="animation-delay: 1.5s">
            <button type="submit" wire:loading.attr="disabled"
                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center disabled:opacity-50">
                <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $course ? 'Update' : 'Create' }} Course</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
            </button>
        </div>
    </form>
</div>