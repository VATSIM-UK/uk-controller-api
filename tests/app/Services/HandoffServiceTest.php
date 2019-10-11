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
}
