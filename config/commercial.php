<?php

return [
    'currency' => env('COMMERCIAL_DEFAULT_CURRENCY', 'NGN'),

    'tax_rate' => (float) env('COMMERCIAL_TAX_RATE', 0),

    'refunds' => [
        'use_paystack_api' => (bool) env('COMMERCIAL_REFUNDS_USE_PAYSTACK_API', false),
        'window_days' => (int) env('COMMERCIAL_REFUND_WINDOW_DAYS', 7),
    ],

    'public_packages' => [
        [
            'name' => 'Starter Learner',
            'slug' => 'starter-learner',
            'audience' => 'Individual learners',
            'description' => 'A clear entry point for learners who want structured courses, certificates, and wallet-based checkout.',
            'price' => 0,
            'currency' => 'NGN',
            'interval' => 'free',
            'is_featured' => false,
            'cta_label' => 'Start learning',
            'cta_route' => 'register',
            'features' => [
                'Browse free and paid courses',
                'Wallet funding through Paystack',
                'Learning dashboard and progress tracking',
                'Receipts for successful payments',
            ],
        ],
        [
            'name' => 'Career Builder',
            'slug' => 'career-builder',
            'audience' => 'Job-ready learners',
            'description' => 'For learners who need mentorship, reviewed project evidence, certificates, and career support.',
            'price' => 15000,
            'currency' => 'NGN',
            'interval' => 'from',
            'is_featured' => true,
            'cta_label' => 'View courses',
            'cta_route' => 'student.course-catalog',
            'features' => [
                'Paid course enrollment',
                'Mentorship and code review workflow',
                'Assessment feedback and certificate readiness',
                'Invoices, receipts, and refund handling',
            ],
        ],
        [
            'name' => 'Institution License',
            'slug' => 'institution-license',
            'audience' => 'Schools and organizations',
            'description' => 'A licensing path for cohorts, bulk enrollment, school admins, progress reports, and license controls.',
            'price' => null,
            'currency' => 'NGN',
            'interval' => 'custom',
            'is_featured' => false,
            'cta_label' => 'Talk to BootKode',
            'cta_route' => 'contact',
            'features' => [
                'Bulk student onboarding',
                'Cohort course assignment',
                'Completion reports and exports',
                'License limit management',
            ],
        ],
        [
            'name' => 'Marketplace Vendor',
            'slug' => 'marketplace-vendor',
            'audience' => 'Instructors and partners',
            'description' => 'Commercial rails for selling resources, receiving payouts, and tracking payout audit history.',
            'price' => null,
            'currency' => 'NGN',
            'interval' => 'commission',
            'is_featured' => false,
            'cta_label' => 'Sell a product',
            'cta_route' => 'marketplace.seller.create',
            'features' => [
                'Marketplace listings and orders',
                'Revenue split and wallet earnings',
                'Withdrawal processing',
                'Payout audit trail',
            ],
        ],
    ],
];
