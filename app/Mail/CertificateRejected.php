<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Certificate;

class CertificateRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Certificate Request Status Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.certificates.rejected',
            with: [
                'certificate' => $this->certificate,
                'url' => route('certificates.request'),
            ],
        );
    }
}