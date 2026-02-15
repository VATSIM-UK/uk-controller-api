<?php

namespace App\Imports\Stand;

use App\BaseFunctionalTestCase;
use Illuminate\Console\OutputStyle;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;

class StandReservationsImportTest extends BaseFunctionalTestCase
{
    /**
     * @var StandReservationsImport
     */
    private $importer;

    public function setUp(): void
    {
        parent::setUp();
        $this->importer = $this->app->make(StandReservationsImport::class);
        $this->importer->withOutput(Mockery::spy(OutputStyle::class));
    }

    public function testItImportsReservations()
    {
        $reservations = collect(
            [
                collect([
                    'EGLL',
                    '1L',
                    'BAW123',
                    '2020-11-12 18:00:00',
                    '2020-11-12 19:00:00',
                ]),
                collect([
                    'EGLL',
                    '251',
                    'BAW251',
                    '2020-11-12 18:00:00',
                    '2020-11-12 19:00:00',
                ]),
            ]
        );

        $this->importer->collection($reservations);

        $this->assertDatabaseCount(
            'stand_reservations',
            2
        );
        $this->assertDatabaseHas(
            'stand_reservations',
            [
                'callsign' => 'BAW123',
                'stand_id' => 1,
                'start' => '2020-11-12 18:00:00',
                'end' => '2020-11-12 19:00:00',
            ]
        );
        $this->assertDatabaseHas(
            'stand_reservations',
            [
                'callsign' => 'BAW251',
                'stand_id' => 2,
                'start' => '2020-11-12 18:00:00',
                'end' => '2020-11-12 19:00:00',
            ]
        );
    }

    public function testItImportsReservationsWithNoCallsign()
    {
        $reservations = collect(
            [
                collect([
                    'EGLL',
                    '1L',
                    '',
                    '2020-11-12 18:00:00',
                    '2020-11-12 19:00:00',
                ]),
                collect([
                    'EGLL',
                    '251',
                    '',
                    '2020-11-12 18:00:00',
                    '2020-11-12 19:00:00',
                ]),
            ]
        );

        $this->importer->collection($reservations);

        $this->assertDatabaseCount(
            'stand_reservations',
            2
        );
        $this->assertDatabaseHas(
            'stand_reservations',
            [
                'stand_id' => 1,
                'start' => '2020-11-12 18:00:00',
                'end' => '2020-11-12 19:00:00',
            ]
        );
        $this->assertDatabaseHas(
            'stand_reservations',
            [
                'stand_id' => 2,
                'start' => '2020-11-12 18:00:00',
                'end' => '2020-11-12 19:00:00',
            ]
        );
    }

    public function testItImportsAssociativeReservations()
    {
        $reservations = collect(
            [
                collect([
                    'airfield' => 'EGLL',
                    'stand' => '1L',
                    'callsign' => 'BAW24A',
                    'cid' => 1234567,
                    'origin' => 'EGKK',
                    'destination' => 'EGLL',
                    'start' => '2024-01-01 09:00:00',
                    'end' => '2024-01-01 10:00:00',
                ]),
            ]
        );

        $this->importer->collection($reservations);

        $this->assertDatabaseHas(
            'stand_reservations',
            [
                'stand_id' => 1,
                'callsign' => 'BAW24A',
                'cid' => 1234567,
                'origin' => 'EGKK',
                'destination' => 'EGLL',
                'start' => '2024-01-01 09:00:00',
                'end' => '2024-01-01 10:00:00',
            ]
        );
    }

    public static function badReservationProvider(): array
    {
        return [
            'Unknown airport' => [
                [
                    'XXXX',
                    '251',
                    '',
                    '2020-11-12 18:00:00',
                    '2020-11-12 19:00:00',
                ],
            ],
            'Unknown stand' => [
                [
                    'EGLL',
                    '999',
                    '',
                    '2020-11-12 18:00:00',
                    '2020-11-12 19:00:00',
                ],
            ],
            'Invalid start time' => [
                [
                    'EGLL',
                    '251',
                    '',
                    'abc',
                    '2020-11-12 19:00:00',
                ],
            ],
            'Invalid end time' => [
                [
                    'EGLL',
                    '251',
                    '',
                    '2020-11-12 18:00:00',
                    'hasdd',
                ],
            ],
            'Start time after end time' => [
                [
                    'EGLL',
                    '251',
                    '',
                    '2020-11-12 18:00:00',
                    '2020-11-12 17:00:00',
                ],
            ],
            'Start time same as end time' => [
                [
                    'EGLL',
                    '251',
                    '',
                    '2020-11-12 18:00:00',
                    '2020-11-12 18:00:00',
                ],
            ],
        ];
    }

    #[DataProvider('badReservationProvider')]
    public function testItDoesntImportBadReservations(array $reservationData)
    {
        $reservation = collect(
            [
                collect($reservationData),
            ]
        );

        $this->importer->collection($reservation);
        $this->assertDatabaseCount(
            'stand_reservations',
            0
        );
    }
}
