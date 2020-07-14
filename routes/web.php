<?php

use Illuminate\Support\Facades\Route;

Route::get('/{path?}', [
    'uses' => 'WebInterface',
    'as' => 'react',
    'where' => ['path' => '.*']
]);
