<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Aircraft\SpeedGroup;
use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use Illuminate\Support\Facades\DB;

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
                'elevation' => 1,
                'transition_altitude' => 6000,
                'wake_category_scheme_id' => 1,
                'standard_high' => 1,
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
                'handoff_id' => null,
            ],
            [
                'id' => 2,
                'code' => 'EGBB',
                'elevation' => 1,
                'transition_altitude' => 6000,
                'wake_category_scheme_id' => 1,
                'standard_high' => 0,
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
                'handoff_id' => null,
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'elevation' => 1,
                'transition_altitude' => 6000,
                'wake_category_scheme_id' => 1,
                'standard_high' => 1,
                'controllers' => [],
                'pairing-prenotes' => [],
                'handoff_id' => null,
            ],
        ];

        $actual = $this->service->getAllAirfieldsWithRelations();
        $this->assertEquals($expected, $actual);
    }

    public function testItUpdatesAllTopDownsThatContainAPositionBefore()
    {
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
            Airfield::find(2),
            ControllerPosition::find(2),
            before: ControllerPosition::find(4)
        );
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
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
            Airfield::find(2),
            ControllerPosition::find(2),
            before: ControllerPosition::find(4)
        );
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
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
            Airfield::find(1),
            $this->newController,
            after: ControllerPosition::find(3)
        );
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
            Airfield::find(2),
            $this->newController,
            after: ControllerPosition::find(4)
        );
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
        AirfieldService::createNewTopDownOrder(3, ['EGLL_S_TWR', 'EGLL_N_APP']);

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

    public function testItReturnsAirfieldDependency()
    {
        DB::table('speed_groups')->delete();
        Airfield::find(2)->update(['wake_category_scheme_id' => 2, 'handoff_id' => 1]);
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
        $speedGroup2->relatedGroups()->sync([$speedGroup->id => ['penalty' => 120]]);
        $speedGroup->relatedGroups()->sync([$speedGroup2->id => ['set_interval_to' => 1]]);

        $expected = [
            [
                'id' => 1,
                'identifier' => 'EGLL',
                'wake_scheme' => 1,
                'departure_speed_groups' => [
                    [
                        'id' => $speedGroup->id,
                        'aircraft_types' => [
                            'B738',
                        ],
                        'engine_types' => [
                            'P'
                        ],
                        'related_groups' => [
                            $speedGroup2->id => [
                                'following_interval_penalty' => null,
                                'set_following_interval_to' => 1,
                            ],
                        ],
                    ],
                    [
                        'id' => $speedGroup2->id,
                        'engine_types' => [
                            'J'
                        ],
                        'aircraft_types' => [],
                        'related_groups' => [
                            $speedGroup->id => [
                                'following_interval_penalty' => 120,
                                'set_following_interval_to' => null,
                            ],
                        ],
                    ],
                ],
                'top_down_controller_positions' => [
                    1,
                    2,
                    3,
                ],
                'pairing_prenotes' => [
                    [
                        'airfield_id' => 2,
                        'flight_rule_id' => 1,
                        'prenote_id' => 1,
                    ]
                ],
                'handoff_id' => null,
            ],
            [
                'id' => 2,
                'identifier' => 'EGBB',
                'wake_scheme' => 2,
                'departure_speed_groups' => [],
                'top_down_controller_positions' => [
                    4,
                ],
                'pairing_prenotes' => [],
                'handoff_id' => 1,
            ],
            [
                'id' => 3,
                'identifier' => 'EGKR',
                'wake_scheme' => 1,
                'departure_speed_groups' => [],
                'top_down_controller_positions' => [],
                'pairing_prenotes' => [],
                'handoff_id' => null,
            ],
        ];

        $this->assertEquals($expected, $this->service->getAirfieldsDependency());
    }

    public function testItReturnsThatAControllerIsInTopDownOrder()
    {
        $this->assertTrue($this->service::controllerIsInTopDownOrder(ControllerPosition::find(3), 'EGLL'));
    }

    public function testItReturnsThatAControllerIsNotInTopDownOrder()
    {
        $this->assertFalse($this->service::controllerIsInTopDownOrder(ControllerPosition::find(4), 'EGLL'));
    }
}
