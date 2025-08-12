<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Barryvdh\Dompdf\Facade\Pdf;

class CertificateController extends Controller
{
    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id() || $certificate->status !== 'approved') {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.certificate', ['certificate' => $certificate]);
        return $pdf->download("certificate-{$certificate->uuid}.pdf");
    }
}