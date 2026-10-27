<?php

return [
    // Enable only after the normal Laravel scheduler and queue have been verified.
    'job_alerts_schedule_enabled' => (bool) env('JOB_ALERTS_SCHEDULE_ENABLED', false),
];
