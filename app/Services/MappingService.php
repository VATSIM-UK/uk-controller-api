<?php

namespace App\Services;

use App\Helpers\Airfield\MappingElementProvider;

class MappingService
{
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
                fn(MappingElementProvider $provider) => $provider->mappingElements(),
                $this->elementProviders,
            )
        )->flatten(1)->toArray();
    }
}
