<?php

namespace App\Helpers\Airfield;

use App\Models\Mapping\MappingElement;
use Illuminate\Support\Collection;

interface MappingElementProvider
{
    /**
     * Returns an array of mapping elements.
     *
     * @return MappingElement[] | Collection
     */
    public function mappingElements(): Collection;
}
