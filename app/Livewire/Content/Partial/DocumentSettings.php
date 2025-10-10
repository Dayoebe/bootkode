<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;

class DocumentSettings extends Component
{
    public $settings = [
        'auto_publish' => false,
        'require_review' => true,
        'allow_comments' => true,
        'enable_versioning' => true,
        'max_file_size' => 50,
        'allowed_file_types' => 'pdf,doc,docx,txt,md',
        'default_visibility' => 'public',
        'enable_notifications' => true,
        'backup_frequency' => 'daily',
        'retention_period' => 365,
    ];

    public $generalSettings = [];
    public $securitySettings = [];
    public $notificationSettings = [];
    public $backupSettings = [];

    public function mount()
    {
        $this->loadSettings();
    }


    private function loadSettings()
    {
        // In a real app, you'd load these from a settings table or config
        $this->generalSettings = [
            'auto_publish' => $this->settings['auto_publish'],
            'require_review' => $this->settings['require_review'],
            'allow_comments' => $this->settings['allow_comments'],
            'enable_versioning' => $this->settings['enable_versioning'],
            'default_visibility' => $this->settings['default_visibility'],
        ];

        $this->securitySettings = [
            'max_file_size' => $this->settings['max_file_size'],
            'allowed_file_types' => $this->settings['allowed_file_types'],
        ];

        $this->notificationSettings = [
            'enable_notifications' => $this->settings['enable_notifications'],
        ];

        $this->backupSettings = [
            'backup_frequency' => $this->settings['backup_frequency'],
            'retention_period' => $this->settings['retention_period'],
        ];
    }

    public function saveGeneralSettings()
    {
        try {
            // In a real app, save to database or config
            // Update settings array
            $this->settings['auto_publish'] = $this->generalSettings['auto_publish'];
            $this->settings['require_review'] = $this->generalSettings['require_review'];
            $this->settings['allow_comments'] = $this->generalSettings['allow_comments'];
            $this->settings['enable_versioning'] = $this->generalSettings['enable_versioning'];
            $this->settings['default_visibility'] = $this->generalSettings['default_visibility'];
            
            session()->flash('message', 'General settings updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update notification settings: ' . $e->getMessage());
        }
    }

    public function saveBackupSettings()
    {
        try {
            // Validate retention period (30-3650 days = ~10 years max)
            if ($this->backupSettings['retention_period'] < 30 || $this->backupSettings['retention_period'] > 3650) {
                session()->flash('error', 'Retention period must be between 30 and 3650 days');
                return;
            }

            $this->settings['backup_frequency'] = $this->backupSettings['backup_frequency'];
            $this->settings['retention_period'] = $this->backupSettings['retention_period'];

            session()->flash('message', 'Backup settings updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update backup settings: ' . $e->getMessage());
        }
    }

    public function triggerManualBackup()
    {
        try {
            // In a real app, trigger a backup job here
            // dispatch(new BackupDocumentsJob());
            
            session()->flash('message', 'Manual backup initiated successfully! You will be notified when complete.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to trigger backup: ' . $e->getMessage());
        }
    }

    public function resetToDefaults()
    {
        try {
            $this->settings = [
                'auto_publish' => false,
                'require_review' => true,
                'allow_comments' => true,
                'enable_versioning' => true,
                'max_file_size' => 50,
                'allowed_file_types' => 'pdf,doc,docx,txt,md',
                'default_visibility' => 'public',
                'enable_notifications' => true,
                'backup_frequency' => 'daily',
                'retention_period' => 365,
            ];

            $this->loadSettings();
            session()->flash('message', 'All settings reset to defaults successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reset settings: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $visibilityOptions = [
            'public' => 'Public - Visible to everyone',
            'private' => 'Private - Only visible to creator',
            'restricted' => 'Restricted - Limited access',
            'internal' => 'Internal - Staff only',
        ];

        $backupFrequencyOptions = [
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];

        return view('livewire.content.partial.document-settings', compact(
            'visibilityOptions', 
            'backupFrequencyOptions'
        ));
    }

    public function saveSecuritySettings()
    {
        try {
            // Validate file types
            $fileTypes = array_map('trim', explode(',', $this->securitySettings['allowed_file_types']));
            $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'md', 'xls', 'xlsx', 'ppt', 'pptx'];
            
            foreach ($fileTypes as $type) {
                if (!in_array(strtolower($type), $allowedTypes)) {
                    session()->flash('error', 'Invalid file type: ' . $type);
                    return;
                }
            }

            // Validate file size (1-100 MB)
            if ($this->securitySettings['max_file_size'] < 1 || $this->securitySettings['max_file_size'] > 100) {
                session()->flash('error', 'File size must be between 1 and 100 MB');
                return;
            }

            // Update settings
            $this->settings['max_file_size'] = $this->securitySettings['max_file_size'];
            $this->settings['allowed_file_types'] = $this->securitySettings['allowed_file_types'];

            session()->flash('message', 'Security settings updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update security settings: ' . $e->getMessage());
        }
    }

    public function saveNotificationSettings()
    {
        try {
            session()->flash('message', 'Notification settings updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update notification settings: ' . $e->getMessage());
        }
    }
}