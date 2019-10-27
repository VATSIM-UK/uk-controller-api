<?php
namespace App\Services;

use App\Providers\RegionalPressureServiceProvider;

class RegionalPressureServiceProviderTest extends \App\BaseUnitTestCase
{
    public function testItConstructs()
    {
        $provider = new RegionalPressureServiceProvider($this->app);
        $this->assertInstanceOf(RegionalPressureServiceProvider::class, $provider);
    }

    public function testItProvidesTheService()
    {
        $provider = new RegionalPressureServiceProvider($this->app);
        $this->assertEquals([\App\Services\RegionalPressureService::class], $provider->provides());
    }
}
