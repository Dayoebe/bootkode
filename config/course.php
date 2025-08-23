<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Course Progress Configuration
    |--------------------------------------------------------------------------
    |
    | These options configure how course progress and section unlocking work
    |
    */

    // Percentage of section completion required to unlock next section
    'section_completion_threshold' => env('SECTION_COMPLETION_THRESHOLD', 80),
    
    // Whether to allow students to jump to any section (bypassing prerequisites)
    'allow_section_jumping' => env('ALLOW_SECTION_JUMPING', false),
    
    // Whether to show locked content in the sidebar
    'show_locked_content' => env('SHOW_LOCKED_CONTENT', true),
    
    // Default course completion threshold for certificates
    'course_completion_threshold' => env('COURSE_COMPLETION_THRESHOLD', 90),
];