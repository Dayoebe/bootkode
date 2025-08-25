<?php 
// App\Services\QRCodeService.php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class QRCodeService
{
    public function generate(Certificate $certificate): string
    {
        $qrCode = QrCode::create($certificate->verification_url)
            ->setSize(config('certificate.qr_code.size', 200))
            ->setMargin(config('certificate.qr_code.margin', 10))
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        $filename = config('certificate.storage.qr_path') . '/' . $certificate->verification_code . '.png';
        
        Storage::disk(config('certificate.storage.disk', 'public'))
            ->put($filename, $result->getString());
        
        return $filename;
    }
}
