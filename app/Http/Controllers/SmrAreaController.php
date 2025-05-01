<?php

namespace App\Http\Controllers;

use App\Models\SmrArea;
use Illuminate\Http\Response;

class SmrAreaController extends Controller
{
    public function getFormattedSmrAreas(): Response
    {
        return response(
            SmrArea::active()->pluck("coordinates")->join("\n\n"),
            200,
            ["Content-Type" => "text/plain"],
        );
    }
}
