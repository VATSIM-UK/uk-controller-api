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
            'this option can be selected to designate the type of stand. It can also be used to designate stands as Cargo only. ' .
            'NOTE that you should only set this to "domestic" or "international" if you plan to do this for all stands at an airfield.',
    ],
    'latitude' => [
        'label' => 'Latitude',
        'helper' => 'The decimal latitude of the stand.',
    ],
    'longitude' => [
        'label' => 'Longitude',
        'helper' => 'The decimal longitude of the stand.',
    ],
    'aerodrome_reference_code' => [
        'label' => 'Maximum Aerodrome Reference Code',
        'helper' => 'Maximum aerodrome reference code that can be assigned to the stand.',
    ],
    'aircraft_length' => [
        'label' => 'Maximum Aircraft Length',
        'helper' => 'Maximum aircraft size that can be assigned to the stand. Overrides maximum aerodrome reference code.',
    ],
    'aircraft_wingspan' => [
        'label' => 'Maximum Aircraft Wingspan',
        'helper' => 'Maximum aircraft size that can be assigned to the stand. Overrides maximum aerodrome reference code.',
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
        'aircraft' => [
            'label' => 'Aircraft Type',
            'helper' => 'The aircraft type that this airline will use when using this stand.'
        ],
        'destination' => [
            'label' => 'Destination',
            'helper' => 'The destination used for this allocation. Can be partial matches, for example, "EGJ".',
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
                'Considered before general allocation priority. Minimum 1, maximum 9999.',
        ],
        'not_before' => [
            'label' => 'Do not allocate before (UTC)',
            'helper' => 'Will not allocate this stand automatically for arrivals before this time.',
        ],
        'remove' => [
            'label' => 'Remove',
        ],
    ],
    'paired' => [
        'stand' => [
            'label' => 'Stand to Pair',
            'helper' => 'Only stands at the same airfield may be paired.',
        ],
        'add' => [
            'label' => 'Add Paired Stand',
        ],
        'detach' => [
            'label' => 'Unpair',
        ],
    ],
    'user_preferences' => [
        'acars_heading' => [
            'label' => 'ACARS Settings',
        ],
        'acars' => [
            'label' => 'Send ACARS messages for arrival stands',
            'helper' => 'If this setting is turned on, then you will automatically be sent an ACARS message via Hoppie whenever an arrival stand is allocated for you. ' .
                'Messages will be sent only if the airfield is controlled by Ground or higher.',
        ],
        'acars_uncontrolled' => [
            'label' => 'Send ACARS messages for arrival stands at uncontrolled airfields',
            'helper' => 'If this setting is turned on, then ACARS messages for arrival stands will be sent at uncontrolled airfields.',
        ],
    ],
];
