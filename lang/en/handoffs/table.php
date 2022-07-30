<?php

return [
    'columns' => [
        'description' => 'Description',
        'controllers' => 'Controllers',
    ],
    'controller_positions' => [
        'columns' => [
            'order' => [
                'label' => '#',
            ],
            'callsign' => [
                'label' => 'Callsign',
            ],
            'frequency' => [
                'label' => 'Frequency',
            ],
        ],
        'attach_form' => [
            'insert_after' => [
                'label' => 'Insert After',
                'helper' => 'Position in the order to insert after, if not specified, will be inserted on the end.',
            ],
        ],
        'move_up_action' => [
            'label' => 'Move Up',
        ],
        'move_down_action' => [
            'label' => 'Move Down',
        ],
        'attach_action' => [
            'label' => 'Add Controller',
            'modal_heading' => 'Add Controller To Handoff',
            'modal_button' => 'Add Controller',
        ],
        'detach_action' => [
            'label' => 'Remove',
        ],
    ],
];
