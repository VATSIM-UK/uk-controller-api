<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Controller\ControllerPosition;
use Illuminate\Support\Facades\DB;

class HandoffServiceTest extends BaseFunctionalTestCase
{
    private readonly HandoffService $service;

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
}
