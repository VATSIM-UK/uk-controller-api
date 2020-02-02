<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use OutOfRangeException;

class AirfieldServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var AirfieldService
     */
    private $service;

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
                'controllers' => [
                    4,
                ],
                'pairing-prenotes' => [],
            ],
            [
                'id' => 3,
                'code' => 'EGKR',
                'transition_altitude' => 6000,
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
}
