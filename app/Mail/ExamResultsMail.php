<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ExamResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $results;

    /**
     * Create a new message instance.
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $passed = $this->results['results']['passed'];
        $score = $this->results['results']['percentage'];
        
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $passed 
                ? "🎉 Congratulations! You Passed - {$this->results['assessment']['title']}" 
                : "📊 Your Exam Results - {$this->results['assessment']['title']}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-results',
            text: 'emails.exam-results-plain',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}