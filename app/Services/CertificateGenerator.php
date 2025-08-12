<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use App\Models\CertificateTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateGenerator
{
    public function generatePreview(User $user, Course $course, CertificateTemplate $template): string
    {
        $tempPath = "temp/previews/" . uniqid() . '.jpg';
        
        $html = view('certificates.templates.' . $template->view_name, [
            'user' => $user,
            'course' => $course,
            'qrCode' => QrCode::size(150)->generate(
                route('certificate.verify', ['code' => 'PREVIEW'])
            ),
            'isPreview' => true
        ])->render();

        $pdf = Pdf::loadHTML($html);
        Storage::put($tempPath, $pdf->output());

        return Storage::url($tempPath);
    }

    public function generatePdf(Certificate $certificate): string
    {
        $path = "certificates/{$certificate->id}.pdf";
        
        $html = view('certificates.templates.' . $certificate->template->view_name, [
            'user' => $certificate->user,
            'course' => $certificate->course,
            'certificate' => $certificate,
            'qrCode' => QrCode::size(150)->generate(
                $certificate->verificationUrl()
            )
        ])->render();

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape');
        
            // For bulk certificates, add a watermark
            if ($certificate->is_bulk_issued) {
                $pdf->setOption('watermark', 'BULK ISSUANCE');
                $pdf->setOption('watermarkOpacity', 0.1);
            }
        Storage::put($path, $pdf->output());
        return $path;
    }
}