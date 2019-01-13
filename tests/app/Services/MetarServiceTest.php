<?php
namespace App\Services;

use App\BaseUnitTestCase;
use App\Exceptions\MetarException;

class MetarServiceTest extends BaseUnitTestCase
{
    /**
     * @var MetarService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(MetarService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(MetarService::class, $this->service);
    }

    public function testItThrowsAnExceptionIfNoQnh()
    {
        $metar = 'EGGD BKN100';
        $this->expectException(MetarException::class);
        $this->expectExceptionMessage('QNH not found in METAR: ' . $metar);

        $this->service->getQnhFromMetar($metar);
    }

    public function testItFindsAFourDigitQNH()
    {
        $metar = 'EGGD Q1001';
        $this->assertEquals(1001, $this->service->getQnhFromMetar($metar));
    }

    public function testItFindsAThreeDigitQNH()
    {
        $metar = 'EGGD Q0998';
        $this->assertEquals(998, $this->service->getQnhFromMetar($metar));
    }

    public function testItUsesTheFirstQNHPresent()
    {
        $metar = 'EGGD Q1029 Q1001';
        $this->assertEquals(1029, $this->service->getQnhFromMetar($metar));
    }
}
