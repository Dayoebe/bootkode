<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="px-4 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-8" 
         x-data="{
             showPreview: false,
             isDirty: false,
             saving: false
         }"
         @input="isDirty = true">

        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 transition-colors duration-300">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 lg:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center mb-6 lg:mb-0">
                    <div class="relative mb-4 sm:mb-0 sm:mr-6 self-start sm:self-auto">
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-4 sm:p-5 rounded-2xl sm:rounded-3xl shadow-xl">
                            <i class="fas fa-edit text-white text-2xl sm:text-3xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 bg-emerald-500 w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center animate-pulse">
                            <i class="fas fa-sparkles text-white text-xs sm:text-sm"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-2">
                            Edit Course
                        </h1>
                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base lg:text-lg font-medium transition-colors duration-300">
                            {{ $title ?? 'Update your course details' }}
                        </p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                    <button @click="showPreview = !showPreview"
                        class="group bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 sm:px-6 py-3 rounded-xl sm:rounded-2xl font-bold border border-gray-200 dark:border-gray-600 transition-all duration-300 flex items-center justify-center transform hover:scale-105">
                        <i class="fas fa-eye mr-2 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="text-sm sm:text-base">Preview</span>
                    </button>

                    <a href="{{ route('all-course') }}" 
                       class="group bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 sm:px-8 py-3 rounded-xl sm:rounded-2xl font-bold transition-all duration-300 flex items-center justify-center transform hover:scale-105 shadow-lg">
                        <i class="fas fa-arrow-left mr-2 sm:mr-3 group-hover:-translate-x-1 transition-transform duration-300"></i>
                        <span class="text-sm sm:text-base">Back to Courses</span>
                    </a>
                </div>
            </div>

            <!-- Status Indicators -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 transform hover:scale-105 transition-all duration-300 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl sm:text-3xl font-black text-blue-600 dark:text-blue-400 transition-colors duration-300">
                                Edit Mode
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold text-sm sm:text-base transition-colors duration-300">Currently Editing</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/50 p-3 sm:p-4 rounded-xl transition-colors duration-300">
                            <i class="fas fa-edit text-blue-600 dark:text-blue-400 text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 transform hover:scale-105 transition-all duration-300 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl sm:text-3xl font-black transition-colors duration-300"
                                :class="isDirty ? 'text-orange-600 dark:text-orange-400' : 'text-emerald-600 dark:text-emerald-400'">
                                <span x-text="isDirty ? 'Unsaved' : 'Saved'"></span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold text-sm sm:text-base transition-colors duration-300">Changes Status</p>
                        </div>
                        <div class="p-3 sm:p-4 rounded-xl transition-colors duration-300"
                             :class="isDirty ? 'bg-orange-100 dark:bg-orange-900/50' : 'bg-emerald-100 dark:bg-emerald-900/50'">
                            <i class="text-xl sm:text-2xl transition-colors duration-300"
                               :class="isDirty ? 'fas fa-exclamation-triangle text-orange-600 dark:text-orange-400' : 'fas fa-check-circle text-emerald-600 dark:text-emerald-400'"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 transform hover:scale-105 transition-all duration-300 border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 transition-colors duration-300">
                                {{ $is_published ? 'Live' : 'Draft' }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 font-semibold text-sm sm:text-base transition-colors duration-300">Publication Status</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/50 p-3 sm:p-4 rounded-xl transition-colors duration-300">
                            <i class="fas {{ $is_published ? 'fa-globe' : 'fa-file-alt' }} text-purple-600 dark:text-purple-400 text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
            <form wire:submit.prevent="save" @submit="saving = true" class="p-6 sm:p-8 lg:p-10">
                
                <!-- Form Header -->
                <div class="mb-8 sm:mb-10 pb-6 sm:pb-8 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-2 transition-colors duration-300">
                        Course Details
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base transition-colors duration-300">
                        Update your course information to keep it fresh and engaging
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6 sm:space-y-8">
                        
                        <!-- Course Title -->
                        <div class="group">
                            <label for="title" class="block text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4 transition-colors duration-300">
                                <i class="fas fa-heading mr-2 text-blue-500"></i>
                                Course Title
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="title" 
                                       wire:model="title"
                                       class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl sm:rounded-2xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300 text-sm sm:text-base group-hover:border-gray-300 dark:group-hover:border-gray-500"
                                       placeholder="Enter an engaging course title...">
                                <div class="absolute right-3 sm:right-4 top-3 sm:top-4 text-gray-400">
                                    <i class="fas fa-pencil-alt text-sm sm:text-base"></i>
                                </div>
                            </div>
                            @error('title') 
                                <div class="mt-2 sm:mt-3 flex items-center text-red-500 dark:text-red-400 text-sm sm:text-base">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="group">
                            <label for="description" class="block text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4 transition-colors duration-300">
                                <i class="fas fa-align-left mr-2 text-emerald-500"></i>
                                Course Description
                            </label>
                            <div class="relative">
                                <textarea id="description" 
                                          wire:model="description" 
                                          rows="6"
                                          class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl sm:rounded-2xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 text-sm sm:text-base resize-none group-hover:border-gray-300 dark:group-hover:border-gray-500"
                                          placeholder="Describe what students will learn and achieve..."></textarea>
                                <div class="absolute right-3 sm:right-4 top-3 sm:top-4 text-gray-400">
                                    <i class="fas fa-file-text text-sm sm:text-base"></i>
                                </div>
                            </div>
                            @error('description') 
                                <div class="mt-2 sm:mt-3 flex items-center text-red-500 dark:text-red-400 text-sm sm:text-base">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="group">
                            <label for="category" class="block text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4 transition-colors duration-300">
                                <i class="fas fa-tags mr-2 text-purple-500"></i>
                                Category
                            </label>
                            <div class="relative">
                                <select id="category" 
                                        wire:model="category_id"
                                        class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl sm:rounded-2xl text-gray-900 dark:text-white focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 transition-all duration-300 text-sm sm:text-base appearance-none group-hover:border-gray-300 dark:group-hover:border-gray-500">
                                    <option value="">Select a Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3 sm:right-4 top-3 sm:top-4 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down text-sm sm:text-base"></i>
                                </div>
                            </div>
                            @error('category_id') 
                                <div class="mt-2 sm:mt-3 flex items-center text-red-500 dark:text-red-400 text-sm sm:text-base">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6 sm:space-y-8">

                        <!-- Difficulty Level -->
                        <div class="group">
                            <label for="difficulty" class="block text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4 transition-colors duration-300">
                                <i class="fas fa-chart-line mr-2 text-orange-500"></i>
                                Difficulty Level
                            </label>
                            <div class="relative">
                                <select id="difficulty" 
                                        wire:model="difficulty_level"
                                        class="w-full px-4 sm:px-6 py-3 sm:py-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl sm:rounded-2xl text-gray-900 dark:text-white focus:border-orange-500 dark:focus:border-orange-400 focus:ring-4 focus:ring-orange-500/20 transition-all duration-300 text-sm sm:text-base appearance-none group-hover:border-gray-300 dark:group-hover:border-gray-500">
                                    <option value="beginner">🌱 Beginner - Perfect for newcomers</option>
                                    <option value="intermediate">🚀 Intermediate - Some experience required</option>
                                    <option value="advanced">⚡ Advanced - For experienced learners</option>
                                    <option value="expert">🏆 Expert - Master level content</option>
                                </select>
                                <div class="absolute right-3 sm:right-4 top-3 sm:top-4 text-gray-400 pointer-events-none">
                                    <i class="fas fa-chevron-down text-sm sm:text-base"></i>
                                </div>
                            </div>
                            @error('difficulty_level') 
                                <div class="mt-2 sm:mt-3 flex items-center text-red-500 dark:text-red-400 text-sm sm:text-base">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Publication Status -->
                        <div class="group">
                            <label class="block text-sm sm:text-base font-bold text-gray-700 dark:text-gray-300 mb-3 sm:mb-4 transition-colors duration-300">
                                <i class="fas fa-globe mr-2 text-green-500"></i>
                                Publication Status
                            </label>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border-2 border-gray-200 dark:border-gray-600 transition-all duration-300 group-hover:border-gray-300 dark:group-hover:border-gray-500">
                                <label class="flex items-center cursor-pointer group/toggle">
                                    <div class="relative">
                                        <input id="is_published" 
                                               type="checkbox" 
                                               wire:model="is_published"
                                               class="sr-only">
                                        <div class="w-12 h-6 sm:w-14 sm:h-7 bg-gray-300 dark:bg-gray-600 rounded-full transition-all duration-300"
                                             :class="$wire.is_published ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'">
                                            <div class="absolute top-0.5 left-0.5 w-5 h-5 sm:w-6 sm:h-6 bg-white rounded-full transition-all duration-300 transform"
                                                 :class="$wire.is_published ? 'translate-x-6 sm:translate-x-7' : 'translate-x-0'">
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i class="text-xs transition-colors duration-300"
                                                       :class="$wire.is_published ? 'fas fa-check text-green-500' : 'fas fa-times text-gray-400'"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-3 sm:ml-4">
                                        <span class="text-sm sm:text-base font-bold text-gray-900 dark:text-white transition-colors duration-300"
                                              x-text="$wire.is_published ? 'Published & Live' : 'Draft Mode'"></span>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300"
                                           x-text="$wire.is_published ? 'Visible to all students' : 'Only visible to you'"></p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Course Stats Preview -->
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700/50 dark:to-gray-600/50 rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-blue-200 dark:border-gray-600 transition-colors duration-300">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center transition-colors duration-300">
                                <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                                Quick Preview
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-black text-blue-600 dark:text-blue-400 transition-colors duration-300">
                                        {{ strlen($title ?? '') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold transition-colors duration-300">Title Length</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 transition-colors duration-300">
                                        {{ str_word_count($description ?? '') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold transition-colors duration-300">Description Words</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row justify-end items-center gap-4 sm:gap-6 pt-8 sm:pt-10 mt-8 sm:mt-10 border-t border-gray-200 dark:border-gray-700">
                    
                    <!-- Cancel Button -->
                    <a href="{{ route('all-course') }}" 
                       class="w-full sm:w-auto group bg-white hover:bg-gray-50 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold border border-gray-200 dark:border-gray-600 transition-all duration-300 flex items-center justify-center transform hover:scale-105 text-sm sm:text-base">
                        <i class="fas fa-times mr-2 sm:mr-3 group-hover:rotate-90 transition-transform duration-300"></i>
                        Cancel Changes
                    </a>

                    <!-- Save Button -->
                    <button type="submit" 
                            :disabled="saving"
                            class="w-full sm:w-auto group bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 sm:px-12 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold transition-all duration-300 flex items-center justify-center transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-xl text-sm sm:text-base min-w-[200px]">
                        <template x-if="!saving">
                            <span class="flex items-center">
                                <i class="fas fa-save mr-2 sm:mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                                Update Course
                            </span>
                        </template>
                        <template x-if="saving">
                            <span class="flex items-center">
                                <i class="fas fa-spinner animate-spin mr-2 sm:mr-3"></i>
                                Updating...
                            </span>
                        </template>
                    </button>
                </div>

            </form>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl p-8 sm:p-12 flex flex-col items-center shadow-2xl border border-gray-200 dark:border-gray-700 transition-colors duration-300 mx-4">
                <div class="relative mb-4 sm:mb-6">
                    <div class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:w-20 border-4 border-blue-200 dark:border-gray-600"></div>
                    <div class="animate-spin rounded-full h-16 w-16 sm:h-20 sm:h-20 border-4 border-blue-600 border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-gray-800 dark:text-white font-black text-lg sm:text-xl text-center">Updating course...</span>
            </div>
        </div>

        <!-- Enhanced Toast Notifications -->
        <div x-data="{
            show: false,
            message: '',
            type: 'success',
            icon: 'fas fa-check-circle'
        }" @notify.window="
            show = true; 
            message = $event.detail.message; 
            type = $event.detail.type || 'success';
            icon = $event.detail.icon || 'fas fa-check-circle';
            setTimeout(() => show = false, 5000)
         " x-show="show" 
            x-transition:enter="transform transition-all duration-300 ease-out"
            x-transition:enter-start="translate-x-full opacity-0" 
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transform transition-all duration-300 ease-in"
            x-transition:leave-start="translate-x-0 opacity-100" 
            x-transition:leave-end="translate-x-full opacity-0"
            class="fixed top-4 sm:top-8 right-4 sm:right-8 z-50 max-w-sm sm:max-w-md" 
            x-cloak
            style="display: none;">

            <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden backdrop-blur-sm transition-colors duration-300">
                <div :class="{
                    'bg-gradient-to-r from-emerald-500 to-green-500': type === 'success',
                    'bg-gradient-to-r from-red-500 to-pink-500': type === 'error',
                    'bg-gradient-to-r from-blue-500 to-purple-500': type === 'info',
                    'bg-gradient-to-r from-yellow-500 to-orange-500': type === 'warning'
                }" class="p-4 sm:p-6">

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i :class="icon" class="text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                            <p class="text-white font-bold text-sm sm:text-lg leading-tight break-words" x-text="message"></p>
                        </div>
                        <button @click="show = false"
                            class="flex-shrink-0 ml-2 sm:ml-4 text-white hover:text-gray-200 transition-colors duration-200">
                            <i class="fas fa-times text-sm sm:text-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush
</div>