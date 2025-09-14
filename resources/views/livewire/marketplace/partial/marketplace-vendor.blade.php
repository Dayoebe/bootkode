{{-- resources/views/livewire/marketplace/partial/marketplace-vendor.blade.php --}}
<div class="space-y-6">
    
    <!-- Internal Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <nav class="flex space-x-4">
                <button wire:click="showListings" 
                        class="{{ $currentView === 'listings' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 rounded-lg border transition-colors flex items-center">
                    <i class="fas fa-list-alt mr-2"></i>
                    My Listings
                </button>
                
                <button wire:click="showDrafts" 
                        class="{{ $currentView === 'drafts' ? 'bg-orange-100 text-orange-700 border-orange-200' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 rounded-lg border transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Drafts
                </button>
                
                <button wire:click="showCreate" 
                        class="{{ $currentView === 'create' ? 'bg-green-100 text-green-700 border-green-200' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 rounded-lg border transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    {{ $editingItemId ? 'Edit Item' : 'Create New' }}
                </button>
            </nav>
        </div>
    </div>

    <!-- Create/Edit Form View -->
    @if($currentView === 'create')
        <!-- Form Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-{{ $editingItemId ? 'blue' : 'green' }}-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-{{ $editingItemId ? 'edit' : 'plus' }} text-{{ $editingItemId ? 'blue' : 'green' }}-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">
                            {{ $editingItemId ? 'Edit Item' : 'Create New Listing' }}
                        </h2>
                        <p class="text-gray-600">
                            {{ $editingItemId ? 'Update your marketplace item' : 'Add a new item to the marketplace' }}
                        </p>
                    </div>
                </div>
                
                @if($editingItemId)
                    <button wire:click="showCreate" 
                            class="inline-flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Cancel Edit
                    </button>
                @endif
            </div>
        </div>

        <!-- Main Form -->
        <form wire:submit.prevent="save" class="space-y-6">
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
                            Thumbnail Image {{ !$editingItemId ? '*' : '' }}
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
                                @foreach($availableCategories as $categoryId => $categoryName)
                                    <label class="flex items-center py-2 hover:bg-gray-50 px-2 rounded cursor-pointer">
                                        <input wire:model="selectedCategories" type="checkbox" value="{{ $categoryId }}"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-3 text-sm text-gray-700">{{ $categoryName }}</span>
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
                                    @if(isset($availableCategories[$categoryId]))
                                        <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                            {{ $availableCategories[$categoryId] }}
                                        </span>
                                    @endif
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
                            Meta Title {{ !$editingItemId ? '*' : '' }}
                        </label>
                        <input wire:model="meta_title" type="text" id="meta_title"
                            placeholder="Leave empty to use item title"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        @error('meta_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Description {{ !$editingItemId ? '*' : '' }}
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

    <!-- Listings View -->
    @else
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-{{ $currentView === 'drafts' ? 'save' : 'list-alt' }} mr-2 text-{{ $currentView === 'drafts' ? 'orange' : 'purple' }}-600"></i>
                        @if($currentView === 'drafts')
                            {{ $isAdmin ? 'All Drafts' : 'My Drafts' }}
                        @else
                            {{ $isAdmin ? 'All Listings' : 'My Listings' }}
                        @endif
                    </h2>
                    <p class="text-gray-600">
                        @if($currentView === 'drafts')
                            {{ $isAdmin ? 'All draft items in the system' : 'Items you\'re still working on' }}
                        @else
                            {{ $isAdmin ? 'Manage all marketplace items' : 'Manage your marketplace items' }}
                        @endif
                    </p>
                </div>
                
                <button wire:click="showCreate" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create New Item
                </button>
            </div>
            
            <!-- Filters (only show for listings view) -->
            @if($currentView === 'listings')
                <div class="mt-4 flex flex-wrap gap-3">
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Search items..." 
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    
                    <select wire:model.live="status" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Status</option>
                        @isset($statuses)
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        @endisset
                    </select>
                    
                    @if($search || $status)
                        <button wire:click="$set('search', ''); $set('status', '')" 
                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Clear
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Items List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @isset($items)
                @if($items->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($items as $item)
                            <div class="p-6 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start space-x-4">
                                    <!-- Item Image -->
                                    <div class="flex-shrink-0">
                                        @if($item->getPrimaryImage())
                                            <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                                 alt="{{ $item->title }}" 
                                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Item Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-medium text-gray-900 hover:text-purple-600 transition-colors">
                                                    {{ $item->title }}
                                                </h3>
                                                
                                                @if($isAdmin)
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <i class="fas fa-user mr-1"></i>
                                                        Vendor: {{ $item->vendor->name }}
                                                    </p>
                                                @endif
                                                
                                                <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                                    {{ $item->short_description }}
                                                </p>
                                                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-eye mr-1"></i>
                                                        {{ number_format($item->views_count) }} views
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-shopping-cart mr-1"></i>
                                                        {{ number_format($item->sales_count) }} sales
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-star mr-1 text-yellow-400"></i>
                                                        {{ number_format($item->average_rating, 1) }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $item->updated_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Price and Status -->
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-semibold text-gray-900">
                                                    {{ $item->getFormattedPrice() }}
                                                    @if($item->hasDiscount())
                                                        <span class="text-sm text-gray-500 line-through block">
                                                            {{ $item->getFormattedOriginalPrice() }}
                                                        </span>
                                                    @endif
                                                </p>
                                                <span class="inline-flex items-center px-2 py-1 bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800 text-xs font-medium rounded-full mt-1">
                                                    <i class="fas fa-{{ $item->status === 'approved' ? 'check' : ($item->status === 'pending' ? 'clock' : ($item->status === 'rejected' ? 'times' : 'edit')) }} mr-1"></i>
                                                    {{ $item->status_name }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="mt-4 flex items-center flex-wrap gap-2">
                                            <button wire:click="editItem({{ $item->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </button>
                                            
                                            @if(!$isAdmin)
                                                @if($item->isDraft())
                                                    <button wire:click="submitForReviewFromList({{ $item->id }})"
                                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                        <i class="fas fa-paper-plane mr-1"></i>
                                                        Submit for Review
                                                    </button>
                                                @endif
                                                
                                                @if($item->isPending())
                                                    <button wire:click="withdrawSubmission({{ $item->id }})"
                                                            class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors">
                                                        <i class="fas fa-undo mr-1"></i>
                                                        Withdraw
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            @if($isAdmin && $item->isPending())
                                                <button wire:click="openApproveModal({{ $item->id }})"
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Approve
                                                </button>
                                            @endif
                                            
                                            @if($isAdmin && $item->isApproved())
                                                <button wire:click="openWithdrawModal({{ $item->id }})"
                                                        class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-colors">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Withdraw Approval
                                                </button>
                                            @endif
                                            
                                            <button wire:click="duplicateItem({{ $item->id }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                                <i class="fas fa-copy mr-1"></i>
                                                Duplicate
                                            </button>
                                            
                                            @if(!$item->orders()->exists())
                                                <button wire:click="deleteItem({{ $item->id }})"
                                                        onclick="confirm('Are you sure you want to delete this item? This action cannot be undone.') || event.stopImmediatePropagation()"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Delete
                                                </button>
                                            @endif
                                            
                                            @if($item->isPublished())
                                                <a href="{{ route('marketplace.item.public', $item->slug) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                                    <i class="fas fa-external-link-alt mr-1"></i>
                                                    View Public
                                                </a>
                                            @endif
                                        </div>
                                        
                                        <!-- Recent Orders -->
                                        @if($item->orders && $item->orders->count() > 0)
                                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                <h5 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                    <i class="fas fa-shopping-cart mr-1"></i>
                                                    Recent Orders:
                                                </h5>
                                                <div class="space-y-1">
                                                    @foreach($item->orders->take(3) as $order)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="flex items-center">
                                                                <i class="fas fa-user mr-1 text-gray-400"></i>
                                                                {{ $order->customer->name }}
                                                            </span>
                                                            <span class="text-{{ $order->status_color }}-600 font-medium">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $items->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <i class="fas fa-{{ $currentView === 'drafts' ? 'save' : 'box-open' }} text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">
                            @if($currentView === 'drafts')
                                @if($search || $status)
                                    No matching drafts found
                                @else
                                    No drafts yet
                                @endif
                            @else
                                @if($search || $status)
                                    No matching items found
                                @else
                                    No items found
                                @endif
                            @endif
                        </h3>
                        <p class="text-gray-500 mb-8">
                            @if($currentView === 'drafts')
                                @if($search || $status)
                                    Try adjusting your search criteria.
                                @else
                                    {{ $isAdmin ? 'No draft items in the system.' : 'You haven\'t saved any drafts yet.' }}
                                @endif
                            @else
                                @if($search || $status)
                                    Try adjusting your filters or search terms.
                                @else
                                    {{ $isAdmin ? 'No marketplace items found.' : 'You haven\'t created any marketplace items yet.' }}
                                @endif
                            @endif
                        </p>
                        
                        <div class="space-y-3">
                            @if($search || $status)
                                <button wire:click="$set('search', ''); $set('status', '')" 
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Clear Filters
                                </button>
                                <br>
                            @endif
                            
                            <button wire:click="showCreate" 
                                    class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                {{ ($currentView === 'drafts' || (!$search && !$status)) ? 'Create Your First Item' : 'Create New Item' }}
                            </button>
                        </div>
                    </div>
                @endif
            @endisset
        </div>
    @endif

    <!-- Admin Modals -->
    @include('livewire.marketplace.partial.vendor.vendor-modals')

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600"></div>
                <span class="text-gray-700">Processing...</span>
            </div>
        </div>
    </div>
</div>