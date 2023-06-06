<?php

return [
    'icao_code' => [
        'label' => 'ICAO Code',
    ],
    'name' => [
        'label' => 'Name',
    ],
    'callsign' => [
        'label' => 'Callsign',
        'helper' => 'The RTF callsign for the airline',
    ],
    'is_cargo' => [
        'label' => 'Is Cargo',
        'helper' => 'Airlines designated as cargo will always be assigned cargo stands in lieu of specific airline assignments',
    ],
    'fieldset_creation_options' => [
        'label' => 'Creation Options',
    ],
    'copy_stand_assignments' => [
        'label' => 'Copy Stand Assignments',
        'helper' => 'If selected, will copy any existing stand and terminal assignments from the specified airline when creating.'
    ],
    'terminals' => [
        'terminal' => [
            'label' => 'Terminal',
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
    'stands' => [
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
            'helper' => 'Priority for allocating this stand, lower value is higher priority. ' .
                'Considered before general allocation priority. Minimum 1, maximum 9999.'
        ],
        'not_before' => [
            'label' => 'Do not allocate before (UTC)',
            'helper' => 'Will not allocate this stand automatically for arrivals before this time.'
        ],
        'remove' => [
            'label' => 'Remove',
        ],
    ],
];
