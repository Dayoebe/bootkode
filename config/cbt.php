<?php

return [
    'security' => [
        'max_violations' => env('CBT_MAX_SECURITY_VIOLATIONS', 5),
        'session_timeout' => env('CBT_SESSION_TIMEOUT_MINUTES', 30),
        'require_fullscreen' => env('CBT_REQUIRE_FULLSCREEN', true),
        'allow_copy_paste' => env('CBT_ALLOW_COPY_PASTE', false),
        'enable_proctoring' => env('CBT_ENABLE_PROCTORING', false),
    ],
    
    'features' => [
        'auto_save_interval' => 30, // seconds
        'heartbeat_interval' => 60, // seconds
        'warning_time' => 300, // 5 minutes before timeout
    ],
    
    'ui' => [
        'questions_per_page' => 1,
        'show_progress' => true,
        'allow_flagging' => true,
        'show_timer' => true,
    ]
];