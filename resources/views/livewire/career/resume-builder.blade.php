<div class="min-h-screen bg-themed-primary">
    <!-- Header -->
    <div class="bg-themed-secondary shadow-sm border-b border-themed-primary">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col xl:flex-row justify-between xl:items-center gap-4 py-6">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="bg-action p-3 rounded-lg">
                        <i aria-hidden="true" class="fas fa-file-alt text-action-foreground text-xl"></i>
                    </div>
                    <div>
                        <h1 class=" font-bold text-themed-primary bk-page-title">Resume Builder</h1>
                        <p class="text-themed-secondary mt-1">Create your professional resume with AI assistance</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="text-right mr-4">
                        <div class="text-sm text-themed-secondary">Completion</div>
                        <div class="text-2xl font-bold text-brand">{{ $completionPercentage }}%</div>
                    </div>

                    <button wire:click="setViewMode('preview')"
                        class="bg-action hover:bg-action/90 text-action-foreground px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i aria-hidden="true" class="fas fa-eye mr-2"></i>Preview
                    </button>

                    <div class="relative" x-data="{ open: false }" @keydown.escape.stop="open = false; $refs.exportTrigger.focus()">
                        <button type="button" x-ref="exportTrigger" @click="open = !open" :aria-expanded="open"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <i aria-hidden="true" class="fas fa-download mr-2"></i>Export
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-themed-secondary rounded-lg shadow-sm z-20 border border-themed-primary">
                            <button wire:click="exportPDF"
                                class="block w-full text-left px-4 py-3 text-sm text-themed-primary hover:bg-themed-tertiary flex items-center transition-colors">
                                <i aria-hidden="true" class="fas fa-file-pdf mr-3 text-red-500"></i>Export as PDF
                            </button>
                            <button wire:click="exportJSON"
                                class="block w-full text-left px-4 py-3 text-sm text-themed-primary hover:bg-themed-tertiary flex items-center transition-colors">
                                <i aria-hidden="true" class="fas fa-file-code mr-3 text-brand"></i>Export as JSON
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="px-4 sm:px-6 lg:px-8 py-4">
        <div class="bg-themed-secondary rounded-lg p-6 shadow-sm border border-themed-primary">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm font-semibold text-themed-primary">Resume Progress</span>
                    <span class="px-3 py-1 text-xs font-medium rounded-full {{ $qualityScore >= 80 ? 'bg-green-100 text-green-800' : ($qualityScore >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        Quality Score: {{ $qualityScore }}/100
                    </span>
                </div>
                <span class="text-lg font-bold text-brand">{{ $completionPercentage }}%</span>
            </div>
            <div class="w-full bg-themed-tertiary rounded-full h-3">
                <div class="bg-surface-muted   h-3 rounded-full transition-all duration-500"
                    style="width: {{ $completionPercentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        @if($viewMode === 'edit')
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar -->
                <div class="min-w-0 lg:col-span-1">
                    <div class="bg-themed-secondary rounded-lg shadow-sm p-6 sticky top-6 border border-themed-primary">
                        <h3 class="text-lg font-semibold text-themed-primary mb-6">Resume Sections</h3>
                        <nav class="space-y-2">
                            @foreach($sections as $key => $section)
                                <button wire:click="setActiveSection('{{ $key }}')"
                                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200
                                                                    {{ $activeSection === $key ? 'bg-action text-action-foreground border-l-4 border-accent-themed-secondary' : 'text-themed-primary hover:bg-themed-tertiary' }}">
                                    <div class="flex items-center">
                                        <i aria-hidden="true" class="{{ $section['icon'] }} mr-3"></i>
                                        {{ $section['label'] }}
                                    </div>
                                    @if($section['required'])
                                        <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                    @endif
                                </button>
                            @endforeach
                        </nav>

                        <!-- Template Preview -->
                        <div class="mt-8 pt-6 border-t border-themed-primary">
                            <h4 class="text-sm font-semibold text-themed-primary mb-4">Template</h4>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($templates as $key => $template)
                                    <button wire:click="updateTemplate('{{ $key }}')"
                                        class="relative p-3 text-left rounded-lg border-2 transition-all duration-200
                                                                        {{ $selectedTemplate === $key ? 'border-brand bg-themed-tertiary' : 'border-themed-primary hover:border-themed-secondary bg-themed-secondary' }}">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-medium text-sm text-themed-primary">{{ $template['name'] }}</div>
                                                <div class="text-xs text-themed-secondary">{{ $template['description'] }}</div>
                                            </div>
                                            @if($template['is_premium'])
                                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">PRO</span>
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="min-w-0 lg:col-span-3">
                    <!-- Section Content -->
                    <div class="bg-themed-secondary rounded-lg shadow-sm p-4 sm:p-8 mb-8 border border-themed-primary">
                        @include('livewire.career.resume.resume-section')
                    </div>

                    <!-- Suggestions -->
                    @if(count($suggestions) > 0)
                        <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border border-themed-primary">
                            <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
                                <i aria-hidden="true" class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                AI Suggestions
                            </h3>
                            <div class="space-y-3">
                                @foreach($suggestions as $suggestion)
                                    <div class="p-4 rounded-lg border-l-4
                                        {{ $suggestion['type'] === 'critical' ? 'border-red-400 bg-red-50' :
                                        ($suggestion['type'] === 'important' ? 'border-yellow-400 bg-yellow-50' : 'border-blue-400 bg-blue-50') }}">
                                        <h4 class="font-medium text-themed-primary">{{ $suggestion['title'] }}</h4>
                                        <p class="text-sm text-themed-secondary mt-1">{{ $suggestion['description'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($viewMode === 'preview')
            <!-- Preview Mode -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-themed-secondary rounded-lg shadow-sm mb-6 p-4 flex items-center justify-between border border-themed-primary">
                    <button wire:click="setViewMode('edit')"
                        class="bg-themed-tertiary text-themed-primary hover:bg-themed-primary hover:text-themed-secondary px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i aria-hidden="true" class="fas fa-arrow-left mr-2"></i>Back to Editor
                    </button>
                    <div class="flex space-x-2">
                        @foreach($templates as $key => $template)
                            <button wire:click="updateTemplate('{{ $key }}')"
                                class="px-3 py-1 text-xs rounded {{ $selectedTemplate === $key ? 'bg-action text-action-foreground' : 'bg-themed-tertiary text-themed-primary hover:bg-themed-secondary' }} transition-colors">
                                {{ $template['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="bg-themed-secondary rounded-lg shadow-sm overflow-hidden border border-themed-primary">
                    @include('livewire.career.resume.pdf-template')
                </div>
            </div>

        @elseif($viewMode === 'settings')
            <!-- Settings Mode -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-themed-secondary rounded-lg shadow-sm p-8 border border-themed-primary">
                    <h2 class="text-2xl font-bold text-themed-primary mb-6">Resume Settings</h2>

                    <!-- Color Schemes -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-themed-primary mb-4">Color Scheme</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($colorSchemes as $key => $scheme)
                                <button wire:click="updateColorScheme('{{ $key }}')"
                                    class="p-4 rounded-lg border-2 transition-all duration-200 text-left bg-themed-secondary
                                        {{ $selectedColorScheme === $key ? 'border-brand' : 'border-themed-primary hover:border-themed-secondary' }}">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <div class="w-6 h-6 rounded-full" style="background-color: {{ $scheme['primary'] }}">
                                        </div>
                                        <div class="w-6 h-6 rounded-full" style="background-color: {{ $scheme['secondary'] }}">
                                        </div>
                                    </div>
                                    <div class="font-medium text-themed-primary">{{ $scheme['name'] }}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Fonts -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-themed-primary mb-4">Font Family</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($fonts as $key => $font)
                                <button wire:click="updateFont('{{ $key }}')"
                                    class="p-4 rounded-lg border-2 transition-all duration-200 text-left bg-themed-secondary
                                        {{ $selectedFont === $key ? 'border-brand' : 'border-themed-primary hover:border-themed-secondary' }}">
                                    <div class="font-medium text-themed-primary">{{ $font }}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Public Sharing -->
                    <div class="pt-6 border-t border-themed-primary">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-themed-primary">Public Access</h3>
                                <p class="text-sm text-themed-secondary">Allow others to view your resume via public link</p>
                            </div>
                            <button wire:click="togglePublicAccess()" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2
                                {{ $resume->is_public ? 'bg-action' : 'bg-themed-tertiary' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-surface shadow ring-0 transition duration-200 ease-in-out
                                    {{ $resume->is_public ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        @if($resume->is_public)
                            <div class="mt-4 p-4 bg-brand/10 rounded-lg border border-brand">
                                <p class="text-sm font-medium text-brand">Public URL:</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <input type="text" readonly value="{{ route('resume.public', $resume->public_slug) }}"
                                        class="flex-1 px-3 py-2 border border-brand rounded text-sm bg-themed-secondary text-themed-primary">
                                    <button onclick="copyToClipboard(this.previousElementSibling.value)"
                                        class="px-4 py-2 bg-action text-action-foreground rounded text-sm hover:bg-action transition-colors">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-sm z-50">
            <div class="flex items-center">
                <i aria-hidden="true" class="fas fa-check-circle mr-3"></i>
                {{ session('success') }}
                <button @click="show = false" class="ml-4 text-green-200 hover:text-white transition-colors" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-sm z-50">
            <div class="flex items-center">
                <i aria-hidden="true" class="fas fa-exclamation-circle mr-3"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-4 text-red-200 hover:text-white transition-colors" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="fixed bottom-4 right-4 bg-action text-action-foreground px-6 py-4 rounded-lg shadow-sm z-50">
            <div class="flex items-center">
                <i aria-hidden="true" class="fas fa-info-circle mr-3"></i>
                {{ session('info') }}
                <button @click="show = false" class="ml-4 text-brand hover:text-white transition-colors" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-themed-secondary rounded-lg p-8 max-w-sm mx-4 border border-themed-primary">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand"></div>
                <div>
                    <div class="font-medium text-themed-primary">Processing...</div>
                    <div class="text-sm text-themed-secondary">Please wait while we generate your resume</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-hide flash messages
    setTimeout(() => {
        const alerts = document.querySelectorAll('[x-data*="show: true"]');
        alerts.forEach(alert => {
            if (alert.__x && alert.__x.$data) {
                alert.__x.$data.show = false;
            }
        });
    }, 5000);

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show temporary success message
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
            notification.textContent = 'Copied to clipboard!';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 2000);
        });
    }
</script>
