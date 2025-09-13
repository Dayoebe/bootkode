<?php
return [
    'commission_rate' => env('MARKETPLACE_COMMISSION_RATE', 20.0),
    'auto_approve_instructors' => env('MARKETPLACE_AUTO_APPROVE_INSTRUCTORS', true),
    'require_admin_approval' => env('MARKETPLACE_REQUIRE_ADMIN_APPROVAL', false),
    'max_thumbnail_size' => 2048, // KB
    'max_image_count' => 10,
    'max_file_size' => 10240, // KB
    'allowed_file_types' => ['pdf', 'zip', 'docx', 'pptx', 'xlsx'],
    'currencies' => [
        'NGN' => 'Nigerian Naira',
        'USD' => 'US Dollar',
    ],
    'default_currency' => 'NGN',
];