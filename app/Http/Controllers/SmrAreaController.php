<?php

namespace App\Http\Controllers;

use App\Models\SmrArea;
use Illuminate\Http\Response;

class SmrAreaController extends Controller
{
    public function getFormattedSmrAreas(): Response
    {
        return response(
            // backwards-compatible format for vSMR: sline-format coordinates on
            // each line, with polygons separated by at least one blank line
            SmrArea::active()->pluck("coordinates")->join("\n\n"),
            200,
            ["Content-Type" => "text/plain"],
        );
    }
}
