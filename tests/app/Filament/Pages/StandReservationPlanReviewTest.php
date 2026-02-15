<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class StandReservationPlanReviewTest extends BaseFilamentTestCase
{
    #[DataProvider('renderRoleProvider')]
    public function testItRendersForAdminRolesOnly(?RoleKeys $role, bool $shouldRender)
    {
        if ($role !== null) {
            $this->assumeRole($role);
        } else {
            $this->noRole();
        }

        $response = Livewire::test(StandReservationPlanReview::class);
        if ($shouldRender) {
            $response->assertOk();
        } else {
            $response->assertForbidden();
        }
    }

    public static function renderRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'VAA' => [RoleKeys::VAA, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }

    public function testItApprovesPlanAndCreatesReservations()
    {
        $this->assumeRole(RoleKeys::WEB_TEAM);

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
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        Livewire::test(StandReservationPlanReview::class)
            ->call('approvePlan', $plan->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'approved',
            'approved_by' => self::ACTIVE_USER_CID,
            'imported_reservations' => 1,
        ]);
        $this->assertDatabaseHas('stand_reservations', [
            'callsign' => 'SBI24',
            'stand_id' => 1,
        ]);
    }

    public function testItDeniesPlan()
    {
        $this->assumeRole(RoleKeys::OPERATIONS_TEAM);

        $plan = StandReservationPlan::create([
            'name' => 'Denied Plan',
            'contact_email' => 'ops@example.com',
            'payload' => ['reservations' => []],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        Livewire::test(StandReservationPlanReview::class)
            ->call('denyPlan', $plan->id)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'denied',
            'denied_by' => self::ACTIVE_USER_CID,
        ]);
    }
}
