
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Commission Rate
    |--------------------------------------------------------------------------
    |
    | The default commission rate percentage that new affiliates receive
    | from the platform's share of course sales.
    |
    */
    'default_commission_rate' => env('AFFILIATE_DEFAULT_COMMISSION', 30.00),

    /*
    |--------------------------------------------------------------------------
    | Minimum Withdrawal Amount
    |--------------------------------------------------------------------------
    |
    | The minimum amount that affiliates must have before they can
    | request a withdrawal.
    |
    */
    'minimum_withdrawal' => env('AFFILIATE_MIN_WITHDRAWAL', 1000.00),

    /*
    |--------------------------------------------------------------------------
    | Auto-approve Instructors
    |--------------------------------------------------------------------------
    |
    | Whether to automatically approve instructor applications for
    | the affiliate program.
    |
    */
    'auto_approve_instructors' => env('AFFILIATE_AUTO_APPROVE_INSTRUCTORS', true),

    /*
    |--------------------------------------------------------------------------
    | Referral Code Length
    |--------------------------------------------------------------------------
    |
    | The length of generated referral codes.
    |
    */
    'referral_code_length' => 8,

    /*
    |--------------------------------------------------------------------------
    | Cookie Expiry Days
    |--------------------------------------------------------------------------
    |
    | How long the referral cookie should last (in days).
    |
    */
    'cookie_expiry_days' => 30,
];