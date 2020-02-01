<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use OutOfRangeException;

class PrenoteServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var PrenoteService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(PrenoteService::class);
    }

    public function testItFormatsSidPrenotes()
    {
        $expected = [
            [
                'airfield' => 'EGLL',
                'departure' => 'TEST1X',
                'type' => 'sid',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];
        $this->assertEquals($expected, $this->service->getAllSidPrenotes());
    }

    public function testItFormatsAirfieldPairingPrenotes()
    {
        $expected = [
            [
                'origin' => 'EGLL',
                'destination' => 'EGBB',
                'type' => 'airfieldPairing',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];
        $this->assertEquals($expected, $this->service->getAllAirfieldPrenotes());
    }

    public function testItFormatsAllPrenotes()
    {
        $expected = [
            [
                'airfield' => 'EGLL',
                'departure' => 'TEST1X',
                'type' => 'sid',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ],
            [
                'origin' => 'EGLL',
                'destination' => 'EGBB',
                'type' => 'airfieldPairing',
                'recipient' => [
                    'EGLL_S_TWR',
                    'EGLL_N_APP',
                ],
            ]
        ];
        $this->assertEquals($expected, $this->service->getAllPrenotesWithControllers());
    }

    public function testItInsertsIntoPrenoteOrderBefore()
    {
        PrenoteService::insertIntoOrderBefore('PRENOTE_ONE', 'LON_C_CTR', 'EGLL_S_TWR');

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
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
                'controller_position_id' => 2,
                'order' => 3,
            ]
        );
    }

    public function testItThrowsAnExceptionIfBeforePositionNotInPrenoteOrder()
    {
        $this->expectException(OutOfRangeException::class);
        PrenoteService::insertIntoOrderBefore('PRENOTE_ONE', 'LON_C_CTR', 'LON_S_CTR');
    }

    public function testItInsertsIntoPrenoteOrderAfter()
    {
        PrenoteService::insertIntoOrderAfter('PRENOTE_ONE', 'LON_C_CTR', 'EGLL_S_TWR');

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

    public function testItThrowsAnExceptionIfAfterPositionNotInPrenoteOrder()
    {
        $this->expectException(OutOfRangeException::class);
        PrenoteService::insertIntoOrderAfter('PRENOTE_ONE', 'LON_C_CTR', 'LON_S_CTR');
    }

    public function testItDeletesFromPrenoteOrder()
    {
        PrenoteService::insertIntoOrderBefore('PRENOTE_ONE', 'LON_C_CTR', 'EGLL_S_TWR');
        PrenoteService::removeFromPrenoteOrder('PRENOTE_ONE', 'LON_C_CTR');

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

        $this->assertDatabaseMissing(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 4,
            ]
        );
    }

    public function testItThrowsAnExceptionIfDeletePositionNotInPrenoteOrder()
    {
        $this->expectException(OutOfRangeException::class);
        PrenoteService::removeFromPrenoteOrder('PRENOTE_ONE', 'LON_C_CTR');
    }

    public function testItUpdatesAllPrenotesThatContainAPositionBefore()
    {
        PrenoteService::updateAllPrenotesWithPosition('EGLL_N_APP', 'LON_C_CTR', true);

        // Prenote one
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

        // Prenote two
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 4,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 2,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItUpdatesAllPrenotesThatContainAPositionAfter()
    {
        PrenoteService::updateAllPrenotesWithPosition('EGLL_N_APP', 'LON_C_CTR', false);

        // Prenote one
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

        // Prenote two
        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 2,
                'order' => 1,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 4,
                'order' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 3,
                'order' => 3,
            ]
        );
    }

    public function testItRemovesFromAllPrenotes()
    {
        PrenoteService::removePositionFromAllPrenotes('EGLL_N_APP');
        $this->assertDatabaseMissing(
            'prenote_orders',
            [
                'controller_position_id' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 1,
                'controller_position_id' => 1,
                'order' => 1
            ]
        );

        $this->assertDatabaseHas(
            'prenote_orders',
            [
                'prenote_id' => 2,
                'controller_position_id' => 3,
                'order' => 1
            ]
        );
    }
}
