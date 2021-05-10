<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\MinStacksUpdatedEvent;
use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use App\Models\MinStack\MslAirfield;
use App\Models\MinStack\MslTma;
use App\Models\Tma;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class MinStackLevelServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var MinStackLevelService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(MinStackLevelService::class);
        Event::fake();
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(MinStackLevelService::class, $this->service);
    }

    public function testItReturnsAirfieldMinStacks()
    {
        $this->assertEquals(7000, $this->service->getMinStackLevelForAirfield("EGLL"));
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
        $this->assertEquals(6000, $this->service->getMinStackLevelForTma("MTMA"));
    }

    public function testItReturnsNullMinStackTmaNotFound()
    {
        $this->assertNull($this->service->getMinStackLevelForTma("STMA"));
    }

    public function testItReturnsNullMinStackTmaHasNoMinStack()
    {
        $this->assertNull($this->service->getMinStackLevelForTma("LTMA"));
    }

    public function testItReturnsAllAirfieldMinStackLevels()
    {
        $this->assertEquals(['EGLL' => 7000], $this->service->getAllAirfieldMinStackLevels());
    }

    public function testItReturnsAllTmaMinStackLevels()
    {
        $this->assertEquals(['MTMA' => 6000], $this->service->getAllTmaMinStackLevels());
    }

    public function testItAddsAirfieldMslsFromMetars()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        Tma::where('msl_airfield_id', 2)->update(['msl_airfield_id' => 3]);

        $metars = collect([new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]])]);
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGBB' => 7000] && $event->tma === [];
            }
        );
    }

    public function testItAddsTmaMslsIfAirfieldUpdated()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();

        $metars = collect([new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]])]);
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 1);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );
        $this->assertDatabaseHas(
            'msl_tma',
            [
                'tma_id' => 2,
                'msl' => 7000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGBB' => 7000] && $event->tma === ['MTMA' => 7000];
            }
        );
    }

    public function testItUpdatesAirfieldMslsFromMetars()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        Tma::where('msl_airfield_id', 2)->update(['msl_airfield_id' => 3]);
        MslAirfield::create(['airfield_id' => 2, 'msl' => 8000]);

        $metars = collect([new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]])]);
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGBB' => 7000] && $event->tma === [];
            }
        );
    }

    public function testItUpdatesTmaMslsIfAirfieldUpdated()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        MslAirfield::create(['airfield_id' => 2, 'msl' => 8000]);
        MslTma::create(['tma_id' => 2, 'msl' => 8000]);

        $metars = collect([new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]])]);
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 1);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );
        $this->assertDatabaseHas(
            'msl_tma',
            [
                'tma_id' => 2,
                'msl' => 7000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGBB' => 7000] && $event->tma === ['MTMA' => 7000];
            }
        );
    }

    public function testItDoesntUpdateMslsIfNothingChanged()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        MslAirfield::create(['airfield_id' => 2, 'msl' => 7000]);

        $metars = collect([new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]])]);
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );
        Event::assertNotDispatched(MinStacksUpdatedEvent::class);
    }

    public function testItDoesntUpdateTmaMslIfControllingAirfieldNotChanged()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        MslAirfield::create(['airfield_id' => 2, 'msl' => 7000]);
        MslAirfield::create(['airfield_id' => 3, 'msl' => 8000]);
        Airfield::find(3)->mslCalculationAirfields()->sync([3]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]]),
                new Metar(['airfield_id' => 3, 'parsed' => ['qnh' => 1014]]),
            ]
        );
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 2);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 3,
                'msl' => 7000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGKR' => 7000] && $event->tma === [];
            }
        );
    }

    public function testItDoesntUpdateAirfieldMslIfThereIsntACalculationAvailable()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        MslAirfield::create(['airfield_id' => 2, 'msl' => 7000]);
        MslAirfield::create(['airfield_id' => 3, 'msl' => 8000]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]]),
                new Metar(['airfield_id' => 3, 'parsed' => ['qnh' => 1014]]),
            ]
        );
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 2);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 3,
                'msl' => 8000,
            ]
        );

        Event::assertNotDispatched(MinStacksUpdatedEvent::class);
    }

    public function testItUsesLowestApplicableQnh()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        Tma::where('msl_airfield_id', 2)->update(['msl_airfield_id' => 3]);
        Airfield::find(2)->mslCalculationAirfields()->sync([2, 3]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]]),
                new Metar(['airfield_id' => 3, 'parsed' => ['qnh' => 1012]]),
            ]
        );
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 8000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGBB' => 8000] && $event->tma === [];
            }
        );
    }

    public function testItIgnoresNullQnhInMetars()
    {
        // Tidy up tables first
        DB::table('msl_tma')->delete();
        DB::table('msl_airfield')->delete();
        Tma::where('msl_airfield_id', 2)->update(['msl_airfield_id' => 3]);
        Airfield::find(2)->mslCalculationAirfields([2, 3]);

        $metars = collect(
            [
                new Metar(['airfield_id' => 2, 'parsed' => ['qnh' => 1014]]),
                new Metar(['airfield_id' => 3, 'parsed' => ['qnh' => null]]),
            ]
        );
        $this->service->updateMinimumStackLevelsFromMetars($metars);

        $this->assertDatabaseCount('msl_tma', 0);
        $this->assertDatabaseCount('msl_airfield', 1);
        $this->assertDatabaseHas(
            'msl_airfield',
            [
                'airfield_id' => 2,
                'msl' => 7000,
            ]
        );

        Event::assertDispatched(
            MinStacksUpdatedEvent::class,
            function ($event) {
                return $event->airfield === ['EGBB' => 7000] && $event->tma === [];
            }
        );
    }
}
