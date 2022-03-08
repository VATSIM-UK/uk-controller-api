<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Helpers\Airfield\MappingElementProvider;
use App\Models\Airfield\VisualReferencePoint;

class MappingServiceTest extends BaseFunctionalTestCase
{
    private MappingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MappingService::class);
    }

    public function testItHasProviders()
    {
        $this->assertEquals(
            [
                VrpService::class,
            ],
            array_map(
                fn(MappingElementProvider $provider) => get_class($provider),
                $this->service->providers()
            )
        );
    }

    public function testItReturnsMappingElements()
    {
        $this->assertEquals(
            VisualReferencePoint::all()->toArray(),
            $this->service->getMappingElementsDependency()
        );
    }
}
