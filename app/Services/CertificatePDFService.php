<?php 
// App\Services\CertificatePDFService.php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificatePDFService
{
    public function generate(Certificate $certificate): string
    {
        $template = $certificate->certificate_template 
            ?? config('certificate.default_template', 'default');
        
        $templateConfig = config("certificate.templates.{$template}");
        
        $pdf = Pdf::loadView($templateConfig['view'], [
                'certificate' => $certificate,
                'config' => $templateConfig
            ])
            ->setPaper($templateConfig['size'] ?? 'A4', $templateConfig['orientation'] ?? 'landscape')
            ->setOptions(config('certificate.pdf.options', []));
        
        $filename = config('certificate.storage.pdf_path') . '/' . $certificate->verification_code . '.pdf';
        
        Storage::disk(config('certificate.storage.disk', 'public'))
            ->put($filename, $pdf->output());
        
        return $filename;
    }

    public function regenerate(Certificate $certificate): string
    {
        // Clean up old PDF if exists
        if ($certificate->pdf_path) {
            $disk = Storage::disk(config('certificate.storage.disk', 'public'));
            if ($disk->exists($certificate->pdf_path)) {
                $disk->delete($certificate->pdf_path);
            }
        }
        
        return $this->generate($certificate);
    }
}