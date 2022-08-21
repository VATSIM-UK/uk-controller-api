<?php

namespace App\Services;

use App\Models\Controller\Prenote;

class PrenoteService
{
    public function getPrenotesV2Dependency(): array
    {
        return Prenote::all()->map(function (Prenote $prenote) {
            return [
                'id' => $prenote->id,
                'description' => $prenote->description,
                'controller_positions' => $prenote->controllers()
                    ->orderBy('order')
                    ->pluck('controller_positions.id')
                    ->toArray(),
            ];
        })->toArray();
    }
}
