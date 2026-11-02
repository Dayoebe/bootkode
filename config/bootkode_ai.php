<?php

return [
    'enabled' => (bool) env('BOOTKODE_AI_ENABLED', false),
    'provider' => env('BOOTKODE_AI_PROVIDER', 'groq'),
    'timeout' => (int) env('BOOTKODE_AI_TIMEOUT', 30),
    'retries' => (int) env('BOOTKODE_AI_RETRIES', 1),
    'daily_requests' => (int) env('BOOTKODE_AI_DAILY_REQUESTS', 40),
    'max_input_characters' => (int) env('BOOTKODE_AI_MAX_INPUT_CHARACTERS', 6000),
    'max_history_messages' => (int) env('BOOTKODE_AI_MAX_HISTORY_MESSAGES', 12),
    'max_context_characters' => (int) env('BOOTKODE_AI_MAX_CONTEXT_CHARACTERS', 12000),
    'course_generation_max_output_tokens' => (int) env('BOOTKODE_AI_COURSE_GENERATION_MAX_OUTPUT_TOKENS', 6500),
    'course_generation_daily_requests' => (int) env('BOOTKODE_AI_COURSE_GENERATION_DAILY_REQUESTS', 12),

    'providers' => [
        'groq' => [
            'key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL'),
            'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        ],
        'openai' => [
            'key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],
    ],
];
