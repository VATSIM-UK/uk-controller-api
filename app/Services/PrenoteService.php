<?php

namespace App\Services;

use App\Models\Controller\Prenote;

class PrenoteService
{
    public function getAllPrenotesWithControllers() : array
    {
        $prenotes = [];

        Prenote::all()->each(function (Prenote $prenote) use (&$prenotes) {
            $prenotes[] = array_merge(
                $prenote->toArray(),
                [
                    'controllers' =>
                        $prenote->controllers()->orderBy('order')->pluck('controller_position_id')->toArray()
                ]
            );
        });

        return $prenotes;
    }
}
