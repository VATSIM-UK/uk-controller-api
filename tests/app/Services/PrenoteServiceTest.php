<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use InvalidArgumentException;

class PrenoteServiceTest extends BaseFunctionalTestCase
{
    private readonly PrenoteService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(PrenoteService::class);
    }

    public function testItReturnsPrenotesV2Dependency()
    {
        $expected = [
            [
                'id' => 1,
                'description' => 'Prenote One',
                'controller_positions' => [
                    1,
                    2,
                ],
            ],
            [
                'id' => 2,
                'description' => 'Prenote Two',
                'controller_positions' => [
                    2,
                    3,
                ],
            ],
        ];

        $this->assertEquals($expected, $this->service->getPrenotesV2Dependency());
    }

    public function testItSetsPositionsForPrenote()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 3,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );
    }

    public function testItSetsPositionsForPrenoteById()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                2,
                4,
                1,
                3,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 3,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );
    }

    public function testItSetsPositionsForPrenoteByCallsign()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                'EGLL_N_APP',
                'LON_C_CTR',
                'EGLL_S_TWR',
                'LON_S_CTR',
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 3,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );
    }

    public function testItAddsAPositionToTheEndOfTheOrder()
    {
        PrenoteService::insertPositionIntoPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(4)
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 3,
            ]
        );
    }

    public function testItAddsAPositionToOrderBeforeAnother()
    {
        PrenoteService::insertPositionIntoPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(4),
            before: ControllerPosition::findOrFail(2)
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItAddsAPositionToOrderAfterAnother()
    {
        PrenoteService::insertPositionIntoPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(4),
            after: ControllerPosition::findOrFail(1)
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsExceptionIfInsertingBeforePositionNotInOrder()
    {
        $this->expectException(InvalidArgumentException::class);
        PrenoteService::insertPositionIntoPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(4),
            before: ControllerPosition::findOrFail(3)
        );
    }

    public function testItRemovesControllersFromAPrenoteOrderByModel()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        PrenoteService::removeFromPrenoteOrder(Prenote::findOrFail(1), ControllerPosition::findOrFail(1));

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesControllersFromAPrenoteOrderById()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        PrenoteService::removeFromPrenoteOrder(Prenote::findOrFail(1), 1);

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesControllersFromAPrenoteOrderByCallsign()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        PrenoteService::removeFromPrenoteOrder(Prenote::findOrFail(1), 'EGLL_S_TWR');

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItMovesPositionUpAtStartOfOrder()
    {
        PrenoteService::moveControllerInPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(1),
            true
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
    }

    public function testItMovesPositionUpIfOnlyPosition()
    {
        PrenoteService::setPositionsForPrenote(Prenote::findOrFail(1), [ControllerPosition::findOrFail(1)]);
        PrenoteService::moveControllerInPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(1),
            true
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
    }

    public function testItMovesPositionUpInTheOrder()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        PrenoteService::moveControllerInPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(2),
            true
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItMovesPositionDownAtEndOfOrder()
    {
        PrenoteService::moveControllerInPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(2),
            false
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
    }

    public function testItMovesPositionDownIfOnlyPosition()
    {
        PrenoteService::setPositionsForPrenote(Prenote::findOrFail(1), [ControllerPosition::findOrFail(1)]);
        PrenoteService::moveControllerInPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(1),
            false
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
    }

    public function testItMovesPositionDownInTheOrder()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        PrenoteService::moveControllerInPrenoteOrder(
            Prenote::findOrFail(1),
            ControllerPosition::findOrFail(2),
            false
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItMovesPositionInOrderByIds()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        PrenoteService::moveControllerInPrenoteOrder(
            1,
            2,
            false
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 3,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsExceptionIfPositionToBeMovedIsNotInOrer()
    {
        PrenoteService::setPositionsForPrenote(
            Prenote::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        $this->expectException(InvalidArgumentException::class);
        PrenoteService::moveControllerInPrenoteOrder(
            1,
            4,
            false
        );
    }
}
