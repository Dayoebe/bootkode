<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 0;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            border: 8px solid #d97706;
            position: relative;
            background: #ffffff;
        }

        .inner-border {
            border: 2px solid #1e3a5f;
            margin: 15px;
            padding: 30px 50px;
            height: calc(100% - 30px);
            background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%);
            position: relative;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .seal {
            width: 85px;
            height: 85px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #0c2340 0%, #1e3a5f 50%, #d97706 100%);
            border-radius: 50%;
            border: 4px solid #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 4px 12px rgba(12, 35, 64, 0.3);
        }

        .seal::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            right: 3px;
            bottom: 3px;
            border-radius: 50%;
            border: 2px solid rgba(217, 119, 6, 0.3);
        }

        .seal-icon {
            color: #ffffff;
            font-size: 38px;
            position: relative;
            z-index: 2;
        }

        .institution-name {
            font-size: 20px;
            font-weight: bold;
            color: #0c2340;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .divider {
            width: 150px;
            height: 2px;
            background: #d97706;
            margin: 0 auto 8px;
        }

        .subtitle {
            font-size: 11px;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Title */
        .certificate-title {
            text-align: center;
            margin: 25px 0 20px;
        }

        .certificate-title h1 {
            font-size: 48px;
            font-weight: bold;
            color: #0c2340;
            margin-bottom: 8px;
        }

        .title-divider {
            width: 250px;
            height: 2px;
            background: #d97706;
            margin: 0 auto 8px;
        }

        .certificate-subtitle {
            font-size: 22px;
            color: #1e3a5f;
            font-style: italic;
        }

        /* Content */
        .content {
            text-align: center;
            margin: 20px 0;
        }

        .award-text {
            font-size: 14px;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #0c2340;
            margin: 15px 0;
            border-bottom: 2px solid #d97706;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 300px;
        }

        .course-section {
            margin: 20px auto;
            padding: 20px 25px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 5px solid #d97706;
            border-radius: 8px;
            max-width: 650px;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.15);
        }

        .course-title {
            font-size: 28px;
            font-weight: bold;
            color: #0c2340;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .grade-badge {
            display: inline-block;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            color: #ffffff;
            padding: 10px 28px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 12px;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
            border: 2px solid #f59e0b;
        }

        /* Details */
        .details-row {
            display: table;
            width: 100%;
            margin: 25px 0;
        }

        .detail-item {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            vertical-align: top;
            padding: 0 10px;
        }

        .detail-label {
            font-size: 10px;
            font-weight: bold;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: bold;
            color: #0c2340;
            padding-top: 3px;
        }

        /* Signatures */
        .signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 3px solid #d97706;
        }

        .signature {
            display: table-cell;
            text-align: center;
            width: 50%;
            vertical-align: top;
            padding: 0 15px;
        }

        .signature-line {
            width: 200px;
            border-bottom: 2px solid #0c2340;
            margin: 0 auto 10px;
            height: 45px;
        }

        .signature-name {
            font-weight: bold;
            color: #0c2340;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .signature-title {
            font-size: 10px;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        /* Verification */
        .verification-section {
            background: #fef3c7;
            border: 2px solid #d97706;
            border-radius: 8px;
            padding: 18px;
            margin-top: 20px;
            display: table;
            width: 100%;
        }

        .verification-info {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
            padding-right: 20px;
        }

        .verification-header {
            font-size: 11px;
            font-weight: bold;
            color: #0c2340;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 12px;
        }

        .verification-header-icon {
            margin-right: 8px;
            color: #d97706;
        }

        .verification-item {
            background: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 8px;
            font-size: 9px;
        }

        .verification-label {
            font-weight: bold;
            color: #d97706;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 3px;
        }

        .verification-value {
            color: #0c2340;
            font-weight: 600;
            word-break: break-all;
            line-height: 1.3;
        }

        .qr-code-container {
            display: table-cell;
            width: 30%;
            text-align: center;
            vertical-align: middle;
        }

        .qr-section-title {
            font-size: 9px;
            font-weight: bold;
            color: #0c2340;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            border: 3px solid #d97706;
            background: #ffffff;
            padding: 4px;
            border-radius: 4px;
            display: inline-block;
        }

        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 3px solid #d97706;
            background: #ffffff;
            display: inline-block;
            text-align: center;
            padding: 20px 10px;
            border-radius: 4px;
            font-size: 8px;
            color: #6b7280;
            line-height: 1.4;
        }

        .qr-helper-text {
            font-size: 7px;
            color: #6b7280;
            margin-top: 6px;
            line-height: 1.3;
            max-width: 110px;
            display: inline-block;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 15px;
        }

        .footer-divider {
            display: inline-block;
            width: 40px;
            height: 1px;
            background: #d97706;
            margin: 0 10px;
            vertical-align: middle;
        }

        .footer-icon {
            color: #d97706;
            font-size: 10px;
            vertical-align: middle;
        }

        .footer-text {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 120px;
            font-weight: bold;
            color: rgba(217, 119, 6, 0.03);
            z-index: 0;
            pointer-events: none;
        }

        /* Corner ornaments */
        .corner {
            position: absolute;
            width: 50px;
            height: 50px;
            border-color: #d97706;
            opacity: 0.4;
        }

        .corner-tl {
            top: 20px;
            left: 20px;
            border-left: 3px solid #d97706;
            border-top: 3px solid #d97706;
        }

        .corner-tr {
            top: 20px;
            right: 20px;
            border-right: 3px solid #d97706;
            border-top: 3px solid #d97706;
        }

        .corner-bl {
            bottom: 20px;
            left: 20px;
            border-left: 3px solid #d97706;
            border-bottom: 3px solid #d97706;
        }

        .corner-br {
            bottom: 20px;
            right: 20px;
            border-right: 3px solid #d97706;
            border-bottom: 3px solid #d97706;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="inner-border">
            <!-- Watermark -->
            <div class="watermark">VERIFIED</div>

            <!-- Corner Ornaments -->
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>

            <!-- Header -->
            <div class="header">
                <div class="seal">
                    <span class="seal-icon">🎓</span>
                </div>
                <div class="institution-name">Bootkode Academy</div>
                <div class="divider"></div>
                <div class="subtitle">Learning Platform</div>
            </div>

            <!-- Title -->
            <div class="certificate-title">
                <h1>Certificate</h1>
                <div class="title-divider"></div>
                <div class="certificate-subtitle">of Achievement</div>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="award-text">This is to certify that</div>

                <div class="recipient-name">{{ $certificate->user->name }}</div>

                <div class="award-text">has successfully completed the requirements and demonstrated exceptional
                    proficiency in</div>

                <div class="course-section">
                    <div class="course-title">{{ $certificate->course->title }}</div>
                    @if($certificate->course->subtitle ?? false)
                        <p class="course-subtitle">{{ $certificate->course->subtitle }}</p>
                    @endif
                    @if($certificate->grade)
                        <div class="grade-badge">Grade: {{ $certificate->grade }}</div>
                    @endif
                </div>
            </div>

            <!-- Details -->
            <div class="details-row">
                <div class="detail-item">
                    <div class="detail-label">Completion Date</div>
                    <div class="detail-value">{{ $certificate->completion_date->format('F j, Y') }}</div>
                </div>
                @if($certificate->credits)
                    <div class="detail-item">
                        <div class="detail-label">Credits Earned</div>
                        <div class="detail-value">{{ $certificate->credits }}</div>
                    </div>
                @endif
                <div class="detail-item">
                    <div class="detail-label">Issue Date</div>
                    <div class="detail-value">{{ $certificate->issued_date->format('F j, Y') }}</div>
                </div>
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->course->instructor->name }}</div>
                    <div class="signature-title">Course Instructor</div>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->approver->name ?? 'Academic Director' }}</div>
                    <div class="signature-title">Academic Director</div>
                </div>
            </div>

            <!-- Verification -->
            <div class="verification-section">
                <div class="verification-info">
                    <div class="verification-header">
                        <span class="verification-header-icon">🛡️</span>
                        Verification Information
                    </div>
                    <div class="verification-item">
                        <div class="verification-label">Certificate ID:</div>
                        <div class="verification-value">{{ $certificate->certificate_number }}</div>
                    </div>
                    <div class="verification-item">
                        <div class="verification-label">Verification Code:</div>
                        <div class="verification-value">{{ $certificate->verification_code }}</div>
                    </div>
                    <div class="verification-item">
                        <div class="verification-label">Verify at:</div>
                        <div class="verification-value">{{ $certificate->verification_url }}</div>
                    </div>
                </div>
                <div class="qr-code-container">
                    <div class="qr-section-title">Instant Verification</div>
                    @php
                        $qrBase64 = null;
                        if ($certificate->qr_code_path) {
                            $qrPath = storage_path('app/public/' . $certificate->qr_code_path);
                            if (file_exists($qrPath) && is_file($qrPath)) {
                                try {
                                    $qrBase64 = base64_encode(file_get_contents($qrPath));
                                } catch (\Exception $e) {
                                    $qrBase64 = null;
                                }
                            }
                        }
                    @endphp

                    @if($qrBase64)
                        <img src="data:image/png;base64,{{ $qrBase64 }}" class="qr-code" alt="QR Code">
                    @else
                        <div class="qr-placeholder">
                            <div style="font-size: 24px; margin-bottom: 4px;">📱</div>
                            <div>SCAN TO<br>VERIFY</div>
                        </div>
                    @endif
                    <div class="qr-helper-text">Scan QR code to instantly verify this certificate</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <span class="footer-divider"></span>
                <span class="footer-icon">★</span>
                <span class="footer-divider"></span>
                <div class="footer-text">Authenticated & Verified</div>
            </div>
        </div>
    </div>
</body>

</html>