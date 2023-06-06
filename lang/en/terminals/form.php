<?php

return [
    'airfield' => [
        'label' => 'Airfield',
    ],
    'description' => [
        'label' => 'Description',
    ],
    'airlines' => [
        'icao' => [
            'label' => 'ICAO Code',
        ],
        'aircraft' => [
            'label' => 'Aircraft Type',
            'helper' => 'The aircraft type that this airline will use when using this stand.'
        ],
        'destination' => [
            'label' => 'Destination',
            'helper' => 'The destination used for this allocation. Can be partial matches, for example, "EGJ".'
        ],
        'full_callsign' => [
            'label' => 'Callsign',
            'helper' => 'The part of the callsign after the ICAO code for this allocation. Must be an exact match.'
        ],
        'callsign_slug' => [
            'label' => 'Partial Callsign',
            'helper' => 'The part of the callsign after the ICAO code for this allocation. Can be partial matches.'
        ],
        'priority' => [
            'label' => 'Allocation Priority',
            'helper' => 'Priority for allocating this terminal, lower value is higher priority. ' .
                'Considered before general allocation priority. Minimum 1, maximum 9999.'
        ],
        'remove' => [
            'label' => 'Remove',
        ],
    ],
];
