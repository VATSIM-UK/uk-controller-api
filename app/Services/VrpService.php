<?php

namespace App\Services;

use App\Helpers\Airfield\MappingElementProvider;
use App\Models\Airfield\VisualReferencePoint;
use Illuminate\Support\Collection;

class VrpService implements MappingElementProvider
{
    public function getVrpDependency(): array
    {
        return VisualReferencePoint::all('id', 'name', 'short_name', 'latitude', 'longitude')->map(
            fn (VisualReferencePoint $visualReferencePoint) => array_merge(
                $visualReferencePoint->toArray(),
                ['airfields' => $visualReferencePoint->airfields->pluck('id')->toArray()]
            )
        )->toArray();
    }

    public function mappingElements(): Collection
    {
        return VisualReferencePoint::all();
    }
}
