<?php

namespace App\Services;

use App\Models\Controller\Handoff;

class HandoffService
{
    public function getAllHandoffsWithControllers() : array
    {
        return Handoff::with('controllers')->get()->map(function (Handoff $handoff) {
            return array_merge(
                $handoff->toArray(),
                [
                    'controllers' => $handoff->controllers()->orderBy('order')->pluck('controller_position_id')->toArray()
                ]
            );
        })->toArray();
    }
}
