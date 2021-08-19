<?php

return [
    'enabled' => env('ENABLE_ACARS_MESSAGES', false),
    'hoppie' => [
        'url' => 'https://www.hoppie.nl/acars/system/connect.html',
        'login_code' => env('HOPPIE_ACARS_LOGIN_CODE'),
    ],
];
