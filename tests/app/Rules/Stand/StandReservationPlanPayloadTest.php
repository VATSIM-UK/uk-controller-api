<?php

namespace App\Rules\Stand;

use App\BaseUnitTestCase;
use Illuminate\Support\Facades\Validator;

class StandReservationPlanPayloadTest extends BaseUnitTestCase
{
    private StandReservationPlanPayload $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new StandReservationPlanPayload();
    }

    private function validatePayload(array $payload): bool
    {
        return !Validator::make(
            ['payload' => $payload],
            ['payload' => $this->rule]
        )->fails();
    }

    private function validSingleAirportPayload(): array
    {
        return [
            'event_start' => '2026-06-12T08:00:00Z',
            'event_end' => '2026-06-12T20:00:00Z',
            'event_airport' => 'EGLL',
            'reservations' => [
                [
                    'stand_id' => 1201,
                    'cid' => 1203533,
                    'timefrom' => '2026-06-12T09:00:00Z',
                    'timeto' => '2026-06-12T10:00:00Z',
                ],
            ],
        ];
    }

    public function testItAcceptsAValidStandIdPlan(): void
    {
        $this->assertTrue($this->validatePayload($this->validSingleAirportPayload()));
    }

    public function testItRejectsAPlanWhereEventEndIsNotAfterEventStart(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['event_end'] = $payload['event_start'];

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsAReservationWhereTimeToIsNotAfterTimeFrom(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['reservations'][0]['timeto'] = $payload['reservations'][0]['timefrom'];

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsAReservationWhenBothStandIdAndStandAreProvided(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['reservations'][0]['stand'] = 'A23';

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsAReservationWhenNeitherStandIdNorStandIsProvided(): void
    {
        $payload = $this->validSingleAirportPayload();
        unset($payload['reservations'][0]['stand_id']);

        $this->assertFalse($this->validatePayload($payload));
    }
    public function testItAcceptsAValidStandIdentifierPlan(): void
    {
        $payload = [
            'event_start' => '2026-06-12T08:00:00Z',
            'event_end' => '2026-06-12T20:00:00Z',
            'event_airports' => ['EGLL', 'EGKK'],
            'reservations' => [
                [
                    'airport' => 'EGLL',
                    'stand' => 'A23',
                    'cid' => 1203533,
                    'timefrom' => '2026-06-12T09:00:00Z',
                    'timeto' => '2026-06-12T10:00:00Z',
                ],
            ],
        ];

        $this->assertTrue($this->validatePayload($payload));
    }

    public function testItRejectsAStandIdentifierPlanWhenAirportIsMissingForMultiAirportEvents(): void
    {
        $payload = [
            'event_start' => '2026-06-12T08:00:00Z',
            'event_end' => '2026-06-12T20:00:00Z',
            'event_airports' => ['EGLL', 'EGKK'],
            'reservations' => [
                [
                    'stand' => 'A23',
                    'cid' => 1203533,
                    'timefrom' => '2026-06-12T09:00:00Z',
                    'timeto' => '2026-06-12T10:00:00Z',
                ],
            ],
        ];

        $validator = Validator::make(['payload' => $payload], ['payload' => $this->rule]);

        $this->assertFalse($validator->passes());
        $this->assertContains(
            "payload.reservations.0.airport is required when event_airports contains multiple airports and stand is used.",
            $validator->errors()->all()
        );
    }

    public function testItRejectsAStandIdentifierPlanWhenAirportIsNotOneOfTheEventAirports(): void
    {
        $payload = [
            'event_start' => '2026-06-12T08:00:00Z',
            'event_end' => '2026-06-12T20:00:00Z',
            'event_airports' => ['EGLL', 'EGKK'],
            'reservations' => [
                [
                    'airport' => 'EGCC',
                    'stand' => 'A23',
                    'cid' => 1203533,
                    'timefrom' => '2026-06-12T09:00:00Z',
                    'timeto' => '2026-06-12T10:00:00Z',
                ],
            ],
        ];

        $validator = Validator::make(['payload' => $payload], ['payload' => $this->rule]);

        $this->assertFalse($validator->passes());
        $this->assertContains(
            "payload.reservations.0.airport must be one of the event's airports.",
            $validator->errors()->all()
        );
    }

    public function testItAcceptsEiPrefixedUkIcaoCodes(): void
    {
        $payload = [
            'event_start' => '2026-06-12T08:00:00Z',
            'event_end' => '2026-06-12T20:00:00Z',
            'event_airport' => 'EIDW',
            'reservations' => [
                [
                    'airport' => 'EIDW',
                    'stand' => 'A23',
                    'cid' => 1203533,
                    'timefrom' => '2026-06-12T09:00:00Z',
                    'timeto' => '2026-06-12T10:00:00Z',
                ],
            ],
        ];

        $this->assertTrue($this->validatePayload($payload));
    }

    public function testItDoesNotEmitAStandAirportMismatchWhenEventAirportsAreInvalid(): void
    {
        $payload = [
            'event_start' => '2026-06-12T08:00:00Z',
            'event_end' => '2026-06-12T20:00:00Z',
            'event_airports' => [],
            'reservations' => [
                [
                    'airport' => 'EGLL',
                    'stand' => 'A23',
                    'cid' => 1203533,
                    'timefrom' => '2026-06-12T09:00:00Z',
                    'timeto' => '2026-06-12T10:00:00Z',
                ],
            ],
        ];

        $validator = Validator::make(['payload' => $payload], ['payload' => $this->rule]);

        $this->assertFalse($validator->passes());
        $this->assertContains(
            'payload.event_airports must be a non-empty array of UK 4-letter ICAO codes.',
            $validator->errors()->all()
        );
        $this->assertNotContains(
            "payload.reservations.0.airport is required when event_airports contains multiple airports and stand is used.",
            $validator->errors()->all()
        );
        $this->assertNotContains(
            "payload.reservations.0.airport must be one of the event's airports.",
            $validator->errors()->all()
        );
    }

    public function testItRejectsReservationsThatAreNotAList(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['reservations'] = [
            'stand-one' => $payload['reservations'][0],
        ];

        $validator = Validator::make(['payload' => $payload], ['payload' => $this->rule]);

        $this->assertFalse($validator->passes());
        $this->assertContains(
            'payload.reservations must be a non-empty list of reservations.',
            $validator->errors()->all()
        );
    }

    public function testItRejectsNonUkIcaoCodes(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['event_airport'] = 'LFPG';

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsUnknownFields(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['unexpected'] = 'value';

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsReservationsOutsideTheEventWindow(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['reservations'][0]['timefrom'] = '2026-06-12T07:59:59Z';

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsOverlappingReservationsForTheSameStand(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['reservations'] = [
            [
                'stand_id' => 1201,
                'cid' => 1203533,
                'timefrom' => '2026-06-12T09:00:00Z',
                'timeto' => '2026-06-12T10:00:00Z',
            ],
            [
                'stand_id' => 1201,
                'cid' => 1203534,
                'timefrom' => '2026-06-12T09:30:00Z',
                'timeto' => '2026-06-12T10:30:00Z',
            ],
        ];

        $this->assertFalse($this->validatePayload($payload));
    }

    public function testItRejectsNegativeStandIds(): void
    {
        $payload = $this->validSingleAirportPayload();
        $payload['reservations'][0]['stand_id'] = -1;

        $this->assertFalse($this->validatePayload($payload));
    }
}
