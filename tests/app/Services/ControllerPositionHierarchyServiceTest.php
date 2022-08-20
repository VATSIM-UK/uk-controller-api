<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use InvalidArgumentException;

class ControllerPositionHierarchyServiceTest extends BaseFunctionalTestCase
{
    public function testItSetsControllerPositionsForHierarchyByControllerId()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerId(
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

    public function testItSetsControllerPositionsForHierarchyByControllerCallsign()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
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

    public function testItSetsControllerPositionsForHierarchyByController()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
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

    public function testItAddsAPositionToTheEndOfTheHierarchy()
    {
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
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

    public function testItAddsAPositionToHierarchyBeforeAnother()
    {
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
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

    public function testItAddsAPositionToHierarchyAfterAnother()
    {
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
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

    public function testItThrowsExceptionIfInsertingBeforePositionNotInHierarchy()
    {
        $this->expectException(InvalidArgumentException::class);
        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(4),
            before: ControllerPosition::findOrFail(3)
        );
    }

    public function testItRemovesControllersFromAHierarchyByModel()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        ControllerPositionHierarchyService::removeFromHierarchy(
            Handoff::findOrFail(1),
            ControllerPosition::findOrFail(1)
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
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesControllersFromAHierarchyById()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        ControllerPositionHierarchyService::removeFromHierarchy(Handoff::findOrFail(1), 1);

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

    public function testItRemovesControllersFromAHierarchyByCallsign()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(4),
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(3),
            ]
        );
        ControllerPositionHierarchyService::removeFromHierarchy(Handoff::findOrFail(1), 'EGLL_S_TWR');

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

    public function testItMovesPositionUpAtStartOfHierarchy()
    {
        ControllerPositionHierarchyService::moveControllerInHierarchy(
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
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [ControllerPosition::findOrFail(1)]
        );
        ControllerPositionHierarchyService::moveControllerInHierarchy(
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

    public function testItMovesPositionUpInTheHierarchy()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        ControllerPositionHierarchyService::moveControllerInHierarchy(
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

    public function testItMovesPositionDownAtEndOfHierarchy()
    {
        ControllerPositionHierarchyService::moveControllerInHierarchy(
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
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [ControllerPosition::findOrFail(1)]
        );
        ControllerPositionHierarchyService::moveControllerInHierarchy(
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

    public function testItMovesPositionDownInTheHierarchy()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        ControllerPositionHierarchyService::moveControllerInHierarchy(
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

    public function testItMovesPositionInHierarchyByIds()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        ControllerPositionHierarchyService::moveControllerInHierarchy(
            Handoff::findOrFail(1),
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

    public function testItThrowsExceptionIfPositionToBeMovedIsNotInHierarchy()
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByController(
            Handoff::findOrFail(1),
            [
                ControllerPosition::findOrFail(1),
                ControllerPosition::findOrFail(2),
                ControllerPosition::findOrFail(3),
            ]
        );
        $this->expectException(InvalidArgumentException::class);
        ControllerPositionHierarchyService::moveControllerInHierarchy(
            Handoff::findOrFail(1),
            4,
            false
        );
    }
}
