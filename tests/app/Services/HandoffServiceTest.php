<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;
use OutOfRangeException;

class HandoffServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var HandoffService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(HandoffService::class);
    }

    public function testItReturnsHandoffV2Dependency()
    {
        DB::table('handoff_orders')->insert(
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 0,
            ]
        );
        $expected = [
            [
                'id' => 1,
                'controller_positions' => [
                    4,
                    1,
                    2,
                ],
            ],
            [
                'id' => 2,
                'controller_positions' => [
                    2,
                    3,
                ],
            ],
        ];

        $this->assertSame($expected, $this->service->getHandoffsV2Dependency());
    }

    public function testItCreatesANewHandoffOrderWithNoPositions()
    {
        $handoff = HandoffService::createNewHandoffOrder(
            'test',
            []
        );
        $this->assertDatabaseHas(
           'handoffs',
            [
                'id' => $handoff->id,
                'description' => 'test',
            ]
        );
    }

    public function testItCreatesANewHandoffOrderWithPositions()
    {
        $handoff = HandoffService::createNewHandoffOrder(
            'test',
            [
                'EGLL_S_TWR',
                'EGLL_N_APP',
            ]
        );

        $this->assertDatabaseHas(
           'handoffs',
            [
                'id' => $handoff->id,
                'description' => 'test',
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => $handoff->id,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => $handoff->id,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
    }

    public function testItSetsControllerPositionsForHandoffOrderByControllerId()
    {
        HandoffService::setPositionsForHandoffByControllerId(
            Handoff::findOrFail(1),
            [
                2,
                4,
                1,
                3,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 3,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );
    }

    public function testItSetsControllerPositionsForHandoffOrderByControllerCallsign()
    {
        HandoffService::setPositionsForHandoffByControllerCallsign(
            Handoff::findOrFail(1),
            [
                'EGLL_N_APP',
                'LON_C_CTR',
                'EGLL_S_TWR',
                'LON_S_CTR',
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 3,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );
    }

    public function testItSetsControllerPositionsForHandoffOrderByController()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 3,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 4,
            ]
        );
    }

    public function testItAddsAPositionToTheEndOfTheOrder()
    {
        HandoffService::insertPositionIntoHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(4)
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 3,
            ]
        );
    }

    public function testItAddsAPositionToOrderBeforeAnother()
    {
        HandoffService::insertPositionIntoHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(4),
            before: ControllerPosition::findOrFail(2)
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItAddsAPositionToOrderAfterAnother()
    {
        HandoffService::insertPositionIntoHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(4),
            after: ControllerPosition::findOrFail(1)
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsExceptionIfInsertingBeforePositionNotInOrder()
    {
        $this->expectException(InvalidArgumentException::class);
        HandoffService::insertPositionIntoHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(4),
            before: ControllerPosition::findOrFail(3)
        );
    }

    public function testItRemovesControllersFromAHandoffOrderByModel()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        HandoffService::removeFromHandoffOrder(Handoff::findOrFail(1), ControllerPosition::findOrFail(1));

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesControllersFromAHandoffOrderById()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        HandoffService::removeFromHandoffOrder(Handoff::findOrFail(1), 1);

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesControllersFromAHandoffOrderByCallsign()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        HandoffService::removeFromHandoffOrder(Handoff::findOrFail(1), 'EGLL_S_TWR');

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItUpdatesAllHandoffsWithPositionBefore()
    {
        HandoffService::updateAllHandoffsWithPosition(
            ControllerPosition::find(2),
            ControllerPosition::find(4),
            true
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 4,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItUpdatesAllHandoffsWithPositionAfter()
    {
        HandoffService::updateAllHandoffsWithPosition(
            ControllerPosition::find(2),
            ControllerPosition::find(4),
            false
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 3,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItUpdatesAllHandoffsWithPositionBeforeByCallsign()
    {
        HandoffService::updateAllHandoffsWithPosition(
            'EGLL_N_APP',
            'LON_C_CTR',
            true
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 4,
                'order' => 1,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesPositionsFromAllHandoffs()
    {
        HandoffService::removePositionFromAllHandoffs(ControllerPosition::find(2));

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 3,
                'order' => 1,
            ]
        );
    }

    public function testItRemovesPositionsFromAllHandoffsByCallsign()
    {
        HandoffService::removePositionFromAllHandoffs('EGLL_N_APP');

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 3,
                'order' => 1,
            ]
        );
    }

    public function testItMovesPositionUpAtStartOfOrder()
    {
        HandoffService::moveControllerInHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(1),
            true
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
    }

    public function testItMovesPositionUpIfOnlyPosition()
    {
        HandoffService::setPositionsForHandoffByController(Handoff::findOrFail(1), [ControllerPosition::findOrFail(1)]);
        HandoffService::moveControllerInHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(1),
            true
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
    }

    public function testItMovesPositionUpInTheOrder()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        HandoffService::moveControllerInHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(2),
            true
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItMovesPositionDownAtEndOfOrder()
    {
        HandoffService::moveControllerInHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(2),
            false
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );
    }

    public function testItMovesPositionDownIfOnlyPosition()
    {
        HandoffService::setPositionsForHandoffByController(Handoff::findOrFail(1), [ControllerPosition::findOrFail(1)]);
        HandoffService::moveControllerInHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(1),
            false
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );
    }

    public function testItMovesPositionDownInTheOrder()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        HandoffService::moveControllerInHandoffOrder(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(2),
            false
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItMovesPositionInOrderByIds()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        HandoffService::moveControllerInHandoffOrder(
            1,
            2,
            false
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 3,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
           'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsExceptionIfPositionToBeMovedIsNotInOrer()
    {
        HandoffService::setPositionsForHandoffByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        $this->expectException(InvalidArgumentException::class);
        HandoffService::moveControllerInHandoffOrder(
            1,
            4,
            false
        );
    }
}
