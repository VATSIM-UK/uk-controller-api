<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use Illuminate\Support\Facades\DB;
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
                'key' => 'HANDOFF_ORDER_1',
                'controller_positions' => [
                    4,
                    1,
                    2,
                ],
            ],
            [
                'id' => 2,
                'key' => 'HANDOFF_ORDER_2',
                'controller_positions' => [
                    2,
                    3,
                ],
            ],
        ];

        $this->assertSame($expected, $this->service->getHandoffsV2Dependency());
    }

    public function testItSetsControllerPositionsForHandoffOrder()
    {
        $handoff = HandoffService::createNewHandoffOrder('NEW_HANDOFF_ORDER', 'New!', ['EGLL_S_TWR']);
        HandoffService::setPositionsForHandoffOrder('NEW_HANDOFF_ORDER', ['EGLL_N_APP', 'LON_S_CTR']);

        $this->assertDatabaseMissing(
            'handoff_orders',
            [
                'handoff_id' => $handoff->id,
                'controller_position_id' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => $handoff->id,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => $handoff->id,
                'controller_position_id' => 3,
                'order' => 2,
            ]
        );
    }

    public function testItAddsANewHandoffOrder()
    {
        $handoff = HandoffService::createNewHandoffOrder('NEW_HANDOFF_ORDER', 'New!', ['EGLL_N_APP', 'LON_S_CTR']);

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
                'handoff_id' => $handoff->id,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'handoff_orders',
            [
                'handoff_id' => $handoff->id,
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
        HandoffService::createNewHandoffOrder('TEST_XYZ', 'Test', ['EGLL_N_APP']);
        HandoffService::deleteHandoffByKey('TEST_XYZ');
        $this->assertDatabaseMissing(
            'handoffs',
            [
                'key' => 'TEST_XYZ',
            ]
        );
    }
}
