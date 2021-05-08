<?php

namespace App\Jobs\AltimeterSettingRegion;

use App\BaseUnitTestCase;
use App\Models\Metars\Metar;
use App\Services\RegionalPressureService;
use Illuminate\Support\Collection;
use Mockery;

class UpdateRegionalPressureSettingsTest extends BaseUnitTestCase
{
    private UpdateRegionalPressureSettings $update;
    private Collection $metars;

    public function setUp(): void
    {
        parent::setUp();
        $this->metars = collect([new Metar(['airfield_id' => 1, 'qnh' => 1014])]);
        $this->update = new UpdateRegionalPressureSettings($this->metars);
    }

    public function testHandleCallsStandService()
    {
        $regionalPressureService = Mockery::mock(RegionalPressureService::class);
        $regionalPressureService->expects('updateRegionalPressuresFromMetars')->with($this->metars)->once();
        $this->update->handle($regionalPressureService);
    }
}
