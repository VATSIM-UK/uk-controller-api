<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Helpers\Airfield\MappingElementProvider;

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
            [
                [
                    'id' => 1,
                    'type' => 'visual_reference_point',
                    'name' => 'VRP One',
                    'latitude' => 1,
                    'longitude' => 2,
                ],
                [
                    'id' => 2,
                    'type' => 'visual_reference_point',
                    'name' => 'VRP Two',
                    'latitude' => 3,
                    'longitude' => 4,
                ],
            ],
            $this->service->getMappingElementsDependency()
        );
    }
}
