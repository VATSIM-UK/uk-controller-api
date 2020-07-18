<?php

namespace App\Http\Controllers;

class WebInterface extends BaseController
{
    public function __invoke()
    {
        return view('web');
    }
}
