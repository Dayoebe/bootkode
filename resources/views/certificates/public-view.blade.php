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
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .certificate-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            position: relative;
        }

        .certificate-border {
            border: 8px solid #1a365d;
            border-radius: 12px;
            margin: 20px;
            position: relative;
            overflow: hidden;
        }

        .certificate-inner-border {
            border: 2px solid #2d3748;
            margin: 15px;
            padding: 60px 80px;
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            position: relative;
            min-height: 600px;
        }

        /* Decorative Corner Elements */
        .corner-decoration {
            position: absolute;
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #1a365d, #2d3748);
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .corner-top-left {
            top: 0;
            left: 0;
        }

        .corner-top-right {
            top: 0;
            right: 0;
            transform: rotate(90deg);
        }

        .corner-bottom-left {
            bottom: 0;
            left: 0;
            transform: rotate(270deg);
        }

        .corner-bottom-right {
            bottom: 0;
            right: 0;
            transform: rotate(180deg);
        }

        /* Header Section */
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .institution-seal {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            border: 4px solid #cbd5e0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .institution-name {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .institution-subtitle {
            font-size: 16px;
            color: #4a5568;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Certificate Title */
        .certificate-title {
            text-align: center;
            margin: 50px 0 40px;
        }

        .certificate-title h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 900;
            color: #1a365d;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .certificate-subtitle {
            font-size: 20px;
            color: #2d3748;
            font-weight: 400;
            font-style: italic;
        }

        /* Main Content */
        .certificate-content {
            text-align: center;
            line-height: 1.8;
            color: #2d3748;
            margin: 40px 0;
        }

        .award-text {
            font-size: 22px;
            margin-bottom: 30px;
            font-weight: 400;
        }

        .recipient-name {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: #1a365d;
            margin: 20px 0;
            border-bottom: 3px solid #cbd5e0;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 400px;
            text-align: center;
        }

        .course-details {
            margin: 30px 0;
        }

        .course-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 10px;
        }

        .course-description {
            font-size: 18px;
            color: #4a5568;
            font-style: italic;
        }

        .completion-details {
            display: flex;
            justify-content: space-between;
            margin: 50px 0 30px;
            font-size: 16px;
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
            margin-bottom: 5px;
            color: #2d3748;
        }

        .completion-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a365d;
        }

        /* Signatures Section */
        .signatures-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 40px;
            border-top: 2px solid #e2e8f0;
        }

        .signature {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }

        .signature-line {
            border-bottom: 2px solid #2d3748;
            margin-bottom: 10px;
            height: 60px;
            position: relative;
        }

        .signature-name {
            font-weight: 700;
            color: #1a365d;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .signature-title {
            font-size: 14px;
            color: #4a5568;
            font-style: italic;
        }

        /* Verification Section */
        .verification-section {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #718096;
        }

        .certificate-number {
            font-weight: 600;
            color: #2d3748;
        }

        .qr-code {
            width: 60px;
            height: 60px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
        }

        .verification-url {
            font-family: monospace;
            font-size: 10px;
            color: #4a5568;
        }

        /* Grade Badge */
        .grade-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 50%;
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #cbd5e0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Decorative Elements */
        .flourish {
            text-align: center;
            font-size: 24px;
            color: #cbd5e0;
            margin: 20px 0;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 120px;
            color: rgba(26, 54, 93, 0.03);
            font-weight: 900;
            z-index: 0;
            pointer-events: none;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .certificate-container {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
            }
            
            .certificate-border {
                margin: 0;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .certificate-inner-border {
                padding: 40px 30px;
                margin: 10px;
            }
            
            .institution-name {
                font-size: 24px;
            }
            
            .certificate-title h1 {
                font-size: 36px;
            }
            
            .recipient-name {
                font-size: 32px;
                min-width: 300px;
            }
            
            .course-title {
                font-size: 24px;
            }
            
            .signatures-section {
                flex-direction: column;
                gap: 30px;
            }
            
            .completion-details {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-inner-border">
                <!-- Corner Decorations -->
                <div class="corner-decoration corner-top-left"></div>
                <div class="corner-decoration corner-top-right"></div>
                <div class="corner-decoration corner-bottom-left"></div>
                <div class="corner-decoration corner-bottom-right"></div>

                <!-- Watermark -->
                <div class="watermark">CERTIFIED</div>

                <!-- Grade Badge -->
                @if($certificate->grade)
                <div class="grade-badge">
                    {{ $certificate->grade }}
                </div>
                @endif

                <!-- Header Section -->
                <div class="certificate-header">
                    <div class="institution-seal">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="institution-name">ACADEMY LEARNING PLATFORM</div>
                    <div class="institution-subtitle">Excellence in Online Education</div>
                </div>

                <!-- Certificate Title -->
                <div class="certificate-title">
                    <h1>Certificate of Completion</h1>
                    <div class="certificate-subtitle">This is to certify that</div>
                </div>

                <!-- Main Content -->
                <div class="certificate-content">
                    <div class="award-text">
                        is hereby awarded this certificate in recognition of successfully completing
                    </div>

                    <div class="recipient-name">
                        {{ $certificate->user->name }}
                    </div>

                    <div class="course-details">
                        <div class="course-title">{{ $certificate->course->title }}</div>
                        @if($certificate->course->subtitle)
                        <div class="course-description">{{ $certificate->course->subtitle }}</div>
                        @endif
                    </div>

                    <div class="flourish">❦ ❦ ❦</div>

                    <div class="completion-details">
                        <div class="completion-item">
                            <div class="completion-label">Completion Date</div>
                            <div class="completion-value">{{ $certificate->completion_date->format('F j, Y') }}</div>
                        </div>
                        
                        @if($certificate->credits)
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
                        <div class="signature-name">{{ $certificate->course->instructor->name }}</div>
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
                        <div class="certificate-number">Certificate No: {{ $certificate->certificate_number }}</div>
                        <div class="verification-url">Verify at: {{ $certificate->verification_url }}</div>
                    </div>

                    @if($certificate->qr_code_path)
                    <div class="qr-code">
                        <img src="{{ asset('storage/' . $certificate->qr_code_path) }}" 
                             alt="Verification QR Code" 
                             style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    @else
                    <div class="qr-code">
                        QR CODE
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome for Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>