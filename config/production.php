<?php

return [
    // Explicit operator declarations; preflight does not infer host capabilities.
    'queue_mode' => env('PRODUCTION_QUEUE_MODE', 'unconfigured'),
    'cron_max_runtime' => (int) env('PRODUCTION_CRON_MAX_RUNTIME', 0),
];
