<?php

return [
    'required' => 'This field is required.',
    'numeric' => 'The value must be numeric.',
    'unique' => 'This field must be unique.',
    'frequency' => 'Frequency is invalid.',
    'callsign' => 'Callsign is invalid.',
    'heading' => 'Headings must be between 1 and 360 degrees (inclusive).',
    'after' => 'End date must be after start date.',
    'runways' => require_once __DIR__ . '/runways/validation.php',
    'stands' => require_once __DIR__ . '/stands/validation.php',
    'sids' => require_once __DIR__ . '/sids/validation.php',
];
