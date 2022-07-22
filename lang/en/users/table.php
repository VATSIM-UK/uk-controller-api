<?php

return [
    'columns' => [
        'id' => 'CID',
        'name' => 'Name',
        'status' => 'Status',
    ],
    'roles' => [
        'description' => 'Assign roles to a user. You may not assign roles to yourself.',
        'attach_action' => [
            'trigger_button' => 'Assign Role',
            'confirm_button' => 'Assign',
            'modal_heading' => 'Assign role',
        ],
        'detach_action' => [
            'trigger_button' => 'Unassign Role',
            'confirm_button' => 'Unassign',
            'modal_heading' => 'Unassign role :role',
        ],
    ],
];
