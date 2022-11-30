<?php

return [
    'airfield' => [
        'label' => 'Airfield',
    ],
    'terminal' => [
        'label' => 'Terminal',
        'helper' => 'Some airfields allocate airlines by terminal rather than specific stands. You can assign stands ' .
        'to terminals here.',
    ],
    'identifier' => [
        'label' => 'Identifier',
        'helper' => 'Stand identifiers must be unique at a given airfield.',
    ],
    'type' => [
        'label' => 'Type',
        'helper' => 'At airfields where certain stands are designated for only domestic or international flights, ' .
        'this option can be selected to designate the type of stand.',
    ],
    'latitude' => [
        'label' => 'Latitude',
        'helper' => 'The decimal latitude of the stand.',
    ],
    'longitude' => [
        'label' => 'Longitude',
        'helper' => 'The decimal longitude of the stand.',
    ],
    'wake_category' => [
        'label' => 'Maximum UK Wake Category',
        'helper' => 'Maximum UK WTC that can be assigned to this stand. Used as a fallback if no specific ' .
        'aircraft type if specified.',
    ],
    'aircraft_type' => [
        'label' => 'Maximum Aircraft Type',
        'helper' => 'Maximum aircraft size that can be assigned to the stand. Overrides Max WTC.',
    ],
    'used_for_allocation' => [
        'label' => 'Used for Allocation',
        'helper' => 'Stands not used for allocation will not be allocated by the automatic allocator ' .
        'or be available for controllers to assign.',
    ],
    'allocation_priority' => [
        'label' => 'Allocation Priority',
        'helper' => 'Global priority when assigning. Lower value is higher priority. Minimum 1, maximum 9999.',
    ],
    'origin_slug' => [
        'label' => 'Origin Slug',
        'helper' => 'Full or partial airfield ICAO to match arrival aircraft against. This is used when doing a "any flights from these origin airports" allocation and does not override airline-specific rules.',
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
    'paired' => [
        'stand' => [
            'label' => 'Stand to Pair',
            'helper' => 'Only stands at the same airfield may be paired.'
        ],
        'add' => [
            'label' => 'Add Paired Stand',
        ],
        'detach' => [
            'label' => 'Unpair',
        ],
    ],
];
