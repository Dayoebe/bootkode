<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-question mr-2"></i> FAQ Management
        </h1>
        <p class="text-amber-100 mt-2">Create and manage frequently asked questions</p>
    </div>

    <!-- Form for Create/Update -->
    <form wire:submit.prevent="saveFaq" class="bg-themed-secondary shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp border border-themed-primary transition-colors duration-300">
        <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
            <i class="fas fa-plus-circle text-amber-600 dark:text-amber-400 mr-2"></i>{{ $editId ? 'Update' : 'Create' }} FAQ
        </h3>

        <div class="space-y-6">
            <div>
                <label for="question" class="block text-sm font-semibold text-themed-primary mb-2">
                    <i class="fas fa-heading mr-2 text-amber-600 dark:text-amber-400"></i>Question <span class="text-red-600 dark:text-red-400">*</span>
                </label>
                <input wire:model="question" type="text" id="question"
                       class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-themed-primary transition-colors duration-200"
                       placeholder="Enter the FAQ question">
                @error('question') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="answer" class="block text-sm font-semibold text-themed-primary mb-2">
                    <i class="fas fa-align-left mr-2 text-amber-600 dark:text-amber-400"></i>Answer <span class="text-red-600 dark:text-red-400">*</span>
                </label>
                <textarea wire:model="answer" id="answer" rows="6"
                          class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-themed-primary transition-colors duration-200"
                          placeholder="Enter the detailed answer..."></textarea>
                @error('answer') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="order" class="block text-sm font-semibold text-themed-primary mb-2">
                    <i class="fas fa-sort-numeric-up mr-2 text-amber-600 dark:text-amber-400"></i>Display Order
                </label>
                <input wire:model="order" type="number" id="order" min="0"
                       class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-themed-primary transition-colors duration-200"
                       placeholder="Enter order number (0, 1, 2, ...)">
                @error('order') <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                <p class="text-xs text-themed-secondary mt-1">Lower numbers appear first</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            @if($editId)
                <button type="button" wire:click="$set('editId', null)"
                        class="px-4 py-2 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors font-medium">
                    Cancel
                </button>
            @endif
            <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:focus:ring-offset-themed-secondary disabled:opacity-50 transition-colors duration-200">
                <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $editId ? 'Update' : 'Create' }} FAQ</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
            </button>
        </div>
    </form>

    <!-- FAQ List -->
    <div class="bg-themed-secondary shadow rounded-lg overflow-hidden border border-themed-primary transition-colors duration-300">
        <!-- Header with Search -->
        <div class="px-6 py-4 border-b border-themed-primary bg-themed-tertiary">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="flex-1 w-full">
                    <label for="search" class="block text-sm font-medium text-themed-primary mb-2">Search FAQs</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search"
                               placeholder="Search questions..."
                               class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-themed-primary transition-colors duration-200">
                    </div>
                </div>
                <div class="text-sm text-themed-secondary mt-2 sm:mt-0">
                    Total: <strong class="text-themed-primary">{{ $faqs->total() }}</strong>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead class="bg-themed-tertiary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Question</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Answer Preview</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-themed-primary">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp">
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 font-semibold">
                                    {{ $faq->order }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <p class="font-medium text-themed-primary">{{ Str::limit($faq->question, 50) }}</p>
                                <p class="text-themed-secondary text-xs mt-1">{{ $faq->question }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-themed-secondary">
                                {{ Str::limit(strip_tags($faq->answer), 60) }}...
                            </td>
                            <td class="px-6 py-4 text-sm text-themed-secondary">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-amber-600 dark:text-amber-400"></i>
                                    {{ $faq->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="editFaq({{ $faq->id }})"
                                            class="p-2 text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-300 hover:bg-amber-100/30 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="deleteFaq({{ $faq->id }})" wire:confirm="Delete this FAQ permanently?"
                                            class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 hover:bg-red-100/30 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="bg-themed-tertiary rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-themed-secondary"></i>
                                </div>
                                <p class="text-themed-secondary text-lg font-medium">No FAQs found</p>
                                <p class="text-themed-tertiary text-sm mt-1">Create your first FAQ to get started</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($faqs->hasPages())
            <div class="p-4 border-t border-themed-primary flex justify-center">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-amber-100 dark:bg-amber-900/30">
                    <i class="fas fa-question-circle text-amber-600 dark:text-amber-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Total FAQs</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $faqs->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-plus-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Last Added</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $faqs->first()?->created_at->format('M d') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Status</p>
                    <p class="text-2xl font-bold text-themed-primary text-green-600 dark:text-green-400">Active</p>
                </div>
            </div>
        </div>
    </div>
</div>