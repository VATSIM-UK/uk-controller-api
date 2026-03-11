<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\Stand\StandReservationPlan;
use App\Providers\AuthServiceProvider;

class StandReservationPlanApprovalControllerTest extends BaseApiTestCase
{
    protected static $tokenScope = [
        AuthServiceProvider::SCOPE_USER_ADMIN,
    ];

    protected static $tokenUser = 1;

    public function testItListsPendingPlans()
    {
        StandReservationPlan::create([
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'payload' => ['reservations' => []],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_GET, 'stand/reservations/plan/pending')
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Speedbird 24']);
    }

    public function testItApprovesPendingPlanAndImportsReservations()
    {
        $eventStart = now()->addDay()->startOfHour();
        $eventFinish = (clone $eventStart)->addHours(9);
        $reservationStart = (clone $eventStart)->addMinutes(15);
        $reservationEnd = (clone $reservationStart)->addMinutes(30);

        $plan = StandReservationPlan::create([
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => $eventStart->format('Y-m-d H:i:s'),
                'event_finish' => $eventFinish->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '1L',
                        'slot_reservations' => [
                            [
                                'callsign' => 'SBI24',
                                'slotstart' => $reservationStart->format('Y-m-d H:i:s'),
                                'slotend' => $reservationEnd->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, "stand/reservations/plan/{$plan->id}/approve")
            ->assertStatus(200)
            ->assertJson(['created' => 1]);

        $this->assertDatabaseHas('stand_reservations', [
            'callsign' => 'SBI24',
            'stand_id' => 1,
        ]);

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'approved',
            'approved_by' => 1,
            'imported_reservations' => 1,
        ]);
    }

    public function testItSystemDeniesPendingPlanWhenEventStartHasPassed()
    {
        $plan = StandReservationPlan::create([
            'name' => 'Late Approval',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->subHour()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addHour()->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '1L',
                        'slot_reservations' => [
                            [
                                'callsign' => 'SBI99',
                                'slotstart' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
                                'slotend' => now()->addMinutes(10)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
        ]);

        $this->makeAuthenticatedApiRequest(self::METHOD_PATCH, "stand/reservations/plan/{$plan->id}/approve")
            ->assertStatus(422)
            ->assertJsonPath('message', 'Event start has already passed');

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'denied',
            'denied_by' => StandReservationPlan::AUTOMATION_DENIED_BY_USER_ID,
            'denied_reason' => StandReservationPlan::AUTOMATION_EVENT_STARTED_PRIOR_TO_APPROVAL_REASON,
        ]);

        $this->assertDatabaseMissing('stand_reservations', [
            'callsign' => 'SBI99',
        ]);
    }
}
