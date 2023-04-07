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
        'destination' => [
            'label' => 'Destination',
            'helper' => 'The destination used for this allocation. Can be partial matches, for example, "EGJ".'
        ],
        'callsign' => [
            'label' => 'Callsign Slug',
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
