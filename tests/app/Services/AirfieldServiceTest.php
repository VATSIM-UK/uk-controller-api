<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\SpeedGroup;
use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OutOfRangeException;

class AirfieldServiceTest extends BaseFunctionalTestCase
{
    private AirfieldService $service;

    /**
     * @var ControllerPosition
     */
    private $newController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(AirfieldService::class);
        $this->newController = ControllerPosition::create(
            [
                'callsign' => 'FOO_CTR',
                'frequency' => '199.998',
            ]
        );
    }

    public function testItReturnsAirfieldsWithControllers()
    {
        $expected = [
            [
                'id' => 1,
                'code' => 'EGLL',
                'transition_altitude' => 6000,
                'departure_wake_separation_scheme_id' => 1,
                'controllers' => [
                    1,
                    2,
                    3,
                ],
                'pairing-prenotes' => [
                    2 => [
                        1,
                    ],
                ],
            ],
            [
                'id' => 2,
                'code' => 'EGBB',
                'transition_altitude' => 6000,
                'departure_wake_separation_scheme_id' => 1,
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'transition_altitude' => 6000,
                'departure_wake_separation_scheme_id' => 1,
                'controllers' => [],
                'pairing-prenotes' => [],
            ],
        ];

        $actual = $this->service->getAllAirfieldsWithRelations();
        $this->assertEquals($expected, $actual);
    }

    public function testItInsertsIntoTopdownOrderBefore()
    {
        AirfieldService::insertIntoOrderBefore('EGLL', 'FOO_CTR', 'EGLL_S_TWR');

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => $this->newController->id,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 1,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsAnExceptionIfBeforePositionNotInTopdownOrder()
    {
        $this->expectException(OutOfRangeException::class);
        AirfieldService::insertIntoOrderBefore('EGLL', 'LON_C_CTR', 'LON_C_CTR');
    }

    public function testItInsertsIntoTopdownOrderAfter()
    {
        AirfieldService::insertIntoOrderAfter('EGLL', 'FOO_CTR', 'EGLL_S_TWR');

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => $this->newController->id,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsAnExceptionIfAfterPositionNotInTopdownOrder()
    {
        $this->expectException(OutOfRangeException::class);
        AirfieldService::insertIntoOrderAfter('EGLL', 'LON_C_CTR', 'LON_C_CTR');
    }

    public function testItDeletesFromTopdownOrder()
    {
        AirfieldService::insertIntoOrderBefore('EGLL', 'FOO_CTR', 'EGLL_S_TWR');
        AirfieldService::removeFromTopDownsOrder('EGLL', 'FOO_CTR');

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );

        $this->assertDatabaseMissing(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => $this->newController->id,
            ]
        );
    }

    public function testItThrowsAnExceptionIfDeletePositionNotInTopdownOrder()
    {
        $this->expectException(OutOfRangeException::class);
        AirfieldService::removeFromTopDownsOrder('EGLL', 'LON_C_CTR');
    }

    public function testItUpdatesAllTopDownsThatContainAPositionBefore()
    {
        AirfieldService::insertIntoOrderBefore('EGBB', 'EGLL_N_APP', 'LON_C_CTR');
        AirfieldService::updateAllTopDownsWithPosition('EGLL_N_APP', 'FOO_CTR', true);

        // TopDown one
        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => $this->newController->id,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );

        // Topdown two
        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => $this->newController->id,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => 4,
                'order' => 3,
            ]
        );
    }

    public function testItUpdatesAllTopDownsThatContainAPositionAfter()
    {
        AirfieldService::insertIntoOrderBefore('EGBB', 'EGLL_N_APP', 'LON_C_CTR');
        AirfieldService::updateAllTopDownsWithPosition('EGLL_N_APP', 'FOO_CTR', false);

        // TopDown one
        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => $this->newController->id,
                'order' => 3,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );

        // Topdown two
        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => $this->newController->id,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => 4,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesFromAllTopDowns()
    {
        AirfieldService::insertIntoOrderAfter('EGLL', 'FOO_CTR', 'LON_S_CTR');
        AirfieldService::insertIntoOrderAfter('EGBB', 'FOO_CTR', 'LON_C_CTR');
        AirfieldService::removePositionFromAllTopDowns('FOO_CTR');
        $this->assertDatabaseMissing(
            'top_downs',
            [
                'controller_position_id' => $this->newController->id,
            ]
        );

        // Top down one
        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );

        // Top down two
        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 2,
                'controller_position_id' => 4,
                'order' => 1
            ]
        );
    }

    public function testItAddsANewTopDownOrder()
    {
        AirfieldService::createNewTopDownOrder('EGKR', ['EGLL_S_TWR', 'EGLL_N_APP']);

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 3,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'top_downs',
            [
                'airfield_id' => 3,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
    }

    public function testItThrowsAnExceptionWhenCreatingNewTopDownIfPositionNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        AirfieldService::createNewTopDownOrder('EGKR', ['EGLL_S_TWR', 'EGLL_S_APP']);
    }

    public function testItThrowsAnExceptionWhenCreatingNewTopDownIfAirfieldNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        AirfieldService::createNewTopDownOrder('EGXY', ['EGLL_S_TWR']);
    }

    public function testItDeletesATopDownOrder()
    {
        AirfieldService::deleteTopDownOrder('EGLL');

        $this->assertDatabaseMissing(
            'top_downs',
            [
                'airfield_id' => 1,
            ]
        );
    }

    public function testItThrowsAnExceptionWhenDeletingTopDownIfAirfieldNotFound()
    {
        $this->expectException(ModelNotFoundException::class);
        AirfieldService::deleteTopDownOrder('EGXY');
    }

    public function testItReturnsAirfieldDependency()
    {
        Airfield::find(2)->update(['departure_wake_separation_scheme_id' => 2]);
        $speedGroup = SpeedGroup::create(
            [
                'airfield_id' => 1,
                'key' => 'SG1'
            ]
        );
        $speedGroup->engineTypes()->sync([2]);
        $speedGroup->aircraft()->sync([1]);

        $speedGroup2 = SpeedGroup::create(
            [
                'airfield_id' => 1,
                'key' => 'SG2'
            ]
        );

        $speedGroup2->engineTypes()->sync([1]);
        $speedGroup2->relatedGroups()->sync([$speedGroup->id => ['interval' => 120]]);

        $expected = [
            [
                'id' => 1,
                'identifier' => 'EGLL',
                'departure_wake_scheme' => 1,
                'departure_speed_groups' => [
                    [
                        'id' => $speedGroup->id,
                        'aircraft_types' => [
                            'B738',
                        ],
                        'engine_types' => [
                            'P'
                        ],
                        'next_group_penalty' => [],
                    ],
                    [
                        'id' => $speedGroup2->id,
                        'engine_types' => [
                            'J'
                        ],
                        'aircraft_types' => [],
                        'next_group_penalty' => [
                            $speedGroup->id => 120,
                        ],
                    ],
                ],
                'top_down_order' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                    'LON_S_CTR',
                ],
            ],
            [
                'id' => 2,
                'identifier' => 'EGBB',
                'departure_wake_scheme' => 2,
                'departure_speed_groups' => [],
                'top_down_order' => [
                    'LON_C_CTR',
                ],
            ],
            [
                'id' => 3,
                'identifier' => 'EGKR',
                'departure_wake_scheme' => 1,
                'departure_speed_groups' => [],
                'top_down_order' => [],
            ],
        ];

        $this->assertEquals($expected, $this->service->getAirfieldsDependency());
    }
}
