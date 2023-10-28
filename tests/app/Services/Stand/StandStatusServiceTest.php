<?php

namespace App\Services\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignment;
use App\Models\Stand\StandReservation;
use App\Models\Vatsim\NetworkAircraft;
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
                'latitude' => '54.658827',
                'longitude' => -'6.22207000',
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
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
                'max_aircraft_id_wingspan' => 1,
                'max_aircraft_id_length' => 2,
            ]
        );
        $this->addStandAssignment('ASSIGNMENT', $stand2->id);

        // Stand 3 is reserved
        $stand3 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST3',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $this->addStandReservation('RESERVATION', $stand3->id, true);

        // Stand 4 is occupied
        $stand4 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST4',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        NetworkAircraftService::createPlaceholderAircraft('OCCUPIED');
        $occupier = NetworkAircraft::find('OCCUPIED');
        $occupier->occupiedStand()->sync($stand4);

        // Stand 5 is paired with stand 2 which is assigned
        $stand5 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST5',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand2->pairedStands()->sync($stand5);
        $stand5->pairedStands()->sync($stand2);

        // Stand 6 is paired with stand 3 which is reserved
        $stand6 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST6',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand3->pairedStands()->sync([$stand6->id]);
        $stand6->pairedStands()->sync([$stand3->id]);

        // Stand 7 is paired with stand 4 which is occupied
        $stand7 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST7',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand4->pairedStands()->sync([$stand7->id]);
        $stand7->pairedStands()->sync([$stand4->id]);

        // Stand 8 is paired with stand 1 which is free
        $stand8 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST8',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand1->pairedStands()->sync([$stand8->id]);
        $stand8->pairedStands()->sync([$stand1->id]);

        // Stand 9 is reserved in half an hour
        $stand9 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST9',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
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
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand10->close();

        // Stand 11 is requested
        $stand11 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST11',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand11->requests()->create(
            ['user_id' => self::ACTIVE_USER_CID, 'callsign' => 'BAW123', 'requested_time' => Carbon::now()]
        );

        // Stand 12 has had its request pass
        $stand12 = Stand::create(
            [
                'airfield_id' => 1,
                'identifier' => 'TEST12',
                'latitude' => '54.65882800',
                'longitude' => -'6.22207000',
            ]
        );
        $stand12->requests()->create(
            ['user_id' => self::ACTIVE_USER_CID, 'callsign' => 'BAW123', 'requested_time' => Carbon::now()->subHours(2)]
        );

        $this->assertEquals(
            [
                [
                    'identifier' => 'TEST1',
                    'latitude' => '54.65882700',
                    'longitude' => -'6.2220700000',
                    'type' => 'CARGO',
                    'status' => 'available',
                    'airlines' => [
                        'BAW' => ['EDDM', 'EDDF'],
                    ],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST2',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'assigned',
                    'callsign' => 'ASSIGNMENT',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => [
                        'wingspan' => 'B738',
                        'length' => 'A333',
                    ],
                ],
                [
                    'identifier' => 'TEST3',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'reserved',
                    'callsign' => 'RESERVATION',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST4',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'occupied',
                    'callsign' => 'OCCUPIED',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST5',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST6',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST7',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'unavailable',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST8',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'available',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST9',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'reserved_soon',
                    'callsign' => null,
                    'reserved_at' => Carbon::now()->addMinutes(59)->startOfSecond(),
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST10',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'closed',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                ],
                [
                    'identifier' => 'TEST11',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'requested',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
                    'requested_by' => collect(['BAW123']),
                ],
                [
                    'identifier' => 'TEST12',
                    'latitude' => '54.65882800',
                    'longitude' => -'6.22207000',
                    'type' => null,
                    'status' => 'available',
                    'airlines' => [],
                    'aerodrome_reference_code' => 'A',
                    'max_aircraft' => null,
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
