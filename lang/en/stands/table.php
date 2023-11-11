<?php

return [
    'columns' => [
        'airfield' => 'Airfield',
        'terminal' => 'Terminal',
        'identifier' => 'Identifier',
        'aerodrome_reference_code' => 'Reference Code',
        'max_size_wingspan' => 'Max Wingspan(m)',
        'max_size_length' => 'Max Length(m)',
        'airlines' => 'Airlines',
        'used' => 'Used',
        'priority' => 'Allocation Priority',
    ],
    'airlines' => [
        'description' => 'Airlines can be assigned to specific stands based on various parameters. See the allocation guide
        for more details.',
        'columns' => [
            'icao' => 'ICAO Code',
            'aircraft' => 'Aircraft Type',
            'destination' => 'Origin',
            'full_callsign' => 'Callsign',
            'callsign_slug' => 'Callsign Slug',
            'priority' => 'Allocation Priority',
            'not_before' => 'Not Before [UTC]',
        ],
    ],
    'paired' => [
        'description' => 'Stands that are paired cannot be simultaneously assigned to aircraft. ' .
            'Note, this does not prevent aircraft from spawning up on a stand!',
        'columns' => [
            'id' => 'Id',
            'airfield' => 'Airfield',
            'identifier' => 'Identifier',
        ],
    ],
];
