<div class="space-y-6">
    <!-- Header and Controls -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">White-label Settings</h2>
            <p class="text-sm text-gray-600">Customize branding for your partner institutions</p>
        </div>
    </div>

    <!-- Institutions List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Institution
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Platform Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Custom Domain
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Branding
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($institutions as $institution)
                        @php
                            $whitelabelSettings = $institution->whitelabel_settings ?? [];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($institution->logo)
                                            <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($institution->logo) }}" alt="{{ $institution->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                                <span class="text-white font-semibold text-sm">{{ substr($institution->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($institution->name, 30) }}</div>
                                        <div class="text-sm text-gray-500">{{ $institution->institution_type_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $whitelabelSettings['platform_name'] ?? $institution->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($whitelabelSettings['custom_domain'] ?? false)
                                    <div class="text-sm text-gray-900">{{ $whitelabelSettings['custom_domain'] }}</div>
                                    <div class="text-xs text-green-600">
                                        <i class="fas fa-check mr-1"></i>Active
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500">No custom domain</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if($whitelabelSettings['primary_color'] ?? false)
                                        <div class="w-4 h-4 rounded-full border border-gray-300" 
                                             style="background-color: {{ $whitelabelSettings['primary_color'] }}"></div>
                                    @endif
                                    @if($whitelabelSettings['logo_url'] ?? false)
                                        <span class="text-xs text-green-600">
                                            <i class="fas fa-image mr-1"></i>Logo
                                        </span>
                                    @endif
                                    @if($whitelabelSettings['custom_css'] ?? false)
                                        <span class="text-xs text-blue-600">
                                            <i class="fas fa-code mr-1"></i>CSS
                                        </span>
                                    @endif
                                    @if(!($whitelabelSettings['primary_color'] ?? false) && !($whitelabelSettings['logo_url'] ?? false))
                                        <span class="text-xs text-gray-500">Default</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="openEditModal({{ $institution->id }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                        title="Edit Settings">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-paint-roller text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No institutions found</h3>
                                    <p class="text-sm text-gray-500">No active institutions available for white-labeling.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($institutions->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>

    <!-- Edit Settings Modal -->
    @if($showEditModal && $selectedInstitution)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeModal">
            <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto" wire:click.stop>
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900">White-label Settings - {{ $selectedInstitution->name }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveSettings" class="mt-6">
                    <div class="space-y-8">
                        <!-- Basic Branding -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Basic Branding</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Platform Name *</label>
                                    <input type="text" wire:model="settings.platform_name" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('settings.platform_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Custom Domain</label>
                                    <input type="text" wire:model="settings.custom_domain" 
                                           placeholder="learning.company.com"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('settings.custom_domain') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Enter domain without protocol (https://)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Color Scheme -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Color Scheme</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Primary Color *</label>
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input type="color" wire:model.live="settings.primary_color" 
                                               class="h-10 w-20 border border-gray-300 rounded-md">
                                        <input type="text" wire:model="settings.primary_color" 
                                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    @error('settings.primary_color') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Secondary Color *</label>
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input type="color" wire:model.live="settings.secondary_color" 
                                               class="h-10 w-20 border border-gray-300 rounded-md">
                                        <input type="text" wire:model="settings.secondary_color" 
                                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    @error('settings.secondary_color') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Color Preview -->
                            <div class="mt-4 p-4 border border-gray-300 rounded-lg">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Color Preview</h5>
                                <div class="flex space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded" style="background-color: {{ $settings['primary_color'] }}"></div>
                                        <span class="text-sm">Primary</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 rounded" style="background-color: {{ $settings['secondary_color'] }}"></div>
                                        <span class="text-sm">Secondary</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo & Favicon -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Logo & Favicon</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Logo</label>
                                    <div class="mt-1">
                                        <input type="file" wire:model="logoFile" accept="image/*" 
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                    @error('logoFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    
                                    @if($settings['logo_url'] && !$logoFile)
                                        <div class="mt-2">
                                            <img src="{{ $settings['logo_url'] }}" alt="Current logo" class="w-24 h-24 object-cover rounded-lg border">
                                        </div>
                                    @endif
                                    
                                    @if($logoFile)
                                        <div class="mt-2">
                                            <img src="{{ $logoFile->temporaryUrl() }}" alt="New logo preview" class="w-24 h-24 object-cover rounded-lg border">
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Favicon</label>
                                    <div class="mt-1">
                                        <input type="file" wire:model="faviconFile" accept="image/*" 
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                    @error('faviconFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    
                                    @if($settings['favicon_url'] && !$faviconFile)
                                        <div class="mt-2">
                                            <img src="{{ $settings['favicon_url'] }}" alt="Current favicon" class="w-8 h-8 object-cover rounded border">
                                        </div>
                                    @endif
                                    
                                    @if($faviconFile)
                                        <div class="mt-2">
                                            <img src="{{ $faviconFile->temporaryUrl() }}" alt="New favicon preview" class="w-8 h-8 object-cover rounded border">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Advanced Settings</h4>
                            
                            <!-- Hide Powered By -->
                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="settings.hide_powered_by" id="hide_powered_by"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="hide_powered_by" class="ml-2 block text-sm text-gray-900">
                                        Hide "Powered by {{ config('app.name') }}" branding
                                    </label>
                                </div>
                            </div>

                            <!-- Custom CSS -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Custom CSS</label>
                                <textarea wire:model="settings.custom_css" rows="8" 
                                          placeholder="/* Add your custom CSS here */"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"></textarea>
                                @error('settings.custom_css') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1">CSS will be injected into the head of all pages</p>
                            </div>
                        </div>

                        <!-- Email Templates -->
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Email Templates</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Header</label>
                                    <textarea wire:model="settings.email_template_header" rows="4" 
                                              placeholder="Custom header for email templates..."
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('settings.email_template_header') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email Footer</label>
                                    <textarea wire:model="settings.email_template_footer" rows="4" 
                                              placeholder="Custom footer for email templates..."
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('settings.email_template_footer') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-between space-x-3 mt-8 pt-6 border-t border-gray-200 sticky bottom-0 bg-white">
                        <button type="button" wire:click="resetToDefaults" 
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md transition-colors">
                            Reset to Defaults
                        </button>
                        <div class="flex space-x-3">
                            <button type="button" wire:click="closeModal" 
                                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 text-sm font-medium rounded-md transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>