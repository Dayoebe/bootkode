<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-question mr-2"></i> FAQ Management
        </h1>
    </div>
    <!-- Form for Create/Update -->
    <form wire:submit.prevent="saveFaq" class="bg-white shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp">
        <div class="space-y-6">
            <div>
                <label for="question" class="block text-sm font-medium text-gray-700">Question</label>
                <input wire:model="question" type="text" id="question"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('question') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="answer" class="block text-sm font-medium text-gray-700">Answer</label>
                <textarea wire:model="answer" id="answer" rows="5"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('answer') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
                <input wire:model="order" type="number" id="order" min="0"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                <span wire:loading.remove><i class="fas fa-save mr-2"></i> {{ $editId ? 'Update' : 'Create' }} FAQ</span>
                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
            </button>
        </div>
    </form>
    <!-- FAQ List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($faqs as $faq)
                    <tr class="animate__animated animate__fadeInUp">
                        <td class="px-6 py-4">{{ $faq->question }}</td>
                        <td class="px-6 py-4">{{ $faq->order }}</td>
                        <td class="px-6 py-4">
                            <button wire:click="editFaq({{ $faq->id }})" class="text-blue-600 mr-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteFaq({{ $faq->id }})" class="text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $faqs->links() }}</div>
    </div>
</div>