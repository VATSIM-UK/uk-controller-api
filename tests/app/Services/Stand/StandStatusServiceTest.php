<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandRequest;
use App\Models\Stand\StandReservation;
use App\Services\NetworkAircraftService;
use Carbon\Carbon;

class StandStatusServiceTest extends BaseFunctionalTestCase
{
    public function testItReturnsStandStatuses()
    {
        Carbon::setTestNow(Carbon::now());

        // Clear out all the stands so its easier to follow the test data.
        Stand::all()->each(function (Stand $stand) {
            $stand->delete();
        });

        // Stand 1 is free but has a reservation starting in a few hours, it also has an airline with some destinations
        $stand1 = Stand::create(
            [
                'airfield_id' => 1,
                'type_id' => 3,
                'identifier' => 'TEST1',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $this->addStandReservation('FUTURE-RESERVATION', $stand1->id, false);
        $stand1->airlines()->attach([1 => ['destination' => 'EDDM']]);
        $stand1->airlines()->attach([1 => ['destination' => 'EDDF']]);

        // Stand 2 is assigned, it has a max aircraft type
        $stand2 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST2',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
                'max_aircraft_id' => 1,
            ]
        );
        $this->addStandAssignment('ASSIGNMENT', $stand2->id);

        // Stand 3 is reserved
        $stand3 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST3',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $this->addStandReservation('RESERVATION', $stand3->id, true);

        // Stand 4 is occupied
        $stand4 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST4',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $occupier = NetworkAircraftService::createPlaceholderAircraft('OCCUPIED');
        $occupier->occupiedStand()->sync($stand4);

        // Stand 5 is paired with stand 2 which is assigned
        $stand5 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST5',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand2->pairedStands()->sync($stand5);
        $stand5->pairedStands()->sync($stand2);

        // Stand 6 is paired with stand 3 which is reserved
        $stand6 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST6',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand3->pairedStands()->sync([$stand6->id]);
        $stand6->pairedStands()->sync([$stand3->id]);

        // Stand 7 is paired with stand 4 which is occupied
        $stand7 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST7',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand4->pairedStands()->sync([$stand7->id]);
        $stand7->pairedStands()->sync([$stand4->id]);

        // Stand 8 is paired with stand 1 which is free
        $stand8 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST8',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand1->pairedStands()->sync([$stand8->id]);
        $stand8->pairedStands()->sync([$stand1->id]);

        // Stand 9 is reserved in half an hour
        $stand9 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST9',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        StandReservation::create(
            [
                'callsign' => null,
                'stand_id' => $stand9->id,
                'start' => Carbon::now()->addMinutes(59)->startOfSecond(),
                'end' => Carbon::now()->addHours(2),
            ]
        );

        // Stand 10 is closed
        $stand10 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST10',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand10->close();

        // Stand 11 is requested
        $stand11 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST11',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand11->requests()->create(
            ['user_id' => self::ACTIVE_USER_CID, 'callsign' => 'BAW123', 'requested_time' => Carbon::now()]
        );

        // Stand 11 is but too far in the future
        $stand12 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST12',
                'latitude' => 54.658828,
                'longitude' => -6.222070,
            ]
        );
        $stand12->requests()->create(
            ['user_id' => self::ACTIVE_USER_CID, 'callsign' => 'BAW123', 'requested_time' => Carbon::now()->addHour()]
        );

        $this->assertEquals(
            [
                [
                    'identifier' => 'TEST1',
                    'type' => 'CARGO',
                    'status' => 'available',
                    'airlines' => [
                        'BAW' => ['EDDM', 'EDDF'],
                    ],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST2',
                    'type' => null,
                    'status' => 'assigned',
                    'callsign' => 'ASSIGNMENT',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => 'B738',
                ],
                [
                    'identifier' => 'TEST3',
                    'type' => null,
                    'status' => 'reserved',
                    'callsign' => 'RESERVATION',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST4',
                    'type' => null,
                    'status' => 'occupied',
                    'callsign' => 'OCCUPIED',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST5',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST6',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST7',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST8',
                    'type' => null,
                    'status' => 'available',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST9',
                    'type' => null,
                    'status' => 'reserved_soon',
                    'callsign' => null,
                    'reserved_at' => Carbon::now()->addMinutes(59)->startOfSecond(),
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST10',
                    'type' => null,
                    'status' => 'closed',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
                [
                    'identifier' => 'TEST11',
                    'type' => null,
                    'status' => 'requested',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                    'requested_by' => ['BAW123'],
                ],
                [
                    'identifier' => 'TEST12',
                    'type' => null,
                    'status' => 'available',
                    'airlines' => [],
                    'max_wake_category' => 'LM',
                    'max_aircraft_type' => null,
                ],
            ],
            StandStatusService::getAirfieldStandStatus('EGLL')
        );
    }


    private function addStandReservation(string $callsign, int $standId, bool $active): void
    {
        NetworkAircraftService::createPlaceholderAircraft($callsign);
        StandReservation::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
                'start' => $active ? Carbon::now() : Carbon::now()->addHours(2),
                'end' => Carbon::now()->addHours(2)->addMinutes(10),
                'destination' => 'EGLL',
                'origin' => 'EGSS',
            ]
        );
    }

    private function addStandAssignment(string $callsign, int $standId): void
    {
        NetworkAircraftService::createPlaceholderAircraft($callsign);
        StandAssignment::create(
            [
                'callsign' => $callsign,
                'stand_id' => $standId,
            ]
        );
    }
}
