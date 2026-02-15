<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use App\Models\User\RoleKeys;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class StandReservationPlanSubmissionTest extends BaseFilamentTestCase
{
    #[DataProvider('renderRoleProvider')]
    public function testItRendersForAuthorisedRolesOnly(?RoleKeys $role, bool $shouldRender)
    {
        if ($role !== null) {
            $this->assumeRole($role);
        } else {
            $this->noRole();
        }

        $response = Livewire::test(StandReservationPlanSubmission::class);
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
            'VAA' => [RoleKeys::VAA, true],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, false],
        ];
    }

    public function testItSubmitsPlanAsPending()
    {
        $this->assumeRole(RoleKeys::VAA);

        Livewire::test(StandReservationPlanSubmission::class)
            ->set('name', 'Speedbird 24')
            ->set('contact_email', 'ops@example.com')
            ->set('plan_json', json_encode([
                'reservations' => [
                    [
                        'airfield' => 'EGLL',
                        'stand' => '1L',
                        'start' => '2026-02-20 09:00:00',
                        'end' => '2026-02-20 10:00:00',
                    ],
                ],
            ]))
            ->call('submitPlan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);
    }
}
