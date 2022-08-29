<?php

return [
    'required' => 'This field is required.',
    'numeric' => 'The value must be numeric.',
    'unique' => 'This field must be unique.',
    'frequency' => 'Frequency is invalid.',
    'callsign' => 'Callsign is invalid.',
    'after' => 'End date must be after start date.',
    'stands' => require_once __DIR__ . '/stands/validation.php',
    'sids' => require_once __DIR__ . '/sids/validation.php',
];
