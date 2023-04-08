<?php

return [
    'columns' => [
        'icao_code' => 'ICAO Code',
        'name' => 'Name',
        'callsign' => 'RTF Callsign',
        'is_cargo' => 'Is Cargo',
    ],
    'terminals' => [
        'description' => 'Airlines can be assigned to specific terminals based on various parameters. The allocation ' .
            'tries to match by Callsign Slug, then by Destination (always taking priority into account), then any ' .
            'stand for that airline (preferring ones without specific callsigns or destinations).',
        'columns' => [
            'terminal' => 'Terminal',
            'destination' => 'Origin',
            'callsign' => 'Callsign Slug',
            'priority' => 'Allocation Priority',
        ],
    ],
];
