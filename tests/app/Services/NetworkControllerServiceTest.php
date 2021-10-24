<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Vatsim\NetworkControllerPosition;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
        $this->dataService->shouldReceive('getNetworkControllerData')->once()->andReturn(new Collection());
        $this->service->updateNetworkData();
        $this->assertNull(NetworkControllerPosition::max('id'));
    }

    public function testItUpdatesControllersFromNetworkData()
    {
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
}
