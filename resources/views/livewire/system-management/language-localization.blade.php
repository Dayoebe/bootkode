<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
    <div class="bg-white rounded-lg shadow-sm mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6 overflow-x-auto" aria-label="Tabs">
                <button 
                    wire:click="setActiveTab('languages')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'languages' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-globe mr-2"></i>
                    Languages
                </button>
                <button 
                    wire:click="setActiveTab('regional')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'regional' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-map mr-2"></i>
                    Regional Settings
                </button>
                <button 
                    wire:click="setActiveTab('translations')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'translations' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-file-alt mr-2"></i>
                    Translations
                </button>
                <button 
                    wire:click="setActiveTab('rtl')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'rtl' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-align-right mr-2"></i>
                    RTL Support
                </button>
                <button 
                    wire:click="setActiveTab('auto-translation')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'auto-translation' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-robot mr-2"></i>
                    Auto-Translation
                </button>
                <button 
                    wire:click="setActiveTab('import-export')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'import-export' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Import/Export
                </button>
                <button 
                    wire:click="setActiveTab('system-actions')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'system-actions' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-tools mr-2"></i>
                    System Actions
                </button>
            </nav>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Languages Tab -->
        <div class="{{ $activeTab === 'languages' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveLanguageSettings" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-globe text-emerald-600 mr-2"></i>
                        Language Configuration
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Configure available languages and default settings</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Default Language Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="default_language" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Language
                            </label>
                            <select wire:model="default_language" id="default_language"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($available_languages as $code => $lang)
                                    <option value="{{ $code }}">{{ $lang['flag'] }} {{ $lang['name'] }} ({{ $lang['native'] }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="fallback_language" class="block text-sm font-medium text-gray-700 mb-2">
                                Fallback Language
                            </label>
                            <select wire:model="fallback_language" id="fallback_language"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($available_languages as $code => $lang)
                                    <option value="{{ $code }}">{{ $lang['flag'] }} {{ $lang['name'] }} ({{ $lang['native'] }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Available Languages -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Available Languages</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($available_languages as $code => $lang)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-emerald-300 transition-colors">
                                    <label class="flex items-center cursor-pointer">
                                        <input wire:click="toggleLanguage('{{ $code }}')" 
                                               type="checkbox" 
                                               {{ in_array($code, $enabled_languages) ? 'checked' : '' }}
                                               class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-2xl">{{ $lang['flag'] }}</span>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $lang['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $lang['native'] }}</p>
                                                </div>
                                            </div>
                                            @if($lang['rtl'])
                                                <span class="inline-block mt-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">RTL</span>
                                            @endif
                                        </div>
                                    </label>

                                    @if(in_array($code, $enabled_languages) && isset($translation_progress[$code]))
                                        <div class="mt-2">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-500">Translation Progress</span>
                                                <span class="font-medium">{{ $translation_progress[$code] }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-emerald-500 h-2 rounded-full" 
                                                     style="width: {{ $translation_progress[$code] }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Enabled Languages Summary -->
                    @if(count($enabled_languages) > 0)
                        <div class="bg-emerald-50 p-4 rounded-lg">
                            <h4 class="font-medium text-emerald-900 mb-2">Enabled Languages ({{ count($enabled_languages) }})</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($enabled_languages as $code)
                                    @php $lang = $available_languages[$code] ?? null @endphp
                                    @if($lang)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm 
                                                   {{ $code === $default_language ? 'bg-emerald-200 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $lang['flag'] }} {{ $lang['name'] }}
                                            @if($code === $default_language)
                                                <i class="fas fa-star ml-1 text-xs"></i>
                                            @endif
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Changes will be applied immediately after saving
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Language Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Regional Settings Tab -->
        <div class="{{ $activeTab === 'regional' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveRegionalSettings" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-map text-emerald-600 mr-2"></i>
                        Regional Settings
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Configure timezone, currency, and date/time formats</p>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Timezone -->
                        <div>
                            <label for="default_timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Timezone
                            </label>
                            <select wire:model="default_timezone" id="default_timezone"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($this->getTimezoneOptions() as $tz => $name)
                                    <option value="{{ $tz }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Currency -->
                        <div>
                            <label for="default_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Currency
                            </label>
                            <select wire:model="default_currency" id="default_currency"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($this->getCurrencyOptions() as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Format -->
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                                Date Format
                            </label>
                            <select wire:model="date_format" id="date_format"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($this->getDateFormatOptions() as $format => $example)
                                    <option value="{{ $format }}">{{ $example }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Time Format -->
                        <div>
                            <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">
                                Time Format
                            </label>
                            <select wire:model="time_format" id="time_format"
                                    class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($this->getTimeFormatOptions() as $format => $example)
                                    <option value="{{ $format }}">{{ $example }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Preview</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Current date/time:</span><br>
                                <span class="font-medium">{{ now()->setTimezone($default_timezone)->format($date_format . ' ' . $time_format) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Sample price:</span><br>
                                <span class="font-medium">
                                    @switch($default_currency)
                                        @case('USD') $99.99 @break
                                        @case('EUR') €99.99 @break
                                        @case('GBP') £99.99 @break
                                        @case('NGN') ₦99.99 @break
                                        @default $99.99
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Regional Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Translations Tab -->
        <div class="{{ $activeTab === 'translations' ? 'block' : 'hidden' }}">
            <div class="space-y-6">
                <!-- Translation Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-file-alt text-emerald-600 mr-2"></i>
                            Translation Management
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Import, export, and manage translations</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Translation Progress -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Translation Progress</h4>
                            <div class="space-y-3">
                                @foreach($enabled_languages as $code)
                                    @php $lang = $available_languages[$code] ?? null @endphp
                                    @if($lang)
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-xl">{{ $lang['flag'] }}</span>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $lang['name'] }}</p>
                                                    <p class="text-sm text-gray-500">{{ $lang['native'] }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <div class="text-right">
                                                    <div class="text-sm font-medium">{{ $translation_progress[$code] ?? 0 }}%</div>
                                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                                        <div class="bg-emerald-500 h-2 rounded-full" 
                                                             style="width: {{ $translation_progress[$code] ?? 0 }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button wire:click="generateLanguageFiles('{{ $code }}')" 
                                                            class="text-blue-600 hover:text-blue-700 text-sm px-2 py-1 rounded border border-blue-200 hover:bg-blue-50">
                                                        <i class="fas fa-plus"></i> Generate
                                                    </button>
                                                    <button wire:click="exportTranslations('{{ $code }}')" 
                                                            class="text-green-600 hover:text-green-700 text-sm px-2 py-1 rounded border border-green-200 hover:bg-green-50">
                                                        <i class="fas fa-download"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Import Translations -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-4">Import Translations</h4>
                            <form wire:submit.prevent="importTranslations">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="selected_language_for_translation" class="block text-sm font-medium text-gray-700 mb-2">
                                            Target Language
                                        </label>
                                        <select wire:model="selected_language_for_translation" id="selected_language_for_translation"
                                                class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                            @foreach($enabled_languages as $code)
                                                @php $lang = $available_languages[$code] ?? null @endphp
                                                @if($lang)
                                                    <option value="{{ $code }}">{{ $lang['flag'] }} {{ $lang['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="translation_file" class="block text-sm font-medium text-gray-700 mb-2">
                                            Translation File (JSON)
                                        </label>
                                        <input wire:model="translation_file" type="file" id="translation_file" accept=".json"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                    </div>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" wire:loading.attr="disabled"
                                            class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                                        <span wire:loading.remove><i class="fas fa-upload mr-2"></i>Import Translations</span>
                                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Importing...</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center">
                            <button wire:click="clearTranslationCache" 
                                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-sync mr-2"></i>Clear Translation Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RTL Support Tab -->
        <div class="{{ $activeTab === 'rtl' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveRtlSettings" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-align-right text-emerald-600 mr-2"></i>
                        Right-to-Left (RTL) Support
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Configure RTL language support and layout</p>
                </div>

                <div class="p-6 space-y-6">
                    <label class="flex items-center cursor-pointer">
                        <input wire:model="enable_rtl_support" type="checkbox"
                               class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700">Enable RTL Support</span>
                            <p class="text-xs text-gray-500">Automatically detect and apply RTL layout for supported languages</p>
                        </div>
                    </label>

                    @if($enable_rtl_support)
                        <div class="ml-7 space-y-4 border-l-2 border-emerald-100 pl-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3">RTL Languages</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($rtl_languages as $code)
                                        @php $lang = $available_languages[$code] ?? null @endphp
                                        @if($lang)
                                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-xl">{{ $lang['flag'] }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $lang['name'] }}</p>
                                                        <p class="text-sm text-gray-500">{{ $lang['native'] }}</p>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">RTL</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h5 class="font-medium text-blue-900 mb-2">RTL Layout Features</h5>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li class="flex items-center"><i class="fas fa-check mr-2"></i> Automatic text direction detection</li>
                                    <li class="flex items-center"><i class="fas fa-check mr-2"></i> Mirrored navigation and menus</li>
                                    <li class="flex items-center"><i class="fas fa-check mr-2"></i> Reversed layout for forms and buttons</li>
                                    <li class="flex items-center"><i class="fas fa-check mr-2"></i> RTL-appropriate icons and graphics</li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                        <span wire:loading.remove"><i class="fas fa-save mr-2"></i>Save RTL Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Auto-Translation Tab -->
        <div class="{{ $activeTab === 'auto-translation' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveTranslationSettings" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-robot text-emerald-600 mr-2"></i>
                        Auto-Translation Settings
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Configure automatic translation services and settings</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Enable Auto-Translation -->
                    <div class="flex items-start space-x-3">
                        <input wire:model="auto_translation_enabled" type="checkbox" id="auto_translation_enabled"
                               class="mt-1 h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <div class="flex-1">
                            <label for="auto_translation_enabled" class="text-sm font-medium text-gray-700">
                                Enable automatic translation
                            </label>
                            <p class="text-sm text-gray-500">
                                Automatically translate missing strings using external translation services
                            </p>
                        </div>
                    </div>

                    @if($auto_translation_enabled)
                        <div class="ml-7 space-y-6 border-l-2 border-emerald-100 pl-6">
                            <!-- Translation Service -->
                            <div>
                                <label for="auto_translation_service" class="block text-sm font-medium text-gray-700 mb-2">
                                    Translation Service
                                </label>
                                <select wire:model="auto_translation_service" id="auto_translation_service"
                                        class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="google">Google Translate</option>
                                    <option value="deepl">DeepL</option>
                                    <option value="azure">Azure Translator</option>
                                    <option value="aws">AWS Translate</option>
                                </select>
                                <p class="text-sm text-gray-500 mt-1">Choose your preferred translation service</p>
                            </div>

                            <!-- API Key -->
                            <div>
                                <label for="translation_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                    API Key
                                </label>
                                <input wire:model="translation_api_key" type="password" id="translation_api_key"
                                       class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                       placeholder="Enter your translation service API key">
                                <p class="text-sm text-gray-500 mt-1">API key for the selected translation service</p>
                            </div>

                            <!-- Translation Quality -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Translation Quality Settings
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input wire:model="auto_translate_missing_only" type="checkbox"
                                               class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-700">Only translate missing strings</span>
                                            <p class="text-xs text-gray-500">Don't overwrite existing translations</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input wire:model="auto_translate_require_approval" type="checkbox"
                                               class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <div class="ml-3">
                                            <span class="text-sm font-medium text-gray-700">Require manual approval</span>
                                            <p class="text-xs text-gray-500">Auto-translations need approval before use</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Test Translation -->
                            <div class="bg-emerald-50 p-4 rounded-lg">
                                <h4 class="font-medium text-emerald-900 mb-2">Test Translation</h4>
                                <div class="space-y-3">
                                    <input type="text" placeholder="Enter text to test translation..."
                                           wire:model.defer="test_translation_text"
                                           class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                    <div class="flex space-x-3">
                                        <select wire:model="test_source_lang" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                            <option value="en">English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                        </select>
                                        <span class="text-gray-500 self-center">→</span>
                                        <select wire:model="test_target_lang" class="border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                            <option value="de">German</option>
                                        </select>
                                        <button type="button" wire:click="testTranslation"
                                                class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 text-sm">
                                            Test Translation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                        <span wire:loading.remove"><i class="fas fa-save mr-2"></i>Save Translation Settings</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Import/Export Tab -->
        <div class="{{ $activeTab === 'import-export' ? 'block' : 'hidden' }}">
            <div class="space-y-6">
                <!-- Bulk Translation Import -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-file-import text-emerald-600 mr-2"></i>
                            Import Translations
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Import translation files in various formats</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <form wire:submit.prevent="bulkImportTranslations">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="import_language" class="block text-sm font-medium text-gray-700 mb-2">
                                        Target Language
                                    </label>
                                    <select wire:model="import_language" id="import_language"
                                            class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">Select Language</option>
                                        @foreach($enabled_languages as $code)
                                            @php $lang = $available_languages[$code] ?? null @endphp
                                            @if($lang)
                                                <option value="{{ $code }}">{{ $lang['flag'] }} {{ $lang['name'] }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="import_format" class="block text-sm font-medium text-gray-700 mb-2">
                                        File Format
                                    </label>
                                    <select wire:model="import_format" id="import_format"
                                            class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="json">JSON (.json)</option>
                                        <option value="csv">CSV (.csv)</option>
                                        <option value="xlsx">Excel (.xlsx)</option>
                                        <option value="po">Gettext (.po)</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="bulk_import_file" class="block text-sm font-medium text-gray-700 mb-2">
                                        Translation File
                                    </label>
                                    <input wire:model="bulk_import_file" type="file" id="bulk_import_file" 
                                           accept=".json,.csv,.xlsx,.po"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                    <p class="text-xs text-gray-500 mt-1">Supported formats: JSON, CSV, Excel, Gettext PO files</p>
                                </div>

                                <div class="md:col-span-2">
                                    <div class="flex items-center space-x-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input wire:model="overwrite_existing" type="checkbox"
                                                   class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                            <span class="ml-2 text-sm text-gray-700">Overwrite existing translations</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input wire:model="validate_before_import" type="checkbox"
                                                   class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                            <span class="ml-2 text-sm text-gray-700">Validate before importing</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" wire:click="downloadImportTemplate"
                                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-download mr-2"></i>Download Template
                                </button>
                                <button type="submit" wire:loading.attr="disabled"
                                        class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                                    <span wire:loading.remove"><i class="fas fa-upload mr-2"></i>Import Translations</span>
                                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Importing...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bulk Translation Export -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-file-export text-emerald-600 mr-2"></i>
                            Export Translations
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Export translations for external editing</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            @foreach($enabled_languages as $code)
                                @php $lang = $available_languages[$code] ?? null @endphp
                                @if($lang)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xl">{{ $lang['flag'] }}</span>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $lang['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $translation_progress[$code] ?? 0 }}% complete</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <button wire:click="exportTranslations('{{ $code }}', 'json')" 
                                                    class="w-full text-left px-3 py-2 text-sm bg-gray-50 hover:bg-gray-100 rounded flex items-center">
                                                <i class="fas fa-download mr-2"></i>JSON
                                            </button>
                                            <button wire:click="exportTranslations('{{ $code }}', 'csv')" 
                                                    class="w-full text-left px-3 py-2 text-sm bg-gray-50 hover:bg-gray-100 rounded flex items-center">
                                                <i class="fas fa-download mr-2"></i>CSV
                                            </button>
                                            <button wire:click="exportTranslations('{{ $code }}', 'xlsx')" 
                                                    class="w-full text-left px-3 py-2 text-sm bg-gray-50 hover:bg-gray-100 rounded flex items-center">
                                                <i class="fas fa-download mr-2"></i>Excel
                                            </button>
                                            <button wire:click="exportTranslations('{{ $code }}', 'po')" 
                                                    class="w-full text-left px-3 py-2 text-sm bg-gray-50 hover:bg-gray-100 rounded flex items-center">
                                                <i class="fas fa-download mr-2"></i>Gettext
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Bulk Export Options -->
                        <div class="border-t pt-6">
                            <h4 class="font-medium text-gray-900 mb-4">Bulk Export Options</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="export_format" class="block text-sm font-medium text-gray-700 mb-2">
                                        Export Format
                                    </label>
                                    <select wire:model="export_format" id="export_format"
                                            class="block w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="json">JSON</option>
                                        <option value="csv">CSV</option>
                                        <option value="xlsx">Excel</option>
                                        <option value="po">Gettext PO</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button wire:click="exportAllTranslations" 
                                            class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                                        <i class="fas fa-download mr-2"></i>Export All Languages
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Actions Tab -->
        <div class="{{ $activeTab === 'system-actions' ? 'block' : 'hidden' }}">
            <div class="space-y-6">
                <!-- Cache Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-sync text-emerald-600 mr-2"></i>
                            Cache Management
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Manage translation and language cache</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <button wire:click="clearTranslationCache" wire:loading.attr="disabled"
                                        class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                    <span wire:loading.remove wire:target="clearTranslationCache">
                                        <i class="fas fa-trash mr-2"></i>Clear Translation Cache
                                    </span>
                                    <span wire:loading wire:target="clearTranslationCache">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>Clearing...
                                    </span>
                                </button>

                                <button wire:click="clearLanguageCache" wire:loading.attr="disabled"
                                        class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center">
                                    <span wire:loading.remove wire:target="clearLanguageCache">
                                        <i class="fas fa-globe mr-2"></i>Clear Language Cache
                                    </span>
                                    <span wire:loading wire:target="clearLanguageCache">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>Clearing...
                                    </span>
                                </button>

                                <button wire:click="clearAllCaches" wire:loading.attr="disabled"
                                        class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center">
                                    <span wire:loading.remove wire:target="clearAllCaches">
                                        <i class="fas fa-bomb mr-2"></i>Clear All Caches
                                    </span>
                                    <span wire:loading wire:target="clearAllCaches">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>Clearing...
                                    </span>
                                </button>
                            </div>

                            <div class="space-y-4">
                                <button wire:click="rebuildLanguageFiles" wire:loading.attr="disabled"
                                        class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <span wire:loading.remove wire:target="rebuildLanguageFiles">
                                        <i class="fas fa-hammer mr-2"></i>Rebuild Language Files
                                    </span>
                                    <span wire:loading wire:target="rebuildLanguageFiles">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>Rebuilding...
                                    </span>
                                </button>

                                <button wire:click="optimizeTranslations" wire:loading.attr="disabled"
                                        class="w-full bg-orange-600 text-white px-4 py-3 rounded-lg hover:bg-orange-700 transition-colors flex items-center justify-center">
                                    <span wire:loading.remove wire:target="optimizeTranslations">
                                        <i class="fas fa-rocket mr-2"></i>Optimize Translations
                                    </span>
                                    <span wire:loading wire:target="optimizeTranslations">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>Optimizing...
                                    </span>
                                </button>

                                <button wire:click="validateTranslations" wire:loading.attr="disabled"
                                        class="w-full bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center">
                                    <span wire:loading.remove wire:target="validateTranslations">
                                        <i class="fas fa-check-circle mr-2"></i>Validate Translations
                                    </span>
                                    <span wire:loading wire:target="validateTranslations">
                                        <i class="fas fa-circle-notch fa-spin mr-2"></i>Validating...
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="font-medium text-gray-900 mb-3">System Information</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Last cache clear:</span><br>
                                        <span class="font-medium">{{ cache('last_translation_cache_clear', 'Never') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Translation files:</span><br>
                                        <span class="font-medium">{{ count($enabled_languages) }} languages active</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Cache size:</span><br>
                                        <span class="font-medium">~{{ number_format(cache('translation_cache_size', 0) / 1024, 1) }} KB</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Auto-translation:</span><br>
                                        <span class="font-medium">{{ $auto_translation_enabled ? 'Enabled' : 'Disabled' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics & Analytics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-chart-bar text-emerald-600 mr-2"></i>
                            Translation Statistics
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Overview of translation completeness and system status</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ count($enabled_languages) }}</div>
                                <div class="text-sm text-blue-700">Active Languages</div>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ number_format(collect($translation_progress)->avg(), 1) }}%
                                </div>
                                <div class="text-sm text-green-700">Avg Completion</div>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ collect($translation_progress)->filter(fn($p) => $p >= 80)->count() }}
                                </div>
                                <div class="text-sm text-purple-700">Well Translated (80%+)</div>
                            </div>

                            <div class="bg-orange-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-orange-600">
                                    {{ collect($rtl_languages)->intersect($enabled_languages)->count() }}
                                </div>
                                <div class="text-sm text-orange-700">RTL Languages</div>
                            </div>
                        </div>

                        <!-- Detailed Progress Chart -->
                        <div class="border-t pt-6">
                            <h4 class="font-medium text-gray-900 mb-4">Language Progress Details</h4>
                            <div class="space-y-3">
                                @foreach($enabled_languages as $code)
                                    @php 
                                        $lang = $available_languages[$code] ?? null;
                                        $progress = $translation_progress[$code] ?? 0;
                                        $progressColor = $progress >= 90 ? 'bg-green-500' : ($progress >= 70 ? 'bg-yellow-500' : ($progress >= 50 ? 'bg-orange-500' : 'bg-red-500'));
                                    @endphp
                                    @if($lang)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <span class="text-lg">{{ $lang['flag'] }}</span>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $lang['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $lang['native'] }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                                    <div class="{{ $progressColor }} h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium min-w-[3rem] text-right">{{ $progress }}%</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>