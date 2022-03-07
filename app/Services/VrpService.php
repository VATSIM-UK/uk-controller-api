<?php

namespace App\Services;

use App\Models\Airfield\VisualReferencePoint;

class VrpService
{
    public function getVrpDependency(): array
    {
        return VisualReferencePoint::all('id', 'name', 'short_name', 'latitude', 'longitude')->map(
            fn(VisualReferencePoint $visualReferencePoint) => array_merge(
                $visualReferencePoint->toArray(),
                ['airfields' => $visualReferencePoint->airfields->pluck('id')->toArray()]
            )
        )->toArray();
    }
}
