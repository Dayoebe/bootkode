<div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-7xl mx-auto my-8">
    <h2 class="text-3xl font-extrabold text-white mb-6 border-b border-gray-700 pb-4">
        Edit Course: {{ $title }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-300">Course Title</label>
            <input type="text" id="title" wire:model="title"
                   class="mt-1 w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400">
            @error('title') <span class="mt-2 text-sm text-red-400">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
            <textarea id="description" wire:model="description" rows="5"
                      class="mt-1 w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400"></textarea>
            @error('description') <span class="mt-2 text-sm text-red-400">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-300">Category</label>
            <select id="category" wire:model="category_id"
                    class="mt-1 w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white">
                <option value="">Select a Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <span class="mt-2 text-sm text-red-400">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="difficulty" class="block text-sm font-medium text-gray-300">Difficulty Level</label>
            <select id="difficulty" wire:model="difficulty_level"
                    class="mt-1 w-full px-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
            </select>
            @error('difficulty_level') <span class="mt-2 text-sm text-red-400">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center">
            <input id="is_published" type="checkbox" wire:model="is_published"
                   class="h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
            <label for="is_published" class="ml-2 block text-sm text-gray-300">
                Published
            </label>
        </div>

        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-700">
            <a href="{{ route('all-course') }}" class="px-6 py-2.5 text-gray-300 hover:text-white rounded-xl font-semibold border border-gray-600 hover:bg-gray-700 transition-colors duration-200">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors duration-200">
                Update Course
            </button>
        </div>
    </form>
</div>