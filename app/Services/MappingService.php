<?php

namespace App\Services;

use App\Helpers\Airfield\MappingElementProvider;
use App\Models\Mapping\MappingElement;

class MappingService
{
    /**
     * @var MappingElementProvider[]
     */
    private array $elementProviders;

    public function __construct(array $elementProviders)
    {
        $this->elementProviders = $elementProviders;
    }

    public function providers(): array
    {
        return $this->elementProviders;
    }

    public function getMappingElementsDependency(): array
    {
        return collect(
            array_map(
                fn(MappingElementProvider $provider) => $provider->mappingElements()->map(
                    fn(MappingElement $element) => [
                        'id' => $element->elementId(),
                        'name' => $element->elementName(),
                        'type' => $element->elementType(),
                        'latitude' => $element->elementCoordinate()->getLat(),
                        'longitude' => $element->elementCoordinate()->getLng(),
                    ]
                ),
                $this->elementProviders,
            )
        )->flatten(1)->toArray();
    }
}
