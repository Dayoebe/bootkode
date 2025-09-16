{{-- resources/views/livewire/marketplace/vendor/partials/create-form.blade.php --}}

<!-- Main Form -->
<form wire:submit.prevent="submitForReview" class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
            Basic Information
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Title -->
            <div class="lg:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input wire:model="title" type="text" id="title" placeholder="Enter item title..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type <span class="text-red-500">*</span>
                </label>
                <select wire:model="type" id="type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @isset($types)
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    @endisset
                </select>
                @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                    Duration (minutes)
                </label>
                <input wire:model="duration_minutes" type="number" id="duration_minutes" placeholder="e.g., 60"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('duration_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Short Description -->
            <div class="lg:col-span-2">
                <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Short Description <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="short_description" id="short_description" rows="3"
                    placeholder="Brief description for search results and cards (max 160 characters)..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                <div class="flex justify-between items-center mt-1">
                    @error('short_description') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 ml-auto">{{ strlen($short_description) }}/160</p>
                </div>
            </div>

            <!-- Full Description -->
            <div class="lg:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Description <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="description" id="description" rows="8"
                    placeholder="Detailed description of your item..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                <p class="text-xs text-gray-500 mt-1">{{ strlen($description) }} characters (minimum 100 required)</p>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Pricing -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-dollar-sign mr-2 text-green-500"></i>
            Pricing & Type
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Price (₦) <span class="text-red-500">*</span>
                </label>
                <input wire:model="price" type="number" step="0.01" min="0" id="price" placeholder="0.00"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">
                    Discount Price (₦)
                </label>
                <input wire:model="discount_price" type="number" step="0.01" min="0" id="discount_price"
                    placeholder="Optional"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('discount_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-end">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer w-full">
                    <input wire:model="is_digital" type="checkbox"
                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-3 text-sm font-medium text-gray-700">Digital Product</span>
                </label>
            </div>
        </div>
        
        @if($price > 0 && $discount_price && $discount_price < $price)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800 flex items-center">
                    <i class="fas fa-percentage mr-2"></i>
                    <span class="font-medium">{{ round((($price - $discount_price) / $price) * 100) }}% discount</span>
                    <span class="ml-2">- Customers save ₦{{ number_format($price - $discount_price, 2) }}</span>
                </p>
            </div>
        @endif
    </div>

    <!-- Media Upload -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-images mr-2 text-purple-500"></i>
            Media & Files
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Thumbnail -->
            <div>
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                    Thumbnail Image <span class="text-red-500">*</span>
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                    <div class="space-y-1 text-center">
                        @if ($thumbnail)
                            <img src="{{ $thumbnail->temporaryUrl() }}" class="mx-auto h-24 w-24 object-cover rounded-lg">
                        @else
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        @endif
                        <div class="flex text-sm text-gray-600">
                            <label for="thumbnail"
                                class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                <span>{{ $thumbnail ? 'Change' : 'Upload' }} thumbnail</span>
                                <input wire:model="thumbnail" id="thumbnail" type="file" class="sr-only"
                                    accept="image/*">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                    </div>
                </div>
                @error('thumbnail') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Additional Images -->
            <div>
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional Images
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                    <div class="space-y-1 text-center">
                        @if ($images && count($images) > 0)
                            <div class="flex flex-wrap gap-2 justify-center">
                                @foreach($images as $image)
                                    <img src="{{ $image->temporaryUrl() }}" class="h-16 w-16 object-cover rounded">
                                @endforeach
                            </div>
                        @else
                            <i class="fas fa-images text-gray-400 text-3xl"></i>
                        @endif
                        <div class="flex text-sm text-gray-600">
                            <label for="images"
                                class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                <span>Upload images</span>
                                <input wire:model="images" id="images" type="file" class="sr-only" accept="image/*"
                                    multiple>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Multiple images allowed</p>
                    </div>
                </div>
                @error('images.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Files for Digital Products -->
        @if($is_digital)
            <div class="mt-6">
                <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file mr-1"></i>
                    Digital Files (for customers)
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-purple-400 transition-colors">
                    <div class="space-y-1 text-center">
                        @if ($files && count($files) > 0)
                            <div class="space-y-1">
                                @foreach($files as $file)
                                    <p class="text-sm text-gray-600 flex items-center justify-center">
                                        <i class="fas fa-file mr-2"></i>
                                        {{ $file->getClientOriginalName() }}
                                    </p>
                                @endforeach
                            </div>
                        @else
                            <i class="fas fa-file text-gray-400 text-3xl"></i>
                        @endif
                        <div class="flex text-sm text-gray-600">
                            <label for="files"
                                class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500">
                                <span>Upload files</span>
                                <input wire:model="files" id="files" type="file" class="sr-only" multiple>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PDF, ZIP, DOCX up to 10MB each</p>
                    </div>
                </div>
                @error('files.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif
    </div>

    <!-- Categories and Tags -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-tags mr-2 text-orange-500"></i>
            Categories & Tags
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label for="selectedCategories" class="block text-sm font-medium text-gray-700 mb-2">
                    Categories <span class="text-red-500">*</span>
                </label>
                <div class="border border-gray-300 rounded-lg p-3 max-h-48 overflow-y-auto">
                    @isset($availableCategories)
                        @foreach($availableCategories as $category)
                            <label class="flex items-center py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                                <input wire:model="selectedCategories" type="checkbox" value="{{ $category->id }}"
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-3 text-sm text-gray-700">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    @endisset
                </div>
                <p class="text-xs text-gray-500 mt-1">Select one or more categories that best describe your item</p>
                @error('selectedCategories') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tagInput" class="block text-sm font-medium text-gray-700 mb-2">
                    Tags
                </label>
                <div class="flex space-x-2">
                    <input wire:model="tagInput" wire:keydown.enter.prevent="addTag"
                        type="text" id="tagInput" placeholder="Enter a tag and press Enter"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <button type="button" wire:click="addTag"
                        class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <!-- Display current tags -->
                @if(!empty($tags))
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($tags as $index => $tag)
                            <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                {{ $tag }}
                                <button type="button" wire:click="removeTag({{ $index }})"
                                    class="ml-1 text-purple-600 hover:text-purple-800">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </span>
                        @endforeach
                    </div>
                @endif

                <p class="text-xs text-gray-500 mt-2">e.g., Laravel, PHP, Web Development, Beginner</p>
                @error('tags') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Selected Categories Preview -->
        @if(!empty($selectedCategories))
            <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-2">Selected Categories:</label>
                <div class="flex flex-wrap gap-2">
                    @isset($availableCategories)
                        @foreach($selectedCategories as $categoryId)
                            @foreach($availableCategories as $category)
                                @if($category->id == $categoryId)
                                    <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                        {{ $category->name }}
                                    </span>
                                @endif
                            @endforeach
                        @endforeach
                    @endisset
                </div>
            </div>
        @endif
    </div>

    <!-- SEO Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
            <i class="fas fa-search mr-2 text-blue-500"></i>
            SEO Settings
        </h3>

        <div class="space-y-4">
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Title <span class="text-red-500">*</span>
                </label>
                <input wire:model="meta_title" type="text" id="meta_title"
                    placeholder="Leave empty to use item title"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('meta_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Description <span class="text-red-500">*</span>
                </label>
                <textarea wire:model="meta_description" id="meta_description" rows="3"
                    placeholder="Leave empty to use short description"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                @error('meta_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="keywords" class="block text-sm font-medium text-gray-700 mb-2">
                    SEO Keywords
                </label>
                <input wire:model="keywords" type="text" id="keywords"
                    placeholder="SEO keywords separated by commas"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                @error('keywords') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">
            <p class="text-sm text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Items are saved as drafts by default. Submit for review when ready to publish.
            </p>

            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <button type="button" wire:click="saveDraft"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i>
                    Save Draft
                </button>

                <button type="submit"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i>
                    {{ $editingItemId ? 'Update Item' : 'Submit for Review' }}
                </button>
            </div>
        </div>
    </div>
</form>