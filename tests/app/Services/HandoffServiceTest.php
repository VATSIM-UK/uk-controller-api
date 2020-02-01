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
            [
                "id" => 1,
                "key" => "HANDOFF_ORDER_1",
                "description" => "foo",
                "controllers" => [
                    1,
                    2,
                ],
            ],
            [
                "id" => 2,
                "key" => "HANDOFF_ORDER_2",
                "description" => "foo",
                "controllers" => [
                    2,
                    3,
                ],
            ],
        ];

        $actual = $this->service->getAllHandoffsWithControllers();
        $this->assertSame($expected, $actual);
    }

    public function testItInsertsIntoHandoffOrderBefore()
    {
        $this->service->insertIntoOrderBefore('HANDOFF_ORDER_1', 'LON_C_CTR', 'EGLL_S_TWR');

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
        $this->service->insertIntoOrderBefore('HANDOFF_ORDER_1', 'LON_C_CTR', 'LON_S_CTR');
    }

    public function testItInsertsIntoHandoffOrderAfter()
    {
        $this->service->insertIntoOrderAfter('HANDOFF_ORDER_1', 'LON_C_CTR', 'EGLL_S_TWR');

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
        $this->service->insertIntoOrderAfter('HANDOFF_ORDER_1', 'LON_C_CTR', 'LON_S_CTR');
    }

    public function testItDeletesFromHandoffOrder()
    {
        $this->service->insertIntoOrderBefore('HANDOFF_ORDER_1', 'LON_C_CTR', 'EGLL_S_TWR');
        $this->service->removeFromHandoffOrder('HANDOFF_ORDER_1', 'LON_C_CTR');

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
        $this->service->removeFromHandoffOrder('HANDOFF_ORDER_1', 'LON_C_CTR');
    }

    public function testItUpdatesAllHandoffsThatContainAPositionBefore()
    {
        $this->service->updateAllHandoffsWithPosition('EGLL_N_APP', 'LON_C_CTR', true);

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
        $this->service->updateAllHandoffsWithPosition('EGLL_N_APP', 'LON_C_CTR', false);

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
        $this->service->removePositionFromAllHandoffs('EGLL_N_APP');
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
}
