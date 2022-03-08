<?php

namespace App\Helpers\Airfield;

use App\Models\Mapping\MappingElement;

interface MappingElementProvider
{
    /**
     * Returns an array of mapping elements.
     *
     * @return MappingElement[]
     */
    public function mappingElements(): array;
}
