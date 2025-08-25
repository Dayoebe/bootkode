<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Certificate Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the certificate system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Certificate Requirements
    |--------------------------------------------------------------------------
    |
    | Define the requirements for certificate eligibility.
    |
    */
    'required_completion_percentage' => env('CERTIFICATE_REQUIRED_COMPLETION', 100),
    'minimum_grade' => env('CERTIFICATE_MINIMUM_GRADE', null),
    'require_assessments' => env('CERTIFICATE_REQUIRE_ASSESSMENTS', false),
    
    /*
    |--------------------------------------------------------------------------
    | Certificate Templates
    |--------------------------------------------------------------------------
    |
    | Define available certificate templates.
    |
    */
    'templates' => [
        'default' => [
            'name' => 'Default Certificate',
            'view' => 'certificates.templates.default',
            'orientation' => 'landscape',
            'size' => 'A4',
        ],
        'formal' => [
            'name' => 'Formal Certificate',
            'view' => 'certificates.templates.formal',
            'orientation' => 'landscape', 
            'size' => 'A4',
        ],
        'modern' => [
            'name' => 'Modern Certificate',
            'view' => 'certificates.templates.modern',
            'orientation' => 'landscape',
            'size' => 'A4',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Template
    |--------------------------------------------------------------------------
    |
    | The default template to use when generating certificates.
    |
    */
    'default_template' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Certificate Storage
    |--------------------------------------------------------------------------
    |
    | Configure where certificate files are stored.
    |
    */
    'storage' => [
        'disk' => env('CERTIFICATE_STORAGE_DISK', 'public'),
        'pdf_path' => 'certificates/pdfs',
        'qr_path' => 'certificates/qr_codes',
        'template_path' => 'certificates/templates',
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    |
    | Configure QR code generation settings.
    |
    */
    'qr_code' => [
        'size' => 200,
        'margin' => 10,
        'format' => 'png',
        'error_correction' => 'M', // L, M, Q, H
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Generation
    |--------------------------------------------------------------------------
    |
    | Configure PDF generation settings.
    |
    */
    'pdf' => [
        'engine' => env('CERTIFICATE_PDF_ENGINE', 'dompdf'), // dompdf, mpdf
        'options' => [
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Institution Information
    |--------------------------------------------------------------------------
    |
    | Information about the issuing institution.
    |
    */
    'institution' => [
        'name' => env('CERTIFICATE_INSTITUTION_NAME', 'Academy Learning Platform'),
        'subtitle' => env('CERTIFICATE_INSTITUTION_SUBTITLE', 'Excellence in Online Education'),
        'logo' => env('CERTIFICATE_INSTITUTION_LOGO', null),
        'seal' => env('CERTIFICATE_INSTITUTION_SEAL', null),
        'website' => env('APP_URL', 'https://academy.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Settings
    |--------------------------------------------------------------------------
    |
    | Configure certificate verification settings.
    |
    */
    'verification' => [
        'url_prefix' => env('CERTIFICATE_VERIFICATION_URL', null),
        'rate_limit' => [
            'attempts' => 60,
            'decay_minutes' => 1,
        ],
        'cache_duration' => 3600, // 1 hour in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configure certificate-related notifications.
    |
    */
    'notifications' => [
        'enabled' => env('CERTIFICATE_NOTIFICATIONS_ENABLED', true),
        'channels' => ['mail', 'database'],
        'auto_approve_notifications' => [
            'super_admin' => true,
            'academy_admin' => true,
            'instructor' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Configure security-related settings.
    |
    */
    'security' => [
        'verification_code_length' => 32,
        'prevent_duplicate_requests' => true,
        'auto_cleanup_rejected' => false,
        'revocation_allowed_roles' => ['super_admin', 'academy_admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Grade Scale
    |--------------------------------------------------------------------------
    |
    | Define the grading scale for certificates.
    |
    */
    'grading' => [
        'scale' => [
            'A+' => 97,
            'A' => 93,
            'A-' => 90,
            'B+' => 87,
            'B' => 83,
            'B-' => 80,
            'C+' => 77,
            'C' => 73,
            'C-' => 70,
            'D' => 60,
            'F' => 0,
        ],
        'passing_grade' => 60,
        'default_grade' => 'Pass',
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Operations
    |--------------------------------------------------------------------------
    |
    | Configure batch operation limits.
    |
    */
    'batch' => [
        'max_certificates' => 50,
        'max_bulk_approve' => 25,
        'processing_timeout' => 300, // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Configure API-related settings.
    |
    */
    'api' => [
        'enabled' => true,
        'rate_limit' => [
            'attempts' => 100,
            'decay_minutes' => 1,
        ],
        'allowed_origins' => ['*'],
    ],
];