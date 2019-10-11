<?php

namespace App\Services;

use App\Models\Controller\Handoff;

class HandoffService
{
    public function getAllHandoffsWithControllers() : array
    {
        $handoffs = Handoff::with(['controllers' => function ($query) {
            $query->orderBy('order');
        }])->get();

        return $handoffs->map(function (Handoff $handoff) {
            return array_merge(
                $handoff->toArray(),
                [
                    'controllers' => array_column($handoff->controllers->toArray(), 'id'),
                ]
            );
        })->toArray();
    }
}
