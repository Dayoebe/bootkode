<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-6 rounded-2xl shadow-xl text-white mb-8">
        <h1 class="text-3xl font-bold flex items-center">
            <i class="fas fa-language mr-3"></i>
            Language & Localization
        </h1>
        <p class="text-emerald-100 mt-2">Manage platform languages, translations, and regional settings</p>
        <div class="mt-3 text-sm text-emerald-200">
            <i class="fas fa-crown mr-1"></i>
            Super Admin Only
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-themed-secondary rounded-xl shadow-sm mb-8 border border-themed-primary overflow-hidden">
        <div class="border-b border-themed-primary">
            <nav class="-mb-px flex space-x-1 px-4 sm:px-6 overflow-x-auto" aria-label="Tabs">
                <button 
                    wire:click="setActiveTab('languages')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'languages' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-100/10 dark:bg-emerald-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-globe mr-2"></i>
                    Languages
                </button>
                <button 
                    wire:click="setActiveTab('regional')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'regional' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-100/10 dark:bg-emerald-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-map mr-2"></i>
                    Regional Settings
                </button>
                <button 
                    wire:click="setActiveTab('translations')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'translations' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-100/10 dark:bg-emerald-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-file-alt mr-2"></i>
                    Translations
                </button>
                <button 
                    wire:click="setActiveTab('rtl')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'rtl' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-100/10 dark:bg-emerald-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-align-right mr-2"></i>
                    RTL Support
                </button>
                <button 
                    wire:click="setActiveTab('system')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'system' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400 bg-emerald-100/10 dark:bg-emerald-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-tools mr-2"></i>
                    System
                </button>
            </nav>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Languages Tab -->
        @if($activeTab === 'languages')
            <form wire:submit.prevent="saveLanguageSettings" class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden animate__animated animate__fadeIn">
                <div class="px-6 py-4 border-b border-themed-primary">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <i class="fas fa-globe text-emerald-600 dark:text-emerald-400 mr-2"></i>
                        Language Configuration
                    </h3>
                    <p class="text-sm text-themed-secondary mt-1">Configure available languages and default settings</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Default & Fallback Languages -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_language" class="block text-sm font-medium text-themed-primary mb-2">
                                <i class="fas fa-star mr-2 text-emerald-600"></i>Default Language
                            </label>
                            <select wire:model="default_language" id="default_language"
                                    class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                <option value="en">English</option>
                                <option value="es">Español</option>
                                <option value="fr">Français</option>
                                <option value="de">Deutsch</option>
                                <option value="pt">Português</option>
                                <option value="ar">العربية</option>
                            </select>
                        </div>

                        <div>
                            <label for="fallback_language" class="block text-sm font-medium text-themed-primary mb-2">
                                <i class="fas fa-shield-alt mr-2 text-emerald-600"></i>Fallback Language
                            </label>
                            <select wire:model="fallback_language" id="fallback_language"
                                    class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                <option value="en">English</option>
                                <option value="es">Español</option>
                                <option value="fr">Français</option>
                            </select>
                        </div>
                    </div>

                    <!-- Available Languages Grid -->
                    <div>
                        <h4 class="font-medium text-themed-primary mb-4">Available Languages</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach(['en' => 'English', 'es' => 'Español', 'fr' => 'Français', 'de' => 'Deutsch', 'pt' => 'Português', 'ar' => 'العربية', 'zh' => '中文', 'ja' => '日本語'] as $code => $lang)
                                <label class="flex items-center p-4 border border-themed-primary rounded-lg hover:border-emerald-300 hover:bg-themed-tertiary transition-all cursor-pointer">
                                    <input wire:click="toggleLanguage('{{ $code }}')" type="checkbox" 
                                           {{ in_array($code, $enabled_languages) ? 'checked' : '' }}
                                           class="h-4 w-4 text-emerald-600 border-themed-primary rounded focus:ring-emerald-500">
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-themed-primary">{{ $lang }}</p>
                                        <p class="text-xs text-themed-secondary">{{ $code }}</p>
                                    </div>
                                    @if($code === 'ar')
                                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">RTL</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Language Summary -->
                    @if(count($enabled_languages) > 0)
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            <h4 class="font-medium text-emerald-900 dark:text-emerald-100 mb-2">Enabled Languages ({{ count($enabled_languages) }})</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($enabled_languages as $code)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm 
                                               {{ $code === $default_language ? 'bg-emerald-200 dark:bg-emerald-800 text-emerald-800 dark:text-emerald-100' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                        {{ strtoupper($code) }}
                                        @if($code === $default_language)
                                            <i class="fas fa-star ml-1 text-xs"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-themed-primary flex justify-between items-center">
                    <div class="text-sm text-themed-secondary">
                        <i class="fas fa-info-circle mr-1"></i>
                        Changes apply immediately
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Languages</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        @endif

        <!-- Regional Settings Tab -->
        @if($activeTab === 'regional')
            <form wire:submit.prevent="saveRegionalSettings" class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden animate__animated animate__fadeIn">
                <div class="px-6 py-4 border-b border-themed-primary">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <i class="fas fa-map text-emerald-600 dark:text-emerald-400 mr-2"></i>
                        Regional Settings
                    </h3>
                    <p class="text-sm text-themed-secondary mt-1">Configure timezone, currency, and date/time formats</p>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_timezone" class="block text-sm font-medium text-themed-primary mb-2">Default Timezone</label>
                            <select wire:model="default_timezone" id="default_timezone"
                                    class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York</option>
                                <option value="Europe/London">Europe/London</option>
                                <option value="Europe/Paris">Europe/Paris</option>
                                <option value="Asia/Tokyo">Asia/Tokyo</option>
                                <option value="Australia/Sydney">Australia/Sydney</option>
                            </select>
                        </div>

                        <div>
                            <label for="default_currency" class="block text-sm font-medium text-themed-primary mb-2">Default Currency</label>
                            <select wire:model="default_currency" id="default_currency"
                                    class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                                <option value="NGN">NGN - Nigerian Naira</option>
                            </select>
                        </div>

                        <div>
                            <label for="date_format" class="block text-sm font-medium text-themed-primary mb-2">Date Format</label>
                            <select wire:model="date_format" id="date_format"
                                    class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                <option value="m/d/Y">MM/DD/YYYY</option>
                                <option value="d/m/Y">DD/MM/YYYY</option>
                                <option value="Y-m-d">YYYY-MM-DD</option>
                                <option value="d.m.Y">DD.MM.YYYY</option>
                            </select>
                        </div>

                        <div>
                            <label for="time_format" class="block text-sm font-medium text-themed-primary mb-2">Time Format</label>
                            <select wire:model="time_format" id="time_format"
                                    class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                <option value="h:i A">12 Hour (h:i AM/PM)</option>
                                <option value="H:i">24 Hour (H:i)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-themed-tertiary p-4 rounded-lg">
                        <h4 class="font-medium text-themed-primary mb-2">Preview</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-themed-secondary">Sample Date:</span>
                                <p class="font-medium text-themed-primary">{{ now()->format($date_format) }}</p>
                            </div>
                            <div>
                                <span class="text-themed-secondary">Sample Time:</span>
                                <p class="font-medium text-themed-primary">{{ now()->format($time_format) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-themed-primary flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Regional Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        @endif

        <!-- Translations Tab -->
        @if($activeTab === 'translations')
            <div class="space-y-6">
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden animate__animated animate__fadeIn">
                    <div class="px-6 py-4 border-b border-themed-primary">
                        <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                            <i class="fas fa-file-alt text-emerald-600 dark:text-emerald-400 mr-2"></i>
                            Translation Management
                        </h3>
                        <p class="text-sm text-themed-secondary mt-1">Import, export, and manage translations</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Translation Progress -->
                        <div>
                            <h4 class="font-medium text-themed-primary mb-4">Translation Progress</h4>
                            <div class="space-y-3">
                                @foreach($enabled_languages as $code)
                                    <div class="flex items-center justify-between p-3 border border-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors">
                                        <div class="flex-1">
                                            <p class="font-medium text-themed-primary">{{ strtoupper($code) }}</p>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="w-32 bg-themed-tertiary rounded-full h-2">
                                                <div class="bg-emerald-500 h-2 rounded-full" style="width: 75%"></div>
                                            </div>
                                            <span class="text-sm font-medium min-w-[3rem] text-right text-themed-primary">75%</span>
                                            <button wire:click="exportTranslations('{{ $code }}')"
                                                    class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded text-xs hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors">
                                                <i class="fas fa-download mr-1"></i>Export
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Import Form -->
                        <div class="border-t border-themed-primary pt-6">
                            <h4 class="font-medium text-themed-primary mb-4">Import Translations</h4>
                            <form wire:submit.prevent="importTranslations" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="target_language" class="block text-sm font-medium text-themed-primary mb-2">Target Language</label>
                                        <select wire:model="target_language" id="target_language"
                                                class="block w-full border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-themed-primary transition-colors duration-200 px-4 py-2">
                                            @foreach($enabled_languages as $code)
                                                <option value="{{ $code }}">{{ strtoupper($code) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="import_file" class="block text-sm font-medium text-themed-primary mb-2">Translation File (JSON)</label>
                                        <input wire:model="import_file" type="file" id="import_file" accept=".json"
                                               class="block w-full text-sm text-themed-secondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-50 dark:file:bg-emerald-900/30 file:text-emerald-700 dark:file:text-emerald-400 hover:file:bg-emerald-100 dark:hover:file:bg-emerald-900/50 transition-colors">
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" wire:loading.attr="disabled"
                                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 text-white rounded-lg transition-colors disabled:opacity-50">
                                        <span wire:loading.remove><i class="fas fa-upload mr-2"></i>Import</span>
                                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Importing...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- RTL Support Tab -->
        @if($activeTab === 'rtl')
            <form wire:submit.prevent="saveRtlSettings" class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden animate__animated animate__fadeIn">
                <div class="px-6 py-4 border-b border-themed-primary">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <i class="fas fa-align-right text-emerald-600 dark:text-emerald-400 mr-2"></i>
                        Right-to-Left (RTL) Support
                    </h3>
                    <p class="text-sm text-themed-secondary mt-1">Configure RTL language support and layout</p>
                </div>

                <div class="p-6 space-y-6">
                    <label class="flex items-center cursor-pointer group">
                        <input wire:model="enable_rtl_support" type="checkbox"
                               class="h-4 w-4 text-emerald-600 border-themed-primary rounded focus:ring-emerald-500">
                        <div class="ml-3 group-hover:translate-x-1 transition-transform">
                            <span class="text-sm font-medium text-themed-primary">Enable RTL Support</span>
                            <p class="text-xs text-themed-secondary">Automatically apply RTL layout for supported languages</p>
                        </div>
                    </label>

                    @if($enable_rtl_support)
                        <div class="ml-7 space-y-4 border-l-2 border-emerald-100 pl-4">
                            <h4 class="font-medium text-themed-primary">Supported RTL Languages</h4>
                            <div class="space-y-2">
                                @foreach(['ar' => 'Arabic', 'he' => 'Hebrew', 'ur' => 'Urdu'] as $code => $name)
                                    <div class="p-3 border border-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors">
                                        <p class="font-medium text-themed-primary">{{ $name }}</p>
                                        <p class="text-xs text-themed-secondary">{{ $code }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-themed-primary flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 text-white px-6 py-2 rounded-lg transition-colors duration-200 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save RTL Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        @endif

        <!-- System Tab -->
        @if($activeTab === 'system')
            <div class="space-y-6">
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden animate__animated animate__fadeIn">
                    <div class="px-6 py-4 border-b border-themed-primary">
                        <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                            <i class="fas fa-tools text-emerald-600 dark:text-emerald-400 mr-2"></i>
                            System Actions
                        </h3>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button wire:click="clearTranslationCache" wire:loading.attr="disabled"
                                    class="px-4 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white rounded-lg transition-colors disabled:opacity-50 font-medium">
                                <span wire:loading.remove><i class="fas fa-trash mr-2"></i>Clear Translation Cache</span>
                                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Clearing...</span>
                            </button>

                            <button wire:click="rebuildLanguageFiles" wire:loading.attr="disabled"
                                    class="px-4 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white rounded-lg transition-colors disabled:opacity-50 font-medium">
                                <span wire:loading.remove><i class="fas fa-hammer mr-2"></i>Rebuild Language Files</span>
                                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Rebuilding...</span>
                            </button>

                            <button wire:click="validateTranslations" wire:loading.attr="disabled"
                                    class="px-4 py-3 bg-purple-600 hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-800 text-white rounded-lg transition-colors disabled:opacity-50 font-medium">
                                <span wire:loading.remove><i class="fas fa-check-circle mr-2"></i>Validate Translations</span>
                                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Validating...</span>
                            </button>

                            <button wire:click="optimizeTranslations" wire:loading.attr="disabled"
                                    class="px-4 py-3 bg-orange-600 hover:bg-orange-700 dark:bg-orange-700 dark:hover:bg-orange-800 text-white rounded-lg transition-colors disabled:opacity-50 font-medium">
                                <span wire:loading.remove><i class="fas fa-rocket mr-2"></i>Optimize</span>
                                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Optimizing...</span>
                            </button>
                        </div>

                        <!-- System Info -->
                        <div class="mt-8 pt-6 border-t border-themed-primary">
                            <h4 class="font-medium text-themed-primary mb-4">System Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-themed-tertiary rounded-lg">
                                    <p class="text-sm text-themed-secondary">Active Languages</p>
                                    <p class="text-2xl font-bold text-themed-primary">{{ count($enabled_languages) }}</p>
                                </div>
                                <div class="p-4 bg-themed-tertiary rounded-lg">
                                    <p class="text-sm text-themed-secondary">Default Language</p>
                                    <p class="text-2xl font-bold text-themed-primary">{{ strtoupper($default_language) }}</p>
                                </div>
                                <div class="p-4 bg-themed-tertiary rounded-lg">
                                    <p class="text-sm text-themed-secondary">RTL Support</p>
                                    <p class="text-2xl font-bold text-themed-primary">{{ $enable_rtl_support ? 'Enabled' : 'Disabled' }}</p>
                                </div>
                                <div class="p-4 bg-themed-tertiary rounded-lg">
                                    <p class="text-sm text-themed-secondary">Timezone</p>
                                    <p class="text-lg font-bold text-themed-primary">{{ $default_timezone }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>