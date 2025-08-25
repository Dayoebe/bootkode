<div>
@php
    // Check if certificate is passed directly to the view
    if (!isset($certificate)) {
        // Check if we're in a Livewire context and get the component instance
        $livewireComponent = null;
        if (app()->has('livewire')) {
            try {
                $livewireComponent = app('livewire')->current();
                if ($livewireComponent && property_exists($livewireComponent, 'previewCertificate')) {
                    $certificate = $livewireComponent->previewCertificate;
                }
            } catch (Exception $e) {
                // Livewire not available in this context
            }
        }
    }
    @endphp

    @if (isset($certificate))
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Certificate of Completion</title>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap');

                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Crimson Text', serif;
                    background: white;
                    width: 297mm;
                    /* A4 landscape width */
                    height: 210mm;
                    /* A4 landscape height */
                    margin: 0;
                    padding: 20mm;
                    position: relative;
                }

                .certificate-container {
                    width: 100%;
                    height: 100%;
                    border: 8px solid #1a365d;
                    border-radius: 12px;
                    position: relative;
                    overflow: hidden;
                }

                .certificate-inner-border {
                    border: 2px solid #2d3748;
                    margin: 15px;
                    padding: 40px 60px;
                    background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
                    position: relative;
                    height: calc(100% - 30px);
                }

                /* Header Section */
                .certificate-header {
                    text-align: center;
                    margin-bottom: 30px;
                }

                .institution-seal {
                    width: 80px;
                    height: 80px;
                    margin: 0 auto 15px;
                    background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 32px;
                    border: 3px solid #cbd5e0;
                }

                .institution-name {
                    font-family: 'Playfair Display', serif;
                    font-size: 24px;
                    font-weight: 700;
                    color: #1a365d;
                    margin-bottom: 8px;
                    letter-spacing: 1px;
                }

                .institution-subtitle {
                    font-size: 14px;
                    color: #4a5568;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                }

                /* Certificate Title */
                .certificate-title {
                    text-align: center;
                    margin: 30px 0 25px;
                }

                .certificate-title h1 {
                    font-family: 'Playfair Display', serif;
                    font-size: 36px;
                    font-weight: 900;
                    color: #1a365d;
                    margin-bottom: 8px;
                }

                .certificate-subtitle {
                    font-size: 16px;
                    color: #2d3748;
                    font-weight: 400;
                    font-style: italic;
                }

                /* Main Content */
                .certificate-content {
                    text-align: center;
                    line-height: 1.6;
                    color: #2d3748;
                    margin: 25px 0;
                }

                .award-text {
                    font-size: 18px;
                    margin-bottom: 20px;
                    font-weight: 400;
                }

                .recipient-name {
                    font-family: 'Playfair Display', serif;
                    font-size: 32px;
                    font-weight: 700;
                    color: #1a365d;
                    margin: 15px 0;
                    border-bottom: 2px solid #cbd5e0;
                    display: inline-block;
                    padding-bottom: 3px;
                    min-width: 300px;
                    text-align: center;
                }

                .course-details {
                    margin: 20px 0;
                }

                .course-title {
                    font-family: 'Playfair Display', serif;
                    font-size: 22px;
                    font-weight: 700;
                    color: #1a365d;
                    margin-bottom: 8px;
                }

                .completion-details {
                    display: flex;
                    justify-content: space-between;
                    margin: 30px 0 20px;
                    font-size: 14px;
                    color: #4a5568;
                }

                .completion-item {
                    text-align: center;
                    flex: 1;
                }

                .completion-label {
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 3px;
                    color: #2d3748;
                }

                .completion-value {
                    font-size: 16px;
                    font-weight: 700;
                    color: #1a365d;
                }

                /* Signatures Section */
                .signatures-section {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 40px;
                    padding-top: 30px;
                    border-top: 1px solid #e2e8f0;
                }

                .signature {
                    text-align: center;
                    flex: 1;
                    margin: 0 15px;
                }

                .signature-line {
                    border-bottom: 2px solid #2d3748;
                    margin-bottom: 8px;
                    height: 40px;
                }

                .signature-name {
                    font-weight: 700;
                    color: #1a365d;
                    font-size: 16px;
                    margin-bottom: 3px;
                }

                .signature-title {
                    font-size: 12px;
                    color: #4a5568;
                    font-style: italic;
                }

                /* Verification Section */
                .verification-section {
                    position: absolute;
                    bottom: 15px;
                    left: 15px;
                    right: 15px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    font-size: 10px;
                    color: #718096;
                }

                .certificate-number {
                    font-weight: 600;
                    color: #2d3748;
                }

                .qr-code {
                    width: 40px;
                    height: 40px;
                    background: white;
                    border: 1px solid #e2e8f0;
                    border-radius: 4px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 6px;
                }

                /* Grade Badge */
                .grade-badge {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
                    color: white;
                    padding: 10px 15px;
                    border-radius: 50%;
                    font-family: 'Playfair Display', serif;
                    font-size: 18px;
                    font-weight: 700;
                    width: 60px;
                    height: 60px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 2px solid #cbd5e0;
                }

                .watermark {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(-15deg);
                    font-size: 80px;
                    color: rgba(26, 54, 93, 0.03);
                    font-weight: 900;
                    z-index: 0;
                    pointer-events: none;
                }

                .flourish {
                    text-align: center;
                    font-size: 18px;
                    color: #cbd5e0;
                    margin: 15px 0;
                }
            </style>
        </head>

        <body>
            <div class="certificate-container">
                <div class="certificate-inner-border">
                    <!-- Watermark -->
                    <div class="watermark">CERTIFIED</div>

                    <!-- Grade Badge -->
                    @if ($certificate->grade)
                        <div class="grade-badge">
                            {{ $certificate->grade }}
                        </div>
                    @endif

                    <!-- Header Section -->
                    <div class="certificate-header">
                        <div class="institution-seal">
                            &#127891;
                        </div>
                        <div class="institution-name">
                            {{ config('certificate.institution.name', 'ACADEMY LEARNING PLATFORM') }}</div>
                        <div class="institution-subtitle">
                            {{ config('certificate.institution.subtitle', 'Excellence in Online Education') }}</div>
                    </div>

                    <!-- Certificate Title -->
                    <div class="certificate-title">
                        <h1>Certificate of Completion</h1>
                        <div class="certificate-subtitle">This is to certify that</div>
                    </div>

                    <!-- Main Content -->
                    <div class="certificate-content">
                        <div class="award-text">
                            has successfully completed the course
                        </div>

                        <div class="recipient-name">
                            {{ $certificate->user->name }}
                        </div>

                        <div class="course-details">
                            <div class="course-title">{{ $certificate->course->title }}</div>
                            @if ($certificate->course->subtitle)
                                <div class="course-description">{{ $certificate->course->subtitle }}</div>
                            @endif
                        </div>

                        <div class="flourish">❦ ❦ ❦</div>

                        <div class="completion-details">
                            <div class="completion-item">
                                <div class="completion-label">Completion Date</div>
                                <div class="completion-value">{{ $certificate->completion_date->format('F j, Y') }}
                                </div>
                            </div>

                            @if ($certificate->credits)
                                <div class="completion-item">
                                    <div class="completion-label">Credits Earned</div>
                                    <div class="completion-value">{{ $certificate->credits }}</div>
                                </div>
                            @endif

                            <div class="completion-item">
                                <div class="completion-label">Certificate Date</div>
                                <div class="completion-value">{{ $certificate->issued_date->format('F j, Y') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="signatures-section">
                        <div class="signature">
                            <div class="signature-line"></div>
                            <div class="signature-name">
                                {{ $certificate->course->instructor->name ?? 'Course Instructor' }}</div>
                            <div class="signature-title">Course Instructor</div>
                        </div>

                        <div class="signature">
                            <div class="signature-line"></div>
                            <div class="signature-name">{{ $certificate->approver->name ?? 'Academic Director' }}</div>
                            <div class="signature-title">Academic Director</div>
                        </div>
                    </div>

                    <!-- Verification Section -->
                    <div class="verification-section">
                        <div>
                            <div class="certificate-number">Certificate No: {{ $certificate->certificate_number }}
                            </div>
                            <div>Verify at:
                                {{ $certificate->verification_url ?? route('certificate.verify.code', $certificate->verification_code) }}
                            </div>
                        </div>

                        <div class="qr-code">
                            <!-- Simplified QR code display for preview -->
                            <div
                                style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                QR CODE
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>

        </html>
    @else
        <div class="p-4 bg-yellow-100 text-yellow-800">
            Certificate data not available for preview. yeshsh
        </div>
    @endif

</div>
