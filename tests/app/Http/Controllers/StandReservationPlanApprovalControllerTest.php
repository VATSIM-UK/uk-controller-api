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
        $plan = StandReservationPlan::create([
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'start' => '2024-08-11 09:00:00',
                'end' => '2024-08-11 18:00:00',
                'reservations' => [
                    [
                        'airfield' => 'EGLL',
                        'stand' => '1L',
                        'callsign' => 'SBI24',
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
}
