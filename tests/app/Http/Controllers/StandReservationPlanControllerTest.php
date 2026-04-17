<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\User\Role;
use App\Models\User\RoleKeys;
use App\Models\User\User;
use UserTableSeeder;

class StandReservationPlanControllerTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $user = User::findOrFail(UserTableSeeder::ACTIVE_USER_CID);
        $user->roles()->sync([Role::idFromKey(RoleKeys::VAA)]);
    }

    public function testItStoresAValidStandReservationPlan(): void
    {
        $payload = [
            'name' => 'Heathrow Summer Event',
            'contact_email' => 'ops@example.org',
            'payload' => [
                'event_start' => '2026-06-12T08:00:00Z',
                'event_end' => '2026-06-12T20:00:00Z',
                'event_airport' => 'EGLL',
                'reservations' => [
                    [
                        'stand_id' => 1,
                        'cid' => 1203533,
                        'timefrom' => '2026-06-12T08:00:00Z',
                        'timeto' => '2026-06-12T10:00:00Z',
                    ],
                    [
                        'stand' => '251',
                        'cid' => 1203533,
                        'timefrom' => '2026-06-12T10:15:00Z',
                        'timeto' => '2026-06-12T12:00:00Z',
                    ],
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'stand/reservation-plan', $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'status'])
            ->assertJson(['status' => 'submitted']);

        $this->assertDatabaseHas(
            'stand_reservation_plans',
            [
                'name' => 'Heathrow Summer Event',
                'contact_email' => 'ops@example.org',
                'submitted_by' => UserTableSeeder::ACTIVE_USER_CID,
                'status' => 'submitted',
            ]
        );
    }

    public function testItRejectsNonVaaUser(): void
    {
        $user = User::findOrFail(UserTableSeeder::ACTIVE_USER_CID);
        $user->roles()->sync([]);

        $response = $this->makeAuthenticatedApiRequest(
            self::METHOD_POST,
            'stand/reservation-plan',
            [
                'name' => 'Heathrow Summer Event',
                'contact_email' => 'ops@example.org',
                'payload' => [
                    'event_start' => '2026-06-12T08:00:00Z',
                    'event_end' => '2026-06-12T20:00:00Z',
                    'event_airport' => 'EGLL',
                    'reservations' => [],
                ],
            ]
        );

        $response->assertStatus(403);
    }

    public function testItRejectsOverlappingReservationsForTheSameStand(): void
    {
        $payload = [
            'name' => 'Overlap Test',
            'contact_email' => 'ops@example.org',
            'payload' => [
                'event_start' => '2026-06-12T08:00:00Z',
                'event_end' => '2026-06-12T20:00:00Z',
                'event_airport' => 'EGLL',
                'reservations' => [
                    [
                        'stand_id' => 1,
                        'cid' => 1203533,
                        'timefrom' => '2026-06-12T10:00:00Z',
                        'timeto' => '2026-06-12T11:00:00Z',
                    ],
                    [
                        'stand_id' => 1,
                        'cid' => 1203533,
                        'timefrom' => '2026-06-12T10:30:00Z',
                        'timeto' => '2026-06-12T12:00:00Z',
                    ],
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'stand/reservation-plan', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payload']);

        $this->assertDatabaseMissing(
            'stand_reservation_plans',
            [
                'name' => 'Overlap Test',
                'contact_email' => 'ops@example.org',
            ]
        );
    }

    public function testItRejectsStandIdentifierWithoutAirportForMultiAirportEvents(): void
    {
        $payload = [
            'name' => 'Multi Airport Missing Reservation Airport',
            'contact_email' => 'ops@example.org',
            'payload' => [
                'event_start' => '2026-06-12T08:00:00Z',
                'event_end' => '2026-06-12T20:00:00Z',
                'event_airports' => ['EGLL', 'EGKK'],
                'reservations' => [
                    [
                        'stand' => '251',
                        'cid' => 1203533,
                        'timefrom' => '2026-06-12T09:00:00Z',
                        'timeto' => '2026-06-12T10:00:00Z',
                    ],
                ],
            ],
        ];

        $this->makeAuthenticatedApiRequest(self::METHOD_POST, 'stand/reservation-plan', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['payload']);

        $this->assertDatabaseMissing(
            'stand_reservation_plans',
            [
                'name' => 'Multi Airport Missing Reservation Airport',
                'contact_email' => 'ops@example.org',
            ]
        );
    }
}
