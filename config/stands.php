<?php

return [
    'auto_allocate' => env('AUTO_ALLOCATE_STANDS', false),
    'assignment_acars_message' => env('SEND_STAND_ACARS_MESSAGES', true),
    'night_remote_stand_weighting' => [
        'enabled' => true,
        // Europe/London time start and end hour (24h) for the overnight window (Automatic DST support).
        'start_hour' => 22,
        'end_hour' => 6,
    ],
];
