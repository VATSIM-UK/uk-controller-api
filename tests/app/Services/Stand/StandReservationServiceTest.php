<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Exceptions\Stand\CallsignHasClashingReservationException;
use App\Exceptions\Stand\StandNotFoundException;
use App\Exceptions\Stand\StandReservationAirfieldsInvalidException;
use App\Exceptions\Stand\StandReservationCallsignNotValidException;
use App\Exceptions\Stand\StandReservationTimeInvalidException;
use App\Models\Stand\StandReservation;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;

class StandReservationServiceTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        StandReservation::create(
            [
                'callsign' => 'BAW999',
                'stand_id' => 1,
                'start' => Carbon::parse('2022-01-01 19:00:00'),
                'end' => Carbon::parse('2022-01-01 20:00:00'),
            ]
        );
        StandReservation::create(
            [
                'callsign' => 'BAW123',
                'stand_id' => 2,
                'start' => Carbon::parse('2022-01-02 19:00:00'),
                'end' => Carbon::parse('2022-01-02 20:00:00'),
            ]
        );
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testItCreatesStandReservations(
        string $callsign,
        CarbonInterface $startTime,
        CarbonInterface $endTime,
        ?string $origin,
        ?string $destination
    ) {
        StandReservationService::createStandReservation(
            $callsign,
            1,
            $startTime,
            $endTime,
            $origin,
            $destination
        );


        $this->assertDatabaseHas(
            'stand_reservations',
            [
                'callsign' => $callsign,
                'stand_id' => 1,
                'start' => $startTime->toDateTimeString(),
                'end' => $endTime->toDateTimeString(),
                'origin' => $origin,
                'destination' => $destination
            ]
        );
    }

    public function goodDataProvider(): array
    {
        return [
            'Starts before existing, ends before existing' => [
                'BAW123',
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:45:00'),
                'EGKK',
                'EGLL',
            ],
            'Starts before existing, ends at existing start' => [
                'BAW123',
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 19:00:00'),
                'EGKK',
                'EGLL',
            ],
            'Starts when existing ends, ends after existing' => [
                'BAW123',
                Carbon::parse('2022-01-01 20:00:00'),
                Carbon::parse('2022-01-01 20:45:00'),
                'EGKK',
                'EGLL',
            ],
            'Starts after existing, ends after existing' => [
                'BAW123',
                Carbon::parse('2022-01-01 20:30:00'),
                Carbon::parse('2022-01-01 20:45:00'),
                'EGKK',
                'EGLL',
            ],
            'No origin or destination' => [
                'BAW123',
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:45:00'),
                null,
                null,
            ],
        ];
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testItThrowsExceptionsForBadReservationData(
        string $callsign,
        int $standId,
        CarbonInterface $startTime,
        CarbonInterface $endTime,
        ?string $origin,
        ?string $destination,
        string $expectedExceptionType,
        string $expectedExceptionMessage
    ) {
        try {
            StandReservationService::createStandReservation(
                $callsign,
                $standId,
                $startTime,
                $endTime,
                $origin,
                $destination
            );
        } catch (Exception $exception) {
            $this->assertEquals($expectedExceptionType, get_class($exception));
            $this->assertEquals($expectedExceptionMessage, $exception->getMessage());
            $this->assertDatabaseCount(
                'stand_reservations',
                2
            );
            return;
        }

        $this->fail('Expected exception but none thrown');
    }

    public function badDataProvider(): array
    {
        return [
            'Invalid callsign' => [
                '###',
                1,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:45:00'),
                'EGKK',
                'EGLL',
                StandReservationCallsignNotValidException::class,
                'Callsign ### is not valid for stand reservation'
            ],
            'Stand doesnt exist' => [
                'BAW123',
                5,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:45:00'),
                'EGKK',
                'EGLL',
                StandNotFoundException::class,
                'Stand with id 5 not found'
            ],
            'Start time same as end time' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:00:00'),
                'EGKK',
                'EGLL',
                StandReservationTimeInvalidException::class,
                'Invalid stand reservation time'
            ],
            'Start time after end time' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-01 19:00:00'),
                Carbon::parse('2022-01-01 18:45:00'),
                'EGKK',
                'EGLL',
                StandReservationTimeInvalidException::class,
                'Invalid stand reservation time'
            ],
            'Callsign reservation clash with start time' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-02 19:05:00'),
                Carbon::parse('2022-01-02 20:05:00'),
                'EGKK',
                'EGLL',
                CallsignHasClashingReservationException::class,
                'Callsign BAW123 has a clashing stand reservation'
            ],
            'Callsign reservation clash with end time' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-02 18:05:00'),
                Carbon::parse('2022-01-02 19:05:00'),
                'EGKK',
                'EGLL',
                CallsignHasClashingReservationException::class,
                'Callsign BAW123 has a clashing stand reservation'
            ],
            'Callsign reservation clash over entire period' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-02 18:05:00'),
                Carbon::parse('2022-01-02 20:05:00'),
                'EGKK',
                'EGLL',
                CallsignHasClashingReservationException::class,
                'Callsign BAW123 has a clashing stand reservation'
            ],
            'Only origin airfield set' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:30:00'),
                'EGKK',
                null,
                StandReservationAirfieldsInvalidException::class,
                'Stand reservations require both or neither airfield to be set'
            ],
            'Only destination airfield set' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:30:00'),
                null,
                'EGLL',
                StandReservationAirfieldsInvalidException::class,
                'Stand reservations require both or neither airfield to be set'
            ],
            'Origin airfield not valid' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:30:00'),
                'EGKKS',
                'EGLL',
                StandReservationAirfieldsInvalidException::class,
                'Stand reservation origin airfield EGKKS is invalid'
            ],
            'Destination airfield not valid' => [
                'BAW123',
                1,
                Carbon::parse('2022-01-01 18:00:00'),
                Carbon::parse('2022-01-01 18:30:00'),
                'EGKK',
                'EGLLS',
                StandReservationAirfieldsInvalidException::class,
                'Stand reservation destination airfield EGLLS is invalid'
            ],
        ];
    }
}
