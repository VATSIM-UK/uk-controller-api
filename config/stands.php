<?php

return [
    'auto_allocate' => env('AUTO_ALLOCATE_STANDS', false),
    'assignment_acars_message' => env('SEND_STAND_ACARS_MESSAGES', true),
    // Optional overnight bias for remote parking at selected airfields.
    // This is disabled by default to preserve existing behaviour unless explicitly enabled.
    'night_remote_stand_weighting' => [
        // Feature flag: when true, apply remote-stand preference within the configured night window.
        'enabled' => env('NIGHT_REMOTE_STAND_WEIGHTING_ENABLED', false),
        // Arrival airfields where this weighting should apply.
        'airfields' => ['EGLL', 'EGKK', 'EGCC'],
        // Local Europe/London start hour (24h) for the overnight window (inclusive).
        'start_hour' => 22,
        // Local Europe/London end hour (24h) for the overnight window (exclusive).
        'end_hour' => 6,
    ],
];
