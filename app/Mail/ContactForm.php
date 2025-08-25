<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactForm extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subject;
    public $userMessage; // Renamed to avoid conflict with reserved $message

    /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $email
     * @param string $subject
     * @param string $userMessage
     */
    public function __construct($name, $email, $subject, $userMessage)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->userMessage = $userMessage; // Renamed here
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.contact-form')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'userMessage' => $this->userMessage, // Pass renamed variable
            ]);
    }
}