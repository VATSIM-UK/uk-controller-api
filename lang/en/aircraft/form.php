<?php

return [
    'code' => [
        'label' => 'ICAO Code',
        'helper' => 'Must be unique.',
    ],
    'aerodrome_reference_code' => [
        'label' => 'Aerodrome Reference',
        'helper' => 'Used to determine which stands this aircraft type can use.'
    ],
    'wingspan' => [
        'label' => 'Wingspan (ft)',
    ],
    'length' => [
        'label' => 'Length (ft)',
    ],
    'allocate_stands' => [
        'label' => 'Stand allocation',
        'helper' => 'If enabled, stands will be allocated to this aircraft type on arrival.',
    ],
    'wake_categories' => [
        'category' => [
            'label' => 'Category',
        ]
    ]
];
