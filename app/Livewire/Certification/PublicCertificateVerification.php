<?php

namespace App\Livewire\Certification;

use Livewire\Component;
use App\Models\Certificate;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'My Certificates', 'description' => 'View and manage your certificates', 'icon' => 'fas fa-certificate', 'active' => 'certificates.index'])]

class PublicCertificateVerification extends Component
{
    public $uuid;
    public $verificationCode = '';
    public $certificate = null;
    public $verificationResult = null;

    public function mount($uuid = null)
    {
        $this->uuid = $uuid;
        
        if ($this->uuid) {
            $this->verifyCertificate();
        }
    }

    public function verifyCertificate()
    {
        $this->reset('certificate', 'verificationResult');
        
        $query = Certificate::with(['user', 'course', 'template', 'approver'])
            ->where('uuid', $this->uuid)
            ->orWhere('verification_code', $this->verificationCode)
            ->first();

        if ($query) {
            $this->certificate = $query;
            $this->verificationResult = $query->status === 'approved' ? 'valid' : 'invalid';
        } else {
            $this->verificationResult = 'not_found';
        }
    }

    public function render()
    {
        return view('livewire.certification.public-certificate-verification');
    }
}