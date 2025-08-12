<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\CertificateGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCertificatePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Certificate $certificate
    ) {}

    public function handle(CertificateGenerator $generator): void
    {
        $path = $generator->generatePdf($this->certificate);
        
        $this->certificate->update([
            'pdf_path' => $path,
            'issued_at' => now()
        ]);
    }
}