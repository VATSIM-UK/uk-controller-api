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
    ]
];
