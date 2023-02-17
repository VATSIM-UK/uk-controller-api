<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Events\NetworkControllersUpdatedEvent;
use App\Models\Controller\ControllerPosition;
use App\Models\Vatsim\NetworkControllerPosition;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Mockery;

class NetworkControllerServiceTest extends BaseFunctionalTestCase
{
    private NetworkDataService $dataService;
    private NetworkControllerService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataService = Mockery::mock(NetworkDataService::class);
        $this->app->instance(NetworkDataService::class, $this->dataService);
        $this->service = $this->app->make(NetworkControllerService::class);
    }

    public function testItHandlesNoControllersOnNetwork()
    {
        Event::fake();
        $this->dataService->shouldReceive('getNetworkControllerData')->once()->andReturn(new Collection());
        $this->service->updateNetworkData();
        $this->assertNull(NetworkControllerPosition::max('id'));
    }

    public function testItUpdatesControllersFromNetworkData()
    {
        Event::fake();
        $position = NetworkControllerPosition::create(
            ['callsign' => 'EGLL_S_TWR', 'cid' => self::ACTIVE_USER_CID, 'frequency' => 118.5]
        );


        $this->dataService->shouldReceive('getNetworkControllerData')
            ->once()
            ->andReturn(
                collect([
                    [
                        'cid' => self::ACTIVE_USER_CID,
                        'callsign' => 'EGLL_N_TWR',
                        'frequency' => 118.7,
                    ],
                    [
                        'cid' => self::BANNED_USER_CID,
                        'callsign' => 'EGKK_APP',
                        'frequency' => 126.820,
                    ]
                ])
            );

        $this->service->updateNetworkData();

        $this->assertDatabaseCount('network_controller_positions', 2);
        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $position->id,
                'callsign' => 'EGLL_N_TWR',
                'frequency' => 118.7,
                'cid' => self::ACTIVE_USER_CID,
            ]
        );
        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'callsign' => 'EGKK_APP',
                'frequency' => 126.820,
                'cid' => self::BANNED_USER_CID,
            ]
        );
    }

    public function testItTimesOutStaleControllers()
    {
        Event::fake();
        // Has "timed out" but is now in the data, so keep
        $positionToKeep = NetworkControllerPosition::create(
            ['callsign' => 'EGLL_S_TWR', 'cid' => self::ACTIVE_USER_CID, 'frequency' => 118.5]
        );
        $positionToKeep->updated_at = Carbon::now()->subMinutes(5);
        $positionToKeep->save();

        // Not in data, but recent
        $positionToKeep2 = NetworkControllerPosition::create(
            ['callsign' => 'EGKK_APP', 'cid' => self::DISABLED_USER_CID, 'frequency' => 126.820]
        );

        // Too old and not in data
        $positionToLose = NetworkControllerPosition::create(
            ['callsign' => 'EGLL_N_TWR', 'cid' => self::BANNED_USER_CID, 'frequency' => 118.7]
        );
        $positionToLose->updated_at = Carbon::now()->subMinutes(4);
        $positionToLose->save();

        $this->dataService->shouldReceive('getNetworkControllerData')
            ->once()
            ->andReturn(
                collect([
                    [
                        'cid' => self::ACTIVE_USER_CID,
                        'callsign' => 'EGLL_S_TWR',
                        'frequency' => 118.5,
                    ],
                ])
            );

        $this->service->updateNetworkData();

        $this->assertDatabaseCount('network_controller_positions', 2);
        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $positionToKeep->id,
            ]
        );
        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $positionToKeep2->id,
            ]
        );
        $this->assertDatabaseMissing(
            'network_controller_positions',
            [
                'id' => $positionToLose->id,
            ]
        );
    }

    public function testItFiresEventOnUpdate()
    {
        Event::assertDispatched(NetworkControllersUpdatedEvent::class);
        $this->dataService->shouldReceive('getNetworkControllerData')->once()->andReturn(new Collection());
        $this->service->updateNetworkData();
    }

    public function testItMatchesControllerPositions()
    {
        ControllerPosition::create(['callsign' => 'FOO_TWR', 'frequency' => 123.456]);

        // Should be unmapped, we don't know who this is
        $pos1 = NetworkControllerPosition::create(
            ['cid' => 1, 'callsign' => 'FOO_CTR', 'frequency' => 111.111, 'controller_position_id' => 1]
        );

        // Should not be mapped
        $pos2 = NetworkControllerPosition::create(
            ['cid' => 2, 'callsign' => 'BAR_CTR', 'frequency' => 222.222]
        );

        // Should be unmapped, it's completely invalid
        $pos3 = NetworkControllerPosition::create(
            ['cid' => 3, 'callsign' => 'FOOOO', 'frequency' => 333.333, 'controller_position_id' => 2]
        );

        // Should be mapped
        $pos4 = NetworkControllerPosition::create(
            ['cid' => 4, 'callsign' => 'EGLL_S_TWR', 'frequency' => 118.500]
        );

        // Should not be mapped, wrong frequency for type of unit
        $pos5 = NetworkControllerPosition::create(
            ['cid' => 5, 'callsign' => 'EGLL_S_TWR', 'frequency' => 119.720]
        );

        // Should not be mapped, matches wrong unit
        $pos6 = NetworkControllerPosition::create(
            ['cid' => 6, 'callsign' => 'EGLL_S_TWR', 'frequency' => 123.456]
        );

        $this->service->updatedMatchedControllerPositions();

        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $pos1->id,
                'controller_position_id' => null,
            ]
        );

        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $pos2->id,
                'controller_position_id' => null,
            ]
        );

        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $pos3->id,
                'controller_position_id' => null,
            ]
        );

        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $pos4->id,
                'controller_position_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $pos5->id,
                'controller_position_id' => null,
            ]
        );

        $this->assertDatabaseHas(
            'network_controller_positions',
            [
                'id' => $pos6->id,
                'controller_position_id' => null,
            ]
        );
    }
}
