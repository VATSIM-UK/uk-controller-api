<?php

return [
    'auto_allocate' => env('AUTO_ALLOCATE_STANDS', false),
    'assignment_acars_message' => env('SEND_STAND_ACARS_MESSAGES', true),
    // Overnight bias for remote parking.
    'night_remote_stand_weighting' => [
        // Local Europe/London start hour (24h) for the overnight window (inclusive).
        'start_hour' => 22,
        // Local Europe/London end hour (24h) for the overnight window (exclusive).
        'end_hour' => 6,
    ],
];
