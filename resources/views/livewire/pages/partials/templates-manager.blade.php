<div class="bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Template Manager</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach(['default', 'landing', 'blog', 'full-width', 'minimal'] as $template)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="aspect-video bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                    <i class="fas fa-eye text-gray-400 text-2xl"></i>
                </div>
                <h3 class="font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('-', ' ', $template)) }} Template</h3>
                <p class="text-sm text-gray-600 mb-4">
                    {{ match($template) {
                        'default' => 'Standard page layout with sidebar and content area',
                        'landing' => 'Marketing-focused layout with hero sections and CTAs',
                        'blog' => 'Blog-style layout optimized for reading',
                        'full-width' => 'Full-width layout without sidebars',
                        'minimal' => 'Clean, distraction-free layout',
                    } }}
                </p>
                <div class="flex space-x-2">
                    <button class="flex-1 text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">
                        Preview
                    </button>
                    <button class="flex-1 text-sm px-3 py-1 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                        Customize
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
