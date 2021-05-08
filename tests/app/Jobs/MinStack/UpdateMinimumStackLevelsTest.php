<?php

namespace App\Jobs\MinStack;

use App\BaseUnitTestCase;
use App\Models\Metars\Metar;
use App\Services\MinStackLevelService;
use Illuminate\Support\Collection;
use Mockery;

class UpdateMinimumStackLevelsTest extends BaseUnitTestCase
{
    private UpdateMinimumStackLevels $update;
    private Collection $metars;

    public function setUp(): void
    {
        parent::setUp();
        $this->metars = collect([new Metar(['airfield_id' => 1, 'qnh' => 1014])]);
        $this->update = new UpdateMinimumStackLevels($this->metars);
    }

    public function testHandleCallsStandService()
    {
        $minStackService = Mockery::mock(MinStackLevelService::class);
        $minStackService->expects('updateMinimumStackLevelsFromMetars')->with($this->metars)->once();
        $this->update->handle($minStackService);
    }
}
