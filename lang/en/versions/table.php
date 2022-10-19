<?php

return [
    'columns' => [
        'version' => 'Version',
        'release_channel' => 'Release Channel',
        'released_at' => 'Released At',
        'is_active' => 'Active Version',
    ],
    'delete_modal' => [
        'heading' => 'Delete Plugin Version',
        'sub_heading' => 'Are you sure you want to delete this plugin version? Doing so will prevent this version from being used by members. If this version is the latest version for the release channel, plugins will be updated back to the previous version.'
    ],
    'restore_modal' => [
        'heading' => 'Restore Plugin Version',
        'sub_heading' => 'Are you sure you want to restore this plugin version? Doing so will cause plugins to update to this version, if it is the latest for the release channel.'
    ],
];
