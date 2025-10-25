<?php

// Mailable: NewsletterMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Admin\NewsletterCampaign;
use App\Models\Admin\NewsletterInteraction;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $campaign;
    protected $interaction;

    public function __construct(NewsletterCampaign $campaign, NewsletterInteraction $interaction)
    {
        $this->campaign = $campaign;
        $this->interaction = $interaction;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($this->campaign->from_email, $this->campaign->from_name),
            replyTo: $this->campaign->reply_to ? [new \Illuminate\Mail\Mailables\Address($this->campaign->reply_to)] : [],
            subject: $this->campaign->subject,
        );
    }


public function content(): Content
{
    $htmlContent = $this->processContent($this->campaign->html_content);
    
    // Generate URLs for the footer
    $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->interaction->subscriber->unsubscribe_token]);
    $preferencesUrl = route('newsletter.preferences', ['token' => $this->interaction->subscriber->unsubscribe_token]);
    $viewOnlineUrl = '#';

    return new Content(
        view: 'emails.newsletter',
        with: [
            'content' => $htmlContent,
            'campaign' => $this->campaign,
            'subscriber' => $this->interaction->subscriber,
            'unsubscribe_url' => $unsubscribeUrl,
            'preferences_url' => $preferencesUrl,
            'view_online_url' => $viewOnlineUrl,
        ],
    );
}

    private function processContent($content)
    {
        $trackingPixelUrl = route('newsletter.track-open', ['token' => $this->interaction->tracking_token]);
        $unsubscribeUrl = route('newsletter.unsubscribe', ['token' => $this->interaction->subscriber->unsubscribe_token]);

        // Replace tracking pixel
        $content = str_replace('{{tracking_pixel_url}}', $trackingPixelUrl, $content);

        // Replace unsubscribe URL
        $content = str_replace('{{unsubscribe_url}}', $unsubscribeUrl, $content);

        // Replace click tracking URLs
        $content = preg_replace_callback('/\{\{track_click_url\|([^}]+)\}\}/', function ($matches) {
            $encodedUrl = $matches[1];
            return route('newsletter.track-click', [
                'token' => $this->interaction->tracking_token,
                'url' => $encodedUrl
            ]);
        }, $content);

        // Replace subscriber variables
        $content = str_replace('{{subscriber_email}}', $this->interaction->subscriber->email, $content);
        $content = str_replace('{{subscriber_first_name}}', $this->interaction->subscriber->first_name, $content);
        $content = str_replace('{{subscriber_last_name}}', $this->interaction->subscriber->last_name, $content);
        $content = str_replace('{{subscriber_full_name}}', $this->interaction->subscriber->full_name, $content);

        return $content;
    }
}