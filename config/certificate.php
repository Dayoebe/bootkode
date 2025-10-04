<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Institution Information
    |--------------------------------------------------------------------------
    */
    'institution' => [
        'name' => env('CERTIFICATE_INSTITUTION_NAME', 'Bootkode Academy'),
        'subtitle' => env('CERTIFICATE_INSTITUTION_SUBTITLE', 'Learning Platform'),
        'logo_path' => env('CERTIFICATE_LOGO_PATH', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => env('CERTIFICATE_STORAGE_DISK', 'public'),
        'qr_path' => env('CERTIFICATE_QR_PATH', 'certificates/qr'),
        'pdf_path' => env('CERTIFICATE_PDF_PATH', 'certificates/pdf'),
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    */
    'qr_code' => [
        'size' => env('CERTIFICATE_QR_SIZE', 300), // Increased for better scanning
        'margin' => env('CERTIFICATE_QR_MARGIN', 15),
        'error_correction' => 'high', // high, low, medium, quartile
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Configuration
    |--------------------------------------------------------------------------
    */
    'pdf' => [
        'options' => [
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false, // Security: disabled for safety
            'enable_php' => false, // Security: disabled for safety
            'dpi' => 150,
            'debugPng' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificate Templates
    |--------------------------------------------------------------------------
    */
    'templates' => [
        'default' => [
            'view' => 'certificates.public-view',
            'size' => 'A4',
            'orientation' => 'landscape',
        ],
        // You can add more templates here
        // 'premium' => [
        //     'view' => 'certificates.certificate-premium',
        //     'size' => 'A4',
        //     'orientation' => 'landscape',
        // ],
    ],

    'default_template' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Certificate Requirements
    |--------------------------------------------------------------------------
    */
    'required_completion_percentage' => env('CERTIFICATE_REQUIRED_COMPLETION', 100),
    'require_all_assessments_passed' => env('CERTIFICATE_REQUIRE_ASSESSMENTS', false),
    'minimum_time_spent_hours' => env('CERTIFICATE_MIN_TIME_HOURS', 0),

    /*
    |--------------------------------------------------------------------------
    | Grading System
    |--------------------------------------------------------------------------
    */
    'grading' => [
        'enabled' => env('CERTIFICATE_GRADING_ENABLED', true),
        'default_grade' => 'Pass',
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
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => env('CERTIFICATE_NOTIFICATIONS_ENABLED', true),
        'notify_on_approval' => true,
        'notify_on_rejection' => true,
        'notify_on_revocation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Features
    |--------------------------------------------------------------------------
    */
    'security' => [
        'watermark_enabled' => true,
        'hologram_effect' => true,
        'security_strip' => true,
        'verify_on_download' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Settings
    |--------------------------------------------------------------------------
    */
    'verification' => [
        'log_attempts' => true,
        'rate_limit' => [
            'enabled' => true,
            'max_attempts' => 100,
            'decay_minutes' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Expiration Settings
    |--------------------------------------------------------------------------
    */
    'expiration' => [
        'enabled' => env('CERTIFICATE_EXPIRATION_ENABLED', false),
        'default_validity_years' => env('CERTIFICATE_VALIDITY_YEARS', null),
        'notify_before_expiry_days' => 30,
    ],
];