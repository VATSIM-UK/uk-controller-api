<?php

namespace App\Services;

use App\Models\Controller\Handoff;

class HandoffService
{
    public function getAllHandoffsWithControllers() : array
    {
        $handoffs = Handoff::all();
        $handoffArray = [];

        $handoffs->each(function (Handoff $handoff) use (&$handoffArray) {
            $handoffArray[] = array_merge(
                $handoff->toArray(),
                [
                    'controllers' =>
                        $handoff->controllers()->orderBy('order')->pluck('controller_position_id')->toArray()
                ]
            );
        });

        return $handoffArray;
    }
}
