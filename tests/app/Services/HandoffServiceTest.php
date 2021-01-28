<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
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

    public function testItReturnsHandoffsWithControllers()
    {
        $expected = [
            'HANDOFF_ORDER_1' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
            ],
            'HANDOFF_ORDER_2' => [
                'EGLL_N_APP',
                'LON_S_CTR',
            ],
        ];

        $actual = $this->service->getAllHandoffsWithControllers();
        $this->assertSame($expected, $actual);
    }

    public function testItMapsSidsToHandoffs()
    {
        $expected = [
            'EGLL' => [
                'TEST1X' => 'HANDOFF_ORDER_1',
                'TEST1Y' => 'HANDOFF_ORDER_1',
            ],
            'EGBB' => [
                'TEST1A' => 'HANDOFF_ORDER_2',
            ],
        ];

        $actual = $this->service->mapSidsToHandoffs();
        $this->assertSame($expected, $actual);
    }

    public function testItAddsANewHandoffOrder()
    {
        HandoffService::createNewHandoffOrder('NEW_HANDOFF_ORDER', 'New!', ['EGLL_N_APP', 'LON_S_CTR']);

        $this->assertDatabaseHas(
            'handoffs',
            [
                'key' => 'NEW_HANDOFF_ORDER',
                'description' => 'New!',
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => 3,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => 3,
                'controller_position_id' => 3,
                'order' => 2,
            ]
        );
    }

    public function testItInsertsIntoHandoffOrderBefore()
    {
        HandoffService::insertIntoOrderBefore('HANDOFF_ORDER_1', 'LON_C_CTR', 'EGLL_S_TWR');

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
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
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsAnExceptionIfBeforePositionNotInHandoffOrder()
    {
        $this->expectException(OutOfRangeException::class);
        HandoffService::insertIntoOrderBefore('HANDOFF_ORDER_1', 'LON_C_CTR', 'LON_S_CTR');
    }

    public function testItInsertsIntoHandoffOrderAfter()
    {
        HandoffService::insertIntoOrderAfter('HANDOFF_ORDER_1', 'LON_C_CTR', 'EGLL_S_TWR');

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

    public function testItThrowsAnExceptionIfAfterPositionNotInHandoffOrder()
    {
        $this->expectException(OutOfRangeException::class);
        HandoffService::insertIntoOrderAfter('HANDOFF_ORDER_1', 'LON_C_CTR', 'LON_S_CTR');
    }

    public function testItDeletesFromHandoffOrder()
    {
        HandoffService::insertIntoOrderBefore('HANDOFF_ORDER_1', 'LON_C_CTR', 'EGLL_S_TWR');
        HandoffService::removeFromHandoffOrder('HANDOFF_ORDER_1', 'LON_C_CTR');

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

        $this->assertDatabaseMissing(
            'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 4,
            ]
        );
    }

    public function testItThrowsAnExceptionIfDeletePositionNotInHandoffOrder()
    {
        $this->expectException(OutOfRangeException::class);
        HandoffService::removeFromHandoffOrder('HANDOFF_ORDER_1', 'LON_C_CTR');
    }

    public function testItUpdatesAllHandoffsThatContainAPositionBefore()
    {
        HandoffService::updateAllHandoffsWithPosition('EGLL_N_APP', 'LON_C_CTR', true);

        // Handoff one
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

        // Handoff two
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

    public function testItUpdatesAllHandoffsThatContainAPositionAfter()
    {
        HandoffService::updateAllHandoffsWithPosition('EGLL_N_APP', 'LON_C_CTR', false);

        // Handoff one
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

        // Handoff two
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

    public function testItRemovesFromAllHandoffs()
    {
        HandoffService::removePositionFromAllHandoffs('EGLL_N_APP');
        $this->assertDatabaseMissing(
            'handoff_orders',
            [
                'controller_position_id' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => 1,
                'controller_position_id' => 1,
                'order' => 1
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => 2,
                'controller_position_id' => 3,
                'order' => 1
            ]
        );
    }

    public function testItSetsHandoffForSid()
    {
        HandoffService::setHandoffForSid('EGLL', 'TEST1X', 'HANDOFF_ORDER_2');
        $this->assertDatabaseHas(
            'sid',
            [
                'airfield_id' => 1,
                'identifier' => 'TEST1X',
                'handoff_id' => 2,
            ]
        );
        $this->assertDatabaseHas(
            'sid',
            [
                'airfield_id' => 1,
                'identifier' => 'TEST1Y',
                'handoff_id' => 1,
            ]
        );
    }

    public function testItDeletesHandoffByKey()
    {
        HandoffService::deleteHandoffByKey('HANDOFF_ORDER_2');
        $this->assertDatabaseMissing(
            'handoffs',
            [
                'key' => 'HANDOFF_ORDER_2',
            ]
        );
    }
}
