<div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-4xl mx-auto my-8">
    <h2 class="text-3xl font-extrabold text-white mb-6 border-b border-gray-700 pb-4">
        Create New Course
    </h2>

    <form wire:submit.prevent="createCourse" class="space-y-6">
        <!-- Title -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Course Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" wire:model.defer="title"
                   class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm"
                   placeholder="e.g., Introduction to Web Development">
            @error('title') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea id="description" wire:model.defer="description" rows="5"
                      class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm"
                      placeholder="Provide a detailed description of the course content and objectives."></textarea>
            @error('description') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Category & Difficulty Level -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                <select id="category_id" wire:model.defer="category_id"
                        class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white shadow-sm">
                    <option value="">Select a Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="difficulty_level" class="block text-sm font-medium text-gray-300 mb-2">Difficulty Level <span class="text-red-500">*</span></label>
                <select id="difficulty_level" wire:model.defer="difficulty_level"
                        class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white shadow-sm">
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
                @error('difficulty_level') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Estimated Duration -->
        <div>
            <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-300 mb-2">Estimated Duration (minutes)</label>
            <input type="number" id="estimated_duration_minutes" wire:model.defer="estimated_duration_minutes"
                   class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm"
                   placeholder="e.g., 180 (for 3 hours)">
            @error('estimated_duration_minutes') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
        </div>

        <!-- Thumbnail Upload -->
        <div>
            <label for="thumbnail" class="block text-sm font-medium text-gray-300 mb-2">Course Thumbnail</label>
            <input type="file" id="thumbnail" wire:model="thumbnail"
                   class="block w-full text-sm text-gray-400
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100 cursor-pointer">
            @error('thumbnail') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror

            @if ($thumbnail)
                <div class="mt-4">
                    <p class="text-sm text-gray-400">Thumbnail Preview:</p>
                    <img src="{{ $thumbnail->temporaryUrl() }}" class="mt-2 rounded-lg max-w-xs h-auto shadow-md border border-gray-700">
                </div>
            @endif
        </div>

        <!-- Premium Status & Price -->
        <div class="flex items-center space-x-4">
            <input type="checkbox" id="is_premium" wire:model.live="is_premium"
                   class="h-5 w-5 text-blue-600 rounded border-gray-600 bg-gray-700 focus:ring-blue-500">
            <label for="is_premium" class="text-sm font-medium text-gray-300">Mark as Premium Course</label>
        </div>

        @if ($is_premium)
            <div>
                <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price (e.g., 29.99)</label>
                <input type="number" id="price" wire:model.defer="price" step="0.01" min="0"
                       class="w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm"
                       placeholder="0.00">
                @error('price') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>
        @endif

        <!-- Published Status (Admins/Instructors can publish, but admin approval is separate) -->
        <div class="flex items-center space-x-4">
            <input type="checkbox" id="is_published" wire:model.defer="is_published"
                   class="h-5 w-5 text-green-600 rounded border-gray-600 bg-gray-700 focus:ring-green-500">
            <label for="is_published" class="text-sm font-medium text-gray-300">Publish Course Immediately</label>
        </div>

        <!-- Approved Status (Only Super Admin/Academy Admin can set this) -->
        @if (Auth::user()->isSuperAdmin() || Auth::user()->isAcademyAdmin())
            <div class="flex items-center space-x-4">
                <input type="checkbox" id="is_approved" wire:model.defer="is_approved"
                       class="h-5 w-5 text-purple-600 rounded border-gray-600 bg-gray-700 focus:ring-purple-500">
                <label for="is_approved" class="text-sm font-medium text-gray-300">Approve Course</label>
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-end pt-4 border-t border-gray-700 mt-6">
            <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-md flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Create Course
            </button>
        </div>
    </form>
</div>