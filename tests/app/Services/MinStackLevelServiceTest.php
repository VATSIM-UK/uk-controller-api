<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\MinStack\MslAirfield;
use App\Models\MinStack\MslTma;

class MinStackLevelServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var MinStackLevelService
     */
    private $service;

    public function setUp() : void
    {
        parent::setUp();
        $this->service = $this->app->make(MinStackLevelService::class);
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(MinStackLevelService::class, $this->service);
    }

    public function testItReturnsAirfieldMinStacks()
    {
        $this->assertEquals(
            MslAirfield::where('airfield_id', 1)->first(),
            $this->service->getMinStackLevelForAirfield("EGLL")
        );
    }

    public function testItReturnsNullMinStackAirfieldNotFound()
    {
        $this->assertNull($this->service->getMinStackLevelForAirfield("EGXY"));
    }

    public function testItReturnsNullMinStackAirfieldHasNoMinStack()
    {
        $this->assertNull($this->service->getMinStackLevelForAirfield("EGBB"));
    }

    public function testItReturnsTmaMinStacks()
    {
        $this->assertEquals(MslTma::where('tma_id', 2)->first(), $this->service->getMinStackLevelForTma("MTMA"));
    }

    public function testItReturnsNullMinStackTmaNotFound()
    {
        $this->assertNull($this->service->getMinStackLevelForTma("STMA"));
    }

    public function testItReturnsNullMinStackTmaHasNoMinStack()
    {
        $this->assertNull($this->service->getMinStackLevelForTma("LTMA"));
    }
}
