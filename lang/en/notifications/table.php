<?php

return [
    'columns' => [
        'title' => 'Title',
        'valid_from' => 'Valid From',
        'valid_to' => 'Valid To',
        'read' => 'Read',
    ],
    'controller_positions' => [
        'columns' => [
            'callsign' => 'Callsign',
            'frequency' => 'Frequency',
        ],
        'attach_action' => [
            'label' => 'Add Controller',
            'modal_heading' => 'Add Controller',
            'modal_button' => 'Add',
        ],
        'attach_form' => [
            'global' => [
                'label' => 'Global',
                'helper' => 'A global notification is applicable to all controllers.',
            ]
        ]
    ],
];
