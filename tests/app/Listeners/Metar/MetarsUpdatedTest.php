<?php

namespace App\Listeners\Metar;

use App\BaseUnitTestCase;
use App\Events\MetarsUpdatedEvent;
use App\Jobs\AltimeterSettingRegion\UpdateRegionalPressureSettings;
use App\Jobs\MinStack\UpdateMinimumStackLevels;
use App\Models\Metars\Metar;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Collection;

class MetarsUpdatedTest extends BaseUnitTestCase
{
    private Collection $metars;
    private MetarsUpdated $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->metars = collect([new Metar(['airfield_id' => 1, 'qnh' => 1014])]);
        $this->handler = $this->app->make(MetarsUpdated::class);
        Bus::fake();
    }

    public function testItDispatchesMinStackUpdateJob()
    {
        $this->handler->handle(new MetarsUpdatedEvent($this->metars));
        Bus::assertDispatched(UpdateMinimumStackLevels::class);
    }

    public function testItDispatchesRegionalPressureUpdateJob()
    {
        $this->handler->handle(new MetarsUpdatedEvent($this->metars));
        Bus::assertDispatched(UpdateRegionalPressureSettings::class);
    }
}
