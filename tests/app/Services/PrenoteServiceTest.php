<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Airfield\Airfield;
use App\Models\Controller\Prenote;
use Illuminate\Support\Facades\DB;
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

    private function getExpectedPairing(bool $withFlightRules): array
    {
        return [
            'origin' => 'EGLL',
            'destination' => 'EGBB',
            'type' => 'airfieldPairing',
            'flight_rules' => $withFlightRules ? 'I' : null,
            'recipient' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
            ],
        ];
    }

    private function getExpectedSid(): array
    {
        return [
            'airfield' => 'EGLL',
            'departure' => 'TEST1X',
            'type' => 'sid',
            'recipient' => [
                'EGLL_S_TWR',
                'EGLL_N_APP',
            ],
        ];
    }

    public function testItFormatsSidPrenotes()
    {
        $this->assertEquals([$this->getExpectedSid()], $this->service->getAllSidPrenotes());
    }

    public function testItFormatsAirfieldPairingPrenotes()
    {
        Airfield::find(1)->prenotePairings()->updateExistingPivot(2, ['flight_rule_id' => null]);
        $this->assertEquals([$this->getExpectedPairing(false)], $this->service->getAllAirfieldPrenotes());
    }

    public function testItFormatsAirfieldPairingPrenotesWithFlightRules()
    {
        Airfield::find(1)->prenotePairings()->updateExistingPivot(2, ['flight_rule_id' => 2]);
        $this->assertEquals([$this->getExpectedPairing(true)], $this->service->getAllAirfieldPrenotes());
    }

    public function testItFormatsAllPrenotes()
    {
        Airfield::find(1)->prenotePairings()->updateExistingPivot(2, ['flight_rule_id' => 2]);
        $expected = [
            $this->getExpectedSid(),
            $this->getExpectedPairing(true),
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

    public function testItCreatesAirfieldPairingPrenote()
    {
        PrenoteService::createNewAirfieldPairingFromPrenote('EGLL', 'EGBB', 'PRENOTE_TWO');
        $this->assertDatabaseHas(
            'airfield_pairing_prenotes',
            [
                'origin_airfield_id' => 1,
                'destination_airfield_id' => 2,
                'prenote_id' => 2,
            ]
        );
    }

    public function testItDeletesAirfieldPairingPrenoteForPair()
    {
        DB::table('airfield_pairing_prenotes')
            ->insert(
                [
                    'origin_airfield_id' => 1,
                    'destination_airfield_id' => 2,
                    'prenote_id' => 2,
                ]
            );
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGLL', 'EGBB');
        $this->assertDatabaseMissing(
            'airfield_pairing_prenotes',
            [
                'origin_airfield_id' => 1,
                'destination_airfield_id' => 2,
            ]
        );
    }

    public function testItReturnsPrenotesV2Dependency()
    {
        $expected = [
            [
                'id' => 1,
                'key' => 'PRENOTE_ONE',
                'description' => 'Prenote One',
                'controller_positions' => [
                    1,
                    2,
                ]
            ],
            [
                'id' => 2,
                'key' => 'PRENOTE_TWO',
                'description' => 'Prenote Two',
                'controller_positions' => [
                    2,
                    3,
                ]
            ],
        ];

        $this->assertEquals($expected, $this->service->getPrenotesV2Dependency());
    }
}
