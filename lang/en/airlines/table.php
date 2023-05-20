<?php

return [
    'columns' => [
        'icao_code' => 'ICAO Code',
        'name' => 'Name',
        'callsign' => 'RTF Callsign',
        'is_cargo' => 'Is Cargo',
    ],
    'terminals' => [
        'description' => 'Airlines can be assigned to specific terminals based on various parameters. See the allocation guide
        for more details.',
        'columns' => [
            'terminal' => 'Terminal',
            'aircraft' => 'Aircraft Type',
            'destination' => 'Origin',
            'full_callsign' => 'Callsign',
            'callsign_slug' => 'Partial Callsign',
            'priority' => 'Allocation Priority',
        ],
    ],
    'stands' => [
        'description' => 'Airlines can be assigned to specific stands based on various parameters. See the allocation guide
        for more details.',
        'columns' => [
            'stand' => 'Stand',
            'aircraft' => 'Aircraft Type',
            'destination' => 'Origin',
            'full_callsign' => 'Callsign',
            'callsign_slug' => 'Partial Callsign',
            'priority' => 'Allocation Priority',
            'not_before' => 'Not Before [UTC]',
        ],
    ],
];
