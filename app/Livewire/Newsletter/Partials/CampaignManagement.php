<?php

// UPDATED: CampaignManagement.php
namespace App\Livewire\Newsletter\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterInteraction;
use App\Jobs\SendNewsletterCampaign;

class CampaignManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Create/Edit Campaign
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;
    
    public $name = '';
    public $subject = '';
    public $previewText = '';
    public $htmlContent = '';
    public $fromName = '';
    public $fromEmail = '';
    public $replyTo = '';
    public $templateId = null;
    public $recipientFilters = [];
    public $scheduledAt = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'previewText' => 'nullable|string|max:500',
        'htmlContent' => 'required|string',
        'fromName' => 'required|string|max:255',
        'fromEmail' => 'required|email',
        'replyTo' => 'nullable|email',
        'scheduledAt' => 'nullable|date|after:now',
    ];

    public function mount()
    {
        $this->fromName = NewsletterCampaign::getSetting('default_from_name', 'Bootkode Academy');
        $this->fromEmail = NewsletterCampaign::getSetting('default_from_email', 'wirelesstexter@gmail.com');
    }

    public function getCampaignsProperty()
    {
        $query = NewsletterCampaign::campaigns()->with('creator');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function getTemplatesProperty()
    {
        return NewsletterCampaign::templates()->orderBy('name')->get();
    }

    public function createCampaign()
    {
        $this->validate();

        $totalRecipients = $this->calculateTotalRecipients();

        $campaign = NewsletterCampaign::create([
            'name' => $this->name,
            'subject' => $this->subject,
            'preview_text' => $this->previewText,
            'html_content' => $this->processHtmlContent($this->htmlContent),
            'from_name' => $this->fromName,
            'from_email' => $this->fromEmail,
            'reply_to' => $this->replyTo,
            'type' => NewsletterCampaign::TYPE_CAMPAIGN,
            'recipient_filters' => $this->recipientFilters,
            'total_recipients' => $totalRecipients,
            'scheduled_at' => $this->scheduledAt ? now()->parse($this->scheduledAt) : null,
            'status' => $this->scheduledAt ? NewsletterCampaign::STATUS_SCHEDULED : NewsletterCampaign::STATUS_DRAFT,
            'created_by' => auth()->id(),
        ]);

        $this->resetForm();
        session()->flash('message', 'Campaign created successfully!');
    }

    public function editCampaign($id)
    {
        $campaign = NewsletterCampaign::campaigns()->findOrFail($id);

        if (!$campaign->canBeSent()) {
            session()->flash('error', 'This campaign cannot be edited.');
            return;
        }

        $this->editingId = $id;
        $this->name = $campaign->name;
        $this->subject = $campaign->subject;
        $this->previewText = $campaign->preview_text;
        $this->htmlContent = $campaign->html_content;
        $this->fromName = $campaign->from_name;
        $this->fromEmail = $campaign->from_email;
        $this->replyTo = $campaign->reply_to;
        $this->recipientFilters = $campaign->recipient_filters ?? [];
        $this->scheduledAt = $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '';
        $this->showEditModal = true;
    }

    public function updateCampaign()
    {
        $this->validate();

        $campaign = NewsletterCampaign::campaigns()->findOrFail($this->editingId);
        $totalRecipients = $this->calculateTotalRecipients();

        $campaign->update([
            'name' => $this->name,
            'subject' => $this->subject,
            'preview_text' => $this->previewText,
            'html_content' => $this->processHtmlContent($this->htmlContent),
            'from_name' => $this->fromName,
            'from_email' => $this->fromEmail,
            'reply_to' => $this->replyTo,
            'recipient_filters' => $this->recipientFilters,
            'total_recipients' => $totalRecipients,
            'scheduled_at' => $this->scheduledAt ? now()->parse($this->scheduledAt) : null,
            'status' => $this->scheduledAt ? NewsletterCampaign::STATUS_SCHEDULED : NewsletterCampaign::STATUS_DRAFT,
        ]);

        $this->resetForm();
        session()->flash('message', 'Campaign updated successfully!');
    }

    public function sendCampaign($id)
    {
        $campaign = NewsletterCampaign::campaigns()->findOrFail($id);

        if (!$campaign->canBeSent()) {
            session()->flash('error', 'This campaign cannot be sent.');
            return;
        }

        SendNewsletterCampaign::dispatch($campaign);
        session()->flash('message', 'Campaign is being sent! You can track progress in the analytics tab.');
    }

    public function cancelCampaign($id)
    {
        $campaign = NewsletterCampaign::campaigns()->findOrFail($id);
        
        // Check if campaign can be cancelled
        if (!in_array($campaign->status, [NewsletterCampaign::STATUS_SCHEDULED, NewsletterCampaign::STATUS_SENDING])) {
            session()->flash('error', 'Only scheduled or sending campaigns can be cancelled.');
            return;
        }
        
        // Update campaign status to cancelled
        $campaign->update(['status' => NewsletterCampaign::STATUS_CANCELLED]);
        
        session()->flash('message', 'Campaign cancelled successfully.');
    }

    public function duplicateCampaign($id)
    {
        $original = NewsletterCampaign::campaigns()->findOrFail($id);

        $duplicate = $original->replicate();
        $duplicate->name = $original->name . ' (Copy)';
        $duplicate->status = NewsletterCampaign::STATUS_DRAFT;
        $duplicate->scheduled_at = null;
        $duplicate->sent_at = null;
        $duplicate->sent_count = 0;
        $duplicate->open_count = 0;
        $duplicate->click_count = 0;
        $duplicate->bounce_count = 0;
        $duplicate->unsubscribe_count = 0;
        $duplicate->created_by = auth()->id();
        $duplicate->save();

        session()->flash('message', 'Campaign duplicated successfully!');
    }

    public function deleteCampaign($id)
    {
        $campaign = NewsletterCampaign::campaigns()->findOrFail($id);

        if (in_array($campaign->status, [NewsletterCampaign::STATUS_SENDING, NewsletterCampaign::STATUS_SENT], true)) {
            session()->flash('error', 'Cannot delete a campaign that has been sent or is being sent.');
            return;
        }

        $campaign->delete();
        session()->flash('message', 'Campaign deleted successfully!');
    }

    public function loadTemplate($templateId)
    {
        if (!$templateId) {
            $this->htmlContent = '';
            return;
        }

        $template = NewsletterCampaign::templates()->find($templateId);
        if ($template) {
            $this->htmlContent = $template->html_content;
        }
    }

    private function calculateTotalRecipients()
    {
        $query = NewsletterSubscriber::active();

        if (!empty($this->recipientFilters)) {
            foreach ($this->recipientFilters as $filter) {
                if ($filter['type'] === 'tag') {
                    $query->whereJsonContains('tags', $filter['value']);
                }
            }
        }

        return $query->count();
    }

    private function processHtmlContent($content)
    {
        // Add tracking pixel
        $trackingPixel = '<img src="{{tracking_pixel_url}}" width="1" height="1" style="display:none;" />';
        
        // Add tracking to links
        $content = preg_replace_callback('/href="([^"]+)"/', function ($matches) {
            $url = $matches[1];
            if (strpos($url, 'http') === 0) {
                return 'href="{{track_click_url|' . base64_encode($url) . '}}"';
            }
            return $matches[0];
        }, $content);

        // // Add unsubscribe link if not present
        // if (strpos($content, '{{unsubscribe_url}}') === false) {
        //     $content .= '<br><small><a href="{{unsubscribe_url}}">Unsubscribe from this list</a></small>';
        // }

        return $content . $trackingPixel;
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'subject', 'previewText', 'htmlContent', 'templateId', 
            'recipientFilters', 'scheduledAt', 'showCreateModal', 'showEditModal', 'editingId'
        ]);
    }

    public function render()
    {
        return view('livewire.newsletter.partials.campaign-management', [
            'campaigns' => $this->campaigns,
            'templates' => $this->templates,
        ]);
    }
}