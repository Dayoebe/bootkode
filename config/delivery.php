<?php

return [
    // Enable only after the normal Laravel scheduler and queue have been verified.
    'newsletter_schedule_enabled' => (bool) env('NEWSLETTER_SCHEDULE_ENABLED', true),
    'withdrawal_processing_schedule_enabled' => (bool) env('WITHDRAWAL_PROCESSING_SCHEDULE_ENABLED', false),
    'job_alerts_schedule_enabled' => (bool) env('JOB_ALERTS_SCHEDULE_ENABLED', false),
    'learning_reminders_schedule_enabled' => (bool) env('LEARNING_REMINDERS_SCHEDULE_ENABLED', false),
];
