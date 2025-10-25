<?php

namespace App\Livewire\SystemManagement;

use App\Models\Core\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard', [
    'title' => 'Language & Localization',
    'description' => 'Manage platform languages and localization settings',
    'icon' => 'fas fa-language',
    'active' => 'language_localization'
])]
class LanguageLocalization extends Component
{
    use WithFileUploads;

    public $activeTab = 'languages';
    
    // Language Management
    public $available_languages = [];
    public $enabled_languages = [];
    public $default_language = 'en';
    public $fallback_language = 'en';
    
    // Regional Settings
    public $default_timezone = 'UTC';
    public $default_currency = 'USD';
    public $date_format = 'Y-m-d';
    public $time_format = 'H:i';
    public $number_format = 'en';
    
    // Translation Management
    public $selected_language_for_translation = 'en';
    public $translation_file = null;
    public $translation_progress = [];
    public $missing_translations = [];
    
    // RTL Support
    public $rtl_languages = ['ar', 'he', 'fa', 'ur'];
    public $enable_rtl_support = true;
    
    // Auto-translation
    public $auto_translation_enabled = false;
    public $auto_translation_service = 'google';
    public $translation_api_key = '';

    public function mount()
    {
        // Check if user is Super Admin
        if (!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)) {
            abort(403, 'Unauthorized access. Super Admin role required.');
        }

        $this->loadLanguageSettings();
        $this->loadAvailableLanguages();
        $this->loadTranslationProgress();
    }

    private function loadLanguageSettings()
    {
        $settings = cache('language_settings', []);
        
        $this->enabled_languages = $settings['enabled_languages'] ?? ['en'];
        $this->default_language = $settings['default_language'] ?? 'en';
        $this->fallback_language = $settings['fallback_language'] ?? 'en';
        $this->default_timezone = $settings['default_timezone'] ?? 'UTC';
        $this->default_currency = $settings['default_currency'] ?? 'USD';
        $this->date_format = $settings['date_format'] ?? 'Y-m-d';
        $this->time_format = $settings['time_format'] ?? 'H:i';
        $this->number_format = $settings['number_format'] ?? 'en';
        $this->enable_rtl_support = $settings['enable_rtl_support'] ?? true;
        $this->auto_translation_enabled = $settings['auto_translation_enabled'] ?? false;
        $this->auto_translation_service = $settings['auto_translation_service'] ?? 'google';
        $this->translation_api_key = $settings['translation_api_key'] ?? '';
    }

    private function loadAvailableLanguages()
    {
        $this->available_languages = [
            'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸', 'rtl' => false],
            'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => '🇪🇸', 'rtl' => false],
            'fr' => ['name' => 'French', 'native' => 'Français', 'flag' => '🇫🇷', 'rtl' => false],
            'de' => ['name' => 'German', 'native' => 'Deutsch', 'flag' => '🇩🇪', 'rtl' => false],
            'it' => ['name' => 'Italian', 'native' => 'Italiano', 'flag' => '🇮🇹', 'rtl' => false],
            'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'flag' => '🇵🇹', 'rtl' => false],
            'ru' => ['name' => 'Russian', 'native' => 'Русский', 'flag' => '🇷🇺', 'rtl' => false],
            'zh' => ['name' => 'Chinese', 'native' => '中文', 'flag' => '🇨🇳', 'rtl' => false],
            'ja' => ['name' => 'Japanese', 'native' => '日本語', 'flag' => '🇯🇵', 'rtl' => false],
            'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦', 'rtl' => true],
            'he' => ['name' => 'Hebrew', 'native' => 'עברית', 'flag' => '🇮🇱', 'rtl' => true],
            'hi' => ['name' => 'Hindi', 'native' => 'हिन्दी', 'flag' => '🇮🇳', 'rtl' => false],
            'ko' => ['name' => 'Korean', 'native' => '한국어', 'flag' => '🇰🇷', 'rtl' => false],
            'nl' => ['name' => 'Dutch', 'native' => 'Nederlands', 'flag' => '🇳🇱', 'rtl' => false],
            'sv' => ['name' => 'Swedish', 'native' => 'Svenska', 'flag' => '🇸🇪', 'rtl' => false],
        ];
    }

    private function loadTranslationProgress()
    {
        $this->translation_progress = [];
        foreach ($this->enabled_languages as $lang) {
            $this->translation_progress[$lang] = $this->calculateTranslationProgress($lang);
        }
    }

    private function calculateTranslationProgress($language)
    {
        if ($language === 'en') {
            return 100; // English is the base language
        }

        $basePath = resource_path("lang/{$language}");
        if (!File::exists($basePath)) {
            return 0;
        }

        $baseStrings = $this->getTranslationStrings('en');
        $langStrings = $this->getTranslationStrings($language);

        if (empty($baseStrings)) {
            return 100;
        }

        $translated = count(array_intersect_key($baseStrings, $langStrings));
        return round(($translated / count($baseStrings)) * 100, 1);
    }

    private function getTranslationStrings($language)
    {
        $strings = [];
        $langPath = resource_path("lang/{$language}");
        
        if (!File::exists($langPath)) {
            return $strings;
        }

        $files = File::files($langPath);
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $content = include $file->getPathname();
                if (is_array($content)) {
                    $strings = array_merge($strings, $content);
                }
            }
        }

        return $strings;
    }

    public function toggleLanguage($language)
    {
        if (in_array($language, $this->enabled_languages)) {
            // Don't allow disabling the default language
            if ($language === $this->default_language) {
                $this->dispatch('notify', 'Cannot disable the default language.', 'error');
                return;
            }
            $this->enabled_languages = array_diff($this->enabled_languages, [$language]);
        } else {
            $this->enabled_languages[] = $language;
        }
        
        $this->enabled_languages = array_values($this->enabled_languages);
    }

    public function saveLanguageSettings()
    {
        // Validate that default and fallback languages are enabled
        if (!in_array($this->default_language, $this->enabled_languages)) {
            $this->enabled_languages[] = $this->default_language;
        }
        
        if (!in_array($this->fallback_language, $this->enabled_languages)) {
            $this->enabled_languages[] = $this->fallback_language;
        }

        $settings = [
            'enabled_languages' => $this->enabled_languages,
            'default_language' => $this->default_language,
            'fallback_language' => $this->fallback_language,
        ];

        Cache::forever('language_settings', $settings);
        
        // Clear Laravel's translation cache
        Artisan::call('cache:clear');
        
        Auth::user()->logCustomActivity('Updated language settings');
        $this->dispatch('notify', 'Language settings saved successfully!', 'success');
    }

    public function saveRegionalSettings()
    {
        $settings = cache('language_settings', []);
        $settings = array_merge($settings, [
            'default_timezone' => $this->default_timezone,
            'default_currency' => $this->default_currency,
            'date_format' => $this->date_format,
            'time_format' => $this->time_format,
            'number_format' => $this->number_format,
        ]);

        Cache::forever('language_settings', $settings);
        
        Auth::user()->logCustomActivity('Updated regional settings');
        $this->dispatch('notify', 'Regional settings saved successfully!', 'success');
    }

    public function saveRtlSettings()
    {
        $settings = cache('language_settings', []);
        $settings['enable_rtl_support'] = $this->enable_rtl_support;

        Cache::forever('language_settings', $settings);
        
        Auth::user()->logCustomActivity('Updated RTL settings');
        $this->dispatch('notify', 'RTL settings saved successfully!', 'success');
    }

    public function saveTranslationSettings()
    {
        $settings = cache('language_settings', []);
        $settings = array_merge($settings, [
            'auto_translation_enabled' => $this->auto_translation_enabled,
            'auto_translation_service' => $this->auto_translation_service,
            'translation_api_key' => $this->translation_api_key,
        ]);

        Cache::forever('language_settings', $settings);
        
        Auth::user()->logCustomActivity('Updated translation settings');
        $this->dispatch('notify', 'Translation settings saved successfully!', 'success');
    }

    public function generateLanguageFiles($language)
    {
        $basePath = resource_path("lang/{$language}");
        
        if (!File::exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        // Copy base English files
        $englishFiles = File::files(resource_path('lang/en'));
        foreach ($englishFiles as $file) {
            $targetFile = $basePath . '/' . $file->getFilename();
            if (!File::exists($targetFile)) {
                File::copy($file->getPathname(), $targetFile);
            }
        }

        $this->loadTranslationProgress();
        $this->dispatch('notify', "Language files generated for {$language}!", 'success');
    }

    public function exportTranslations($language)
    {
        $strings = $this->getTranslationStrings($language);
        $filename = "translations_{$language}_" . date('Y_m_d') . ".json";
        
        Auth::user()->logCustomActivity("Exported translations for {$language}");
        
        return response()->streamDownload(function () use ($strings) {
            echo json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function importTranslations()
    {
        $this->validate([
            'translation_file' => 'required|file|mimes:json|max:2048',
            'selected_language_for_translation' => 'required|string',
        ]);

        try {
            $content = json_decode(file_get_contents($this->translation_file->getPathname()), true);
            
            if (!$content) {
                throw new \Exception('Invalid JSON file');
            }

            // Save translations to appropriate language file
            $langPath = resource_path("lang/{$this->selected_language_for_translation}");
            if (!File::exists($langPath)) {
                File::makeDirectory($langPath, 0755, true);
            }

            $filePath = $langPath . '/messages.php';
            $existingTranslations = File::exists($filePath) ? include $filePath : [];
            $mergedTranslations = array_merge($existingTranslations, $content);

            File::put($filePath, '<?php return ' . var_export($mergedTranslations, true) . ';');

            $this->loadTranslationProgress();
            $this->translation_file = null;
            
            Auth::user()->logCustomActivity("Imported translations for {$this->selected_language_for_translation}");
            $this->dispatch('notify', 'Translations imported successfully!', 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', 'Failed to import translations: ' . $e->getMessage(), 'error');
        }
    }

    public function clearTranslationCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        
        $this->dispatch('notify', 'Translation cache cleared!', 'success');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getTimezoneOptions()
    {
        return collect(timezone_identifiers_list())
            ->mapWithKeys(fn($tz) => [$tz => $tz])
            ->toArray();
    }

    public function getCurrencyOptions()
    {
        return [
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'JPY' => 'Japanese Yen (¥)',
            'CAD' => 'Canadian Dollar (C$)',
            'AUD' => 'Australian Dollar (A$)',
            'CHF' => 'Swiss Franc (CHF)',
            'CNY' => 'Chinese Yuan (¥)',
            'NGN' => 'Nigerian Naira (₦)',
            'INR' => 'Indian Rupee (₹)',
        ];
    }

    public function getDateFormatOptions()
    {
        return [
            'Y-m-d' => 'YYYY-MM-DD (2024-01-15)',
            'm/d/Y' => 'MM/DD/YYYY (01/15/2024)',
            'd/m/Y' => 'DD/MM/YYYY (15/01/2024)',
            'F j, Y' => 'Month DD, YYYY (January 15, 2024)',
            'j F Y' => 'DD Month YYYY (15 January 2024)',
        ];
    }

    public function getTimeFormatOptions()
    {
        return [
            'H:i' => '24-hour (14:30)',
            'g:i A' => '12-hour (2:30 PM)',
            'G:i' => '24-hour no leading zero (14:30)',
        ];
    }

    public function render()
    {
        return view('livewire.system-management.language-localization');
    }
}