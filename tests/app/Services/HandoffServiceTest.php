<?php

namespace App\Services;

use App\BaseFunctionalTestCase;

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

    public function testItThrowsException()
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

    public function testItInsertsIntoOrderBefore()
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
}
