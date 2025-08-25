<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use App\Mail\ContactForm;

#[Layout('layouts.app', ['title' => 'Contact Us', 'description' => "Get in touch with BootKode to empower Africa's youth with digital skills, mentorship & careers.", 'developer' => 'Bootkode', 'developer_url' => 'https://bootkode.com'])]
class ContactUs extends Component
{
    public $name = '';
    public $email = '';
    public $subject = '';
    public $message = '';

    protected $rules = [
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|email',
        'subject' => 'required|string|min:5|max:255',
        'message' => 'required|string|min:10',
    ];

    public function submit()
    {
        $this->validate();

        // Send email using Laravel Mail (queue for performance)
        Mail::to('oyetoke.ebenezer@gmail.com') // Real email from PDF
            ->queue(new ContactForm($this->name, $this->email, $this->subject, $this->message));

        // Reset form and show success
        $this->reset(['name', 'email', 'subject', 'message']);
        session()->flash('success', 'Your message has been sent successfully!');
    }

    public function render()
    {
        return view('livewire.pages.contact-us');
    }
}