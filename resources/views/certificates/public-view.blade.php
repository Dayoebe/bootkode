<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.4;
            color: #1a1a1a;
            background: #ffffff;
            font-size: 14pt;
        }

        @media screen {
            body {
                background: linear-gradient(135deg, #f5f3ef 0%, #e8e6e1 50%, #d4cfca 100%);
                padding: 2rem;
                min-height: 100vh;
            }
            .certificate-wrapper {
                box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
                border-radius: 4px;
                overflow: hidden;
            }
            .action-buttons {
                max-width: 297mm;
                margin: 0 auto 2rem;
                text-align: right;
            }
            .btn {
                display: inline-block;
                padding: 12px 28px;
                margin-left: 12px;
                border: none;
                border-radius: 6px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                color: white;
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            }
            .btn-print {
                background: linear-gradient(135deg, #1a1a1a 0%, #3a3a3a 100%);
            }
            .btn-print:hover {
                background: linear-gradient(135deg, #2a2a2a 0%, #4a4a4a 100%);
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            }
            .btn-download {
                background: linear-gradient(135deg, #b8860b 0%, #daa520 100%);
            }
            .btn-download:hover {
                background: linear-gradient(135deg, #daa520 0%, #ffd700 100%);
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(218, 165, 32, 0.4);
            }
        }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .action-buttons, .no-print {
                display: none !important;
            }
        }

        .certificate-wrapper {
            max-width: 297mm;
            margin: 0 auto;
            background: white;
        }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(to bottom, #fdfdfb 0%, #f9f8f5 100%);
            border: 12px solid #b8860b;
            box-shadow: inset 0 0 0 2px #daa520;
            overflow: hidden;
        }

        /* Background Pattern */
        .certificate-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(184, 134, 11, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(218, 165, 32, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 215, 0, 0.08) 0%, transparent 50%),
                repeating-linear-gradient(45deg, transparent, transparent 20px, rgba(184, 134, 11, 0.06) 20px, rgba(184, 134, 11, 0.06) 22px),
                repeating-linear-gradient(-45deg, transparent, transparent 20px, rgba(218, 165, 32, 0.06) 20px, rgba(218, 165, 32, 0.06) 22px),
                repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(184, 134, 11, 0.03) 40px, rgba(184, 134, 11, 0.03) 42px),
                repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(218, 165, 32, 0.03) 40px, rgba(218, 165, 32, 0.03) 42px);
            z-index: 0;
            pointer-events: none;
        }

        .inner-border {
            border: 3px double #8b7355;
            margin: 8px;
            padding: 12px 30px 10px 30px;
            height: calc(100% - 16px);
            background: linear-gradient(to bottom, #ffffff 0%, #fefdfb 50%, #faf9f6 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            z-index: 1;
        }

        .gold-accent {
            color: #b8860b;
        }

        .shimmer {
            background: linear-gradient(120deg, #b8860b 0%, #daa520 50%, #ffd700 75%, #daa520 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 500px;
            margin-left: -250px;
            margin-top: -100px;
            font-size: 120pt;
            font-weight: 300;
            color: rgba(184, 134, 11, 0.02);
            text-align: center;
            z-index: 0;
            transform: rotate(-25deg);
            font-family: 'Playfair Display', serif;
            letter-spacing: 10pt;
        }

        .corner-ornament {
            position: absolute;
            width: 60px;
            height: 60px;
            border-style: solid;
            border-color: #b8860b;
            opacity: 0.4;
        }

        .corner-tl {
            top: 12px;
            left: 12px;
            border-width: 4px 0 0 4px;
            border-radius: 0 0 8px 0;
        }

        .corner-tr {
            top: 12px;
            right: 12px;
            border-width: 4px 4px 0 0;
            border-radius: 0 0 0 8px;
        }

        .corner-bl {
            bottom: 12px;
            left: 12px;
            border-width: 0 0 4px 4px;
            border-radius: 0 8px 0 0;
        }

        .corner-br {
            bottom: 12px;
            right: 12px;
            border-width: 0 4px 4px 0;
            border-radius: 8px 0 0 0;
        }

        /* Ribbon Badge - Replacing authenticity-badge */
        .ribbon-badge {
            position: absolute;
            top: -8px;
            right: 40px;
            z-index: 10;
        }

        .ribbon-wrapper {
            width: 80px;
            height: 100px;
            position: relative;
        }

        .ribbon-front {
            background: linear-gradient(135deg, #b8860b 0%, #daa520 50%, #b8860b 100%);
            height: 70px;
            width: 80px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-shadow: 0 4px 12px rgba(184, 134, 11, 0.4);
        }

        .ribbon-front::before {
            content: '';
            position: absolute;
            height: 0;
            width: 0;
            top: 0;
            left: -10px;
            border-top: 35px solid #8b6914;
            border-bottom: 35px solid #8b6914;
            border-left: 10px solid transparent;
        }

        .ribbon-front::after {
            content: '';
            position: absolute;
            height: 0;
            width: 0;
            top: 0;
            right: -10px;
            border-top: 35px solid #8b6914;
            border-bottom: 35px solid #8b6914;
            border-right: 10px solid transparent;
        }

        .ribbon-edge-topleft,
        .ribbon-edge-topright,
        .ribbon-edge-bottomleft,
        .ribbon-edge-bottomright {
            position: absolute;
            z-index: -1;
            border-style: solid;
            height: 0;
            width: 0;
        }

        .ribbon-edge-topleft {
            top: 0;
            left: 0;
            border-color: #6b5010 transparent transparent #6b5010;
            border-width: 5px;
        }

        .ribbon-edge-topright {
            top: 0;
            right: 0;
            border-color: #6b5010 #6b5010 transparent transparent;
            border-width: 5px;
        }

        .ribbon-edge-bottomleft {
            bottom: 30px;
            left: 0;
            border-color: transparent transparent #6b5010 #6b5010;
            border-width: 5px;
        }

        .ribbon-edge-bottomright {
            bottom: 30px;
            right: 0;
            border-color: transparent #6b5010 #6b5010 transparent;
            border-width: 5px;
        }

        .ribbon-icon {
            font-size: 28pt;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .ribbon-left,
        .ribbon-right {
            position: absolute;
            height: 40px;
            width: 30px;
            top: 70px;
        }

        .ribbon-left {
            left: 10px;
            background: linear-gradient(to bottom, #b8860b 0%, #8b6914 100%);
            clip-path: polygon(0 0, 100% 0, 50% 100%);
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .ribbon-right {
            right: 10px;
            background: linear-gradient(to bottom, #b8860b 0%, #8b6914 100%);
            clip-path: polygon(0 0, 100% 0, 50% 100%);
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .seal {
            width: 60px;
            height: 60px;
            margin: 0 auto 6px;
            background: linear-gradient(135deg, #b8860b 0%, #daa520 50%, #b8860b 100%);
            border-radius: 50%;
            border: 3px solid #8b7355;
            box-shadow: 0 2px 8px rgba(184, 134, 11, 0.3), inset 0 1px 3px rgba(255, 255, 255, 0.3);
            text-align: center;
            line-height: 60px;
            font-size: 30pt;
            color: #ffffff;
        }

        .institution-name {
            font-size: 16pt;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 4pt;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-family: 'Montserrat', sans-serif;
        }

        .gold-divider {
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, transparent, #b8860b, #daa520, #b8860b, transparent);
            margin: 0 auto 3px;
            box-shadow: 0 1px 2px rgba(184, 134, 11, 0.3);
        }

        .subtitle {
            font-size: 9pt;
            color: #5a5a5a;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 2pt;
            font-family: 'Montserrat', sans-serif;
        }

        .certificate-title {
            text-align: center;
            margin: 4px 0 3px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .certificate-title h1 {
            font-size: 44pt;
            font-weight: 300;
            background: linear-gradient(135deg, #b8860b 0%, #daa520 40%, #ffd700 50%, #daa520 60%, #b8860b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 3px;
            font-family: 'Playfair Display', 'Garamond', serif;
            letter-spacing: 6pt;
            text-shadow: 0 2px 4px rgba(184, 134, 11, 0.1);
        }

        .title-divider {
            width: 180px;
            height: 2px;
            background: linear-gradient(to right, transparent, #b8860b 20%, #daa520 50%, #b8860b 80%, transparent);
            margin: 0 auto 3px;
        }

        .certificate-subtitle {
            font-size: 14pt;
            color: #5a5a5a;
            font-style: italic;
            font-family: 'Playfair Display', serif;
            font-weight: 400;
            letter-spacing: 1.5pt;
        }

        .content {
            text-align: center;
            margin: 4px 0;
            position: relative;
            z-index: 1;
            flex-shrink: 1;
            flex-grow: 0;
        }

        .award-text {
            font-size: 10pt;
            color: #3a3a3a;
            margin-bottom: 5px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 400;
        }

        .recipient-name {
            font-size: 32pt;
            font-weight: 400;
            background: linear-gradient(135deg, #8b7355 0%, #b8860b 30%, #daa520 50%, #b8860b 70%, #8b7355 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 6px 0;
            border-bottom: 2px solid #b8860b;
            display: inline-block;
            padding: 0 20px 3px 20px;
            font-family: 'Great Vibes', cursive;
            letter-spacing: 1pt;
        }

        .course-section {
            margin: 6px auto;
            padding: 10px 18px;
            background: linear-gradient(135deg, #fefdfb 0%, #faf9f6 100%);
            border-left: 4px solid #b8860b;
            border-right: 1px solid #daa520;
            max-width: 600px;
            box-shadow: 0 2px 8px rgba(184, 134, 11, 0.1);
        }

        .course-title {
            font-size: 20pt;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 4px;
            font-family: 'Playfair Display', serif;
            line-height: 1.3;
            word-wrap: break-word;
        }

        .course-title.long {
            font-size: 18pt;
        }

        .course-title.very-long {
            font-size: 16pt;
        }

        .course-subtitle {
            font-size: 10pt;
            font-weight: 400;
            color: #5a5a5a;
            margin-bottom: 4px;
            font-family: 'Montserrat', sans-serif;
            line-height: 1.3;
            word-wrap: break-word;
            font-style: italic;
        }

        .grade-badge {
            display: inline-block;
            background: linear-gradient(135deg, #b8860b 0%, #daa520 100%);
            color: #ffffff;
            padding: 6px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11pt;
            margin-top: 5px;
            border: 2px solid #ffd700;
            font-family: 'Montserrat', sans-serif;
            box-shadow: 0 2px 6px rgba(184, 134, 11, 0.3);
            letter-spacing: 0.5pt;
        }

        .details-signatures-wrapper {
            margin: 5px 0 4px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .details-row {
            text-align: center;
            margin-bottom: 5px;
            font-family: 'Montserrat', sans-serif;
        }

        .detail-inline {
            display: inline-block;
            margin: 0 12px;
            font-size: 8.5pt;
        }

        .detail-label {
            font-weight: 600;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }

        .detail-value {
            font-weight: 600;
            color: #1a1a1a;
        }

        .signatures {
            width: 100%;
            margin-top: 5px;
            padding-top: 6px;
            border-top: 2px solid #b8860b;
        }

        .signatures table {
            width: 100%;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-name {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 11pt;
            margin-bottom: 2px;
            font-family: 'Montserrat', sans-serif;
        }

        .signature-title {
            font-size: 8pt;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 1pt;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
        }

        .verification-section {
            background: linear-gradient(135deg, #fefdfb 0%, #faf9f6 100%);
            border: 2px solid #b8860b;
            border-radius: 4px;
            border-bottom: none;
            padding: 5px 8px;
            margin-top: auto;
            max-height: 100px; 
            overflow: hidden; 
            position: relative;
            z-index: 1;
            flex-shrink: 0;
            box-shadow: inset 0 1px 3px rgba(184, 134, 11, 0.1);
        }

        .verification-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .verification-section td {
            vertical-align: top;
        }

        .verification-info {
            width: 68%;
            padding-right: 8px;
        }

        .verification-header {
            font-size: 11pt;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1pt;
            margin-bottom: 3px;
            font-family: 'Montserrat', sans-serif;
        }

        .verification-item {
            background: rgba(255, 255, 255, 0.7);
            padding: 2px 5px;
            margin-bottom: 2px;
            font-size: 9pt;
            font-family: 'Montserrat', sans-serif;
            line-height: 1.3;
            border-left: 2px solid #daa520;
            max-height: 100px;
            overflow: hidden;
        }

        .verification-label {
            font-weight: 600;
            color: #b8860b;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            display: inline;
        }

        .verification-value {
            color: #1a1a1a;
            font-weight: 500;
            word-wrap: break-word;
        }

        .qr-code-container {
            width: 32%;
            text-align: center;
            padding-bottom: 8px;
        }

        .qr-section-title {
            font-size: 7.5pt;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-bottom: 4px;
            font-family: 'Montserrat', sans-serif;
        }

        .qr-code {
            width: 75px;
            height: 75px;
            border: 3px solid #b8860b;
            background: #ffffff;
            padding: 2px;
            box-shadow: 0 2px 6px rgba(184, 134, 11, 0.2);
        }

        .qr-placeholder {
            width: 75px;
            height: 75px;
            border: 3px solid #b8860b;
            background: #ffffff;
            text-align: center;
            padding: 12px 4px;
            font-size: 6.5pt;
            color: #8b7355;
            line-height: 1.2;
            font-family: 'Montserrat', sans-serif;
            box-shadow: 0 2px 6px rgba(184, 134, 11, 0.2);
        }

        .footer {
            text-align: center;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .footer-ornament {
            display: inline-block;
            width: 30px;
            height: 5px;
            background: linear-gradient(to right, transparent, #b8860b, transparent);
            margin: 0 8px;
            vertical-align: middle;
        }

        .footer-text {
            font-size: 10pt;
            color: #8b7355;
            text-transform: uppercase;
            letter-spacing: 1pt;
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
        }

        .gold-border-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(to right, 
                transparent 0%, 
                #b8860b 10%, 
                #daa520 30%, 
                #ffd700 50%, 
                #daa520 70%, 
                #b8860b 90%, 
                transparent 100%);
            opacity: 0.3;
        }

        .embossed-seal {
            position: absolute;
            top: 45px;
            left: 28px;
            width: 50px;
            height: 50px;
            background: radial-gradient(circle, rgba(184, 134, 11, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 0;
        }
    </style>

    @if(!isset($isPdf) || !$isPdf)
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseTitle = document.querySelector('.course-title');
            if (courseTitle) {
                const titleLength = courseTitle.textContent.length;
                if (titleLength > 60) {
                    courseTitle.classList.add('very-long');
                } else if (titleLength > 40) {
                    courseTitle.classList.add('long');
                }
            }
        });
    </script>
    @endif
</head>
<body>
    
    @if(!isset($isPdf) || !$isPdf)
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> Print Certificate
        </button>
        <a href="{{ route('certificate.download', $certificate->verification_code) }}" class="btn btn-download">
            <i class="fas fa-download"></i> Download PDF
        </a>
    </div>
    @endif

    <div class="certificate-wrapper">
        <div class="certificate-container">
            <div class="inner-border">
                
                <div class="gold-border-accent"></div>
                <div class="embossed-seal"></div>
                
                <div class="watermark">CERTIFIED</div>

                <div class="corner-ornament corner-tl"></div>
                <div class="corner-ornament corner-tr"></div>
                <div class="corner-ornament corner-bl"></div>
                <div class="corner-ornament corner-br"></div>

                <div class="header">
                    <div class="seal">🎓</div>
                    <div class="institution-name">{{ config('certificate.institution.name', 'YOUR INSTITUTION') }}</div>
                    <div class="gold-divider"></div>
                    <div class="subtitle">{{ config('certificate.institution.subtitle', 'Excellence in Education') }}</div>
                </div>

                <div class="certificate-title">
                    <h1>Certificate</h1>
                    <div class="title-divider"></div>
                    <div class="certificate-subtitle">of Achievement</div>
                </div>

                <div class="content">
                    <div class="award-text">This certifies that</div>
                    <div class="recipient-name">{{ $certificate->user->name }}</div>
                    <div class="award-text">has successfully completed the requirements and demonstrated mastery in</div>

                    <div class="course-section">
                        <div class="course-title">{{ $certificate->course->title }}</div>
                        @if($certificate->course->subtitle)
                        <p class="course-subtitle">{{ $certificate->course->subtitle }}</p>
                        @endif
                        @if($certificate->grade)
                        <div class="grade-badge">Grade: {{ $certificate->grade }}</div>
                        @endif
                    </div>
                </div>

                <div class="details-signatures-wrapper">
                    <div class="details-row">
                        <div class="detail-inline">
                            <span class="detail-label">Completed:</span> 
                            <span class="detail-value">{{ $certificate->completion_date->format('F j, Y') }}</span>
                        </div>
                        @if($certificate->credits)
                        <div class="detail-inline">
                            <span class="detail-label">Credits:</span> 
                            <span class="detail-value">{{ $certificate->credits }}</span>
                        </div>
                        @endif
                        <div class="detail-inline">
                            <span class="detail-label">Issued:</span> 
                            <span class="detail-value">{{ $certificate->issued_date->format('F j, Y') }}</span>
                        </div>
                    </div>

                    <div class="signatures">
                        <table>
                            <tr>
                                <td>
                                    <div class="signature-name">{{ $certificate->course->instructor->name }}</div>
                                    <div class="signature-title">Course Instructor</div>
                                </td>
                                <td>
                                    <div class="signature-name">{{ $certificate->approver->name ?? 'Academic Director' }}</div>
                                    <div class="signature-title">Academic Director</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="verification-section">
                    <table>
                        <tr>
                            <td class="verification-info">
                                <div class="verification-header">VERIFICATION</div>
                                <div class="verification-item">
                                    <span class="verification-label">Certificate No:</span>
                                    <span class="verification-value">{{ $certificate->certificate_number }}</span>
                                </div>
                                <div class="verification-item">
                                    <span class="verification-label">Code:</span>
                                    <span class="verification-value">{{ $certificate->verification_code }}</span>
                                </div>
                                <div class="verification-item">
                                    <span class="verification-label">Verify:</span>
                                    <span class="verification-value">{{ $certificate->verification_url }}</span>
                                </div>
                            </td>
                            <td class="qr-code-container">
                                <div class="qr-section-title">SCAN TO VERIFY</div>
                                @php
                                    $qrBase64 = null;
                                    if ($certificate->qr_code_path) {
                                        $qrPath = storage_path('app/public/' . $certificate->qr_code_path);
                                        if (file_exists($qrPath) && is_file($qrPath)) {
                                            try {
                                                $qrBase64 = base64_encode(file_get_contents($qrPath));
                                            } catch (\Exception $e) {
                                                \Log::error('QR Code embed error: ' . $e->getMessage());
                                            }
                                        }
                                    }
                                @endphp

                                @if($qrBase64)
                                    <img src="data:image/png;base64,{{ $qrBase64 }}" class="qr-code" alt="QR Code">
                                @else
                                    <div class="qr-placeholder">
                                        <div style="font-size: 16pt; color: #b8860b;">&#9733;</div>
                                        <div>SCAN TO<br>VERIFY</div>
                                    </div>
                                @endif
                                
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footer">
                    <span class="footer-ornament"></span>
                    <span style="color: #b8860b; font-size: 15pt;">&#10086;</span>
                    <span class="footer-ornament"></span>
                    <div class="footer-text">Authenticated & Verified</div>
                </div>
            </div>
        </div>
    </div>

    @if(!isset($isPdf) || !$isPdf)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    @endif
</body>
</html>