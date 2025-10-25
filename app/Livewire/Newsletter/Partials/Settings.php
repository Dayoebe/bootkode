<?php
// UPDATED: Settings.php
namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use App\Models\Admin\NewsletterCampaign;

class Settings extends Component
{
    public $settings = [];

    protected $rules = [
        'settings.default_from_name' => 'required|string|max:255',
        'settings.default_from_email' => 'required|email|max:255',
        'settings.throttle_limit' => 'required|integer|min:1|max:1000',
        'settings.throttle_delay' => 'required|integer|min:1|max:300',
        'settings.unsubscribe_page_content.title' => 'required|string|max:255',
        'settings.unsubscribe_page_content.message' => 'required|string|max:1000',
        'settings.unsubscribe_page_content.resubscribe_text' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->settings = [
            'default_from_name' => NewsletterCampaign::getSetting('default_from_name', 'Bootkode Academy'),
            'default_from_email' => NewsletterCampaign::getSetting('default_from_email', 'wirelesstexter@gmail.com'),
            'throttle_limit' => NewsletterCampaign::getSetting('throttle_limit', 100),
            'throttle_delay' => NewsletterCampaign::getSetting('throttle_delay', 60),
            'unsubscribe_page_content' => NewsletterCampaign::getSetting('unsubscribe_page_content', [
                'title' => 'Unsubscribe Confirmation',
                'message' => 'You have been successfully unsubscribed from our newsletter.',
                'resubscribe_text' => 'Changed your mind? You can resubscribe anytime.'
            ]),
        ];
    }

    public function saveSettings()
    {
        $this->validate();

        foreach ($this->settings as $key => $value) {
            NewsletterCampaign::setSetting($key, $value);
        }

        session()->flash('message', 'Settings saved successfully!');
    }

    public function testEmailConfiguration()
    {
        try {
            \Mail::raw('This is a test email from your newsletter system.', function ($message) {
                $message->to($this->settings['default_from_email'])
                        ->from($this->settings['default_from_email'], $this->settings['default_from_name'])
                        ->subject('Newsletter System Test Email');
            });

            session()->flash('message', 'Test email sent successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.newsletter.partials.settings');
    }
}