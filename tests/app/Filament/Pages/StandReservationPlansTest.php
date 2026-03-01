<?php

namespace App\Filament\Pages;

use App\BaseFilamentTestCase;
use App\Models\Stand\StandReservationPlan;
use App\Models\User\RoleKeys;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

class StandReservationPlansTest extends BaseFilamentTestCase
{
    #[DataProvider('renderRoleProvider')]
    public function testItRendersForAuthorisedRolesOnly(?RoleKeys $role, bool $shouldRender)
    {
        if ($role !== null) {
            $this->assumeRole($role);
        } else {
            $this->noRole();
        }

        $response = Livewire::test(StandReservationPlans::class);
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

        $eventStart = now()->addDay()->setTime(9, 0);
        $eventFinish = $eventStart->copy()->addHour();

        Livewire::test(StandReservationPlans::class)
            ->fillForm([
                'name' => 'Speedbird 24',
                'contactEmail' => 'ops@example.com',
                'planJson' => json_encode([
                    'event_start' => $eventStart->format('Y-m-d H:i:s'),
                    'event_finish' => $eventFinish->format('Y-m-d H:i:s'),
                    'stand_slots' => [
                        [
                            'airport' => 'EGLL',
                            'stand' => '1L',
                            'slot_reservations' => [
                                [
                                    'callsign' => 'SBI24',
                                    'slotstart' => $eventStart->format('Y-m-d H:i:s'),
                                    'slotend' => $eventStart->copy()->addMinutes(30)->format('Y-m-d H:i:s'),
                                ],
                            ],
                        ],
                    ],
                ]),
            ])
            ->call('submitPlan')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
            'approval_due_at' => $eventStart->copy()->subDay()->format('Y-m-d H:i:s'),
        ]);
    }


    public function testItRejectsPlanWithoutEventStart()
    {
        $this->assumeRole(RoleKeys::VAA);

        Livewire::test(StandReservationPlans::class)
            ->fillForm([
                'name' => 'Missing Event Start',
                'contactEmail' => 'ops@example.com',
                'planJson' => json_encode([
                    'event_finish' => now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
                    'stand_slots' => [
                        [
                            'airport' => 'EGLL',
                            'stand' => '1L',
                            'slot_reservations' => [
                                [
                                    'callsign' => 'SBI24',
                                    'slotstart' => now()->addDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
                                    'slotend' => now()->addDay()->setTime(9, 30)->format('Y-m-d H:i:s'),
                                ],
                            ],
                        ],
                    ],
                ]),
            ])
            ->call('submitPlan')
            ->assertHasErrors(['data.planJson']);

        $this->assertDatabaseMissing('stand_reservation_plans', [
            'name' => 'Missing Event Start',
            'contact_email' => 'ops@example.com',
        ]);
    }

    public function testItRejectsPlanWithEventStartBeforeToday()
    {
        $this->assumeRole(RoleKeys::VAA);

        Livewire::test(StandReservationPlans::class)
            ->fillForm([
                'name' => 'Past Event Plan',
                'contactEmail' => 'ops@example.com',
                'planJson' => json_encode([
                    'event_start' => now()->subDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
                    'event_finish' => now()->subDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
                    'stand_slots' => [
                        [
                            'airport' => 'EGLL',
                            'stand' => '1L',
                            'slot_reservations' => [
                                [
                                    'callsign' => 'SBI24',
                                    'slotstart' => now()->subDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
                                    'slotend' => now()->subDay()->setTime(9, 30)->format('Y-m-d H:i:s'),
                                ],
                            ],
                        ],
                    ],
                ]),
            ])
            ->call('submitPlan')
            ->assertHasErrors(['data.planJson']);

        $this->assertDatabaseMissing('stand_reservation_plans', [
            'name' => 'Past Event Plan',
            'contact_email' => 'ops@example.com',
        ]);
    }

    public function testItRejectsPlanJsonThatFailsSchemaValidation()
    {
        $this->assumeRole(RoleKeys::VAA);

        Livewire::test(StandReservationPlans::class)
            ->fillForm([
                'name' => 'Speedbird 24',
                'contactEmail' => 'ops@example.com',
                'planJson' => json_encode([
                    'event_start' => '2026-02-20 09:00:00',
                    'event_finish' => '2026-02-20 10:00:00',
                ]),
            ])
            ->call('submitPlan')
            ->assertHasErrors(['data.planJson']);

        $this->assertDatabaseMissing('stand_reservation_plans', [
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
        ]);
    }

    public function testItApprovesPlanAndCreatesReservations()
    {
        $this->assumeRole(RoleKeys::OPERATIONS_TEAM);

        $plan = StandReservationPlan::create([
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->addDay()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '1L',
                        'slot_reservations' => [
                            [
                                'callsign' => 'SBI24',
                                'slotstart' => '2024-08-11 09:00:00',
                                'slotend' => '2024-08-11 09:30:00',
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        Livewire::test(StandReservationPlans::class)
            ->callTableAction('approve', $plan)
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


    public function testItApprovesPlanAfterApprovalDueDate()
    {
        $this->assumeRole(RoleKeys::OPERATIONS_TEAM);

        $plan = StandReservationPlan::create([
            'name' => 'Late Approval Plan',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->addDay()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '1L',
                        'slot_reservations' => [
                            [
                                'callsign' => 'SBI25',
                                'slotstart' => '2024-08-11 10:00:00',
                                'slotend' => '2024-08-11 10:30:00',
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->subDay(),
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        Livewire::test(StandReservationPlans::class)
            ->callTableAction('approve', $plan)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'approved',
            'approved_by' => self::ACTIVE_USER_CID,
            'imported_reservations' => 1,
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

        Livewire::test(StandReservationPlans::class)
            ->callTableAction('deny', $plan)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'denied',
            'denied_by' => self::ACTIVE_USER_CID,
        ]);
    }

    public function testItMarksPlanExpiredWhenApprovingOnOrAfterEventDay()
    {
        $this->assumeRole(RoleKeys::OPERATIONS_TEAM);

        $plan = StandReservationPlan::create([
            'name' => 'Too Late Plan',
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
                                'callsign' => 'SBI26',
                                'slotstart' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
                                'slotend' => now()->addMinutes(10)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        Livewire::test(StandReservationPlans::class)
            ->callTableAction('approve', $plan)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'expired',
            'denied_by' => null,
        ]);

        $this->assertDatabaseMissing('stand_reservations', [
            'callsign' => 'SBI26',
        ]);
    }


    public function testItAutoExpiresPendingPlanWhenEventDayStarts()
    {
        $this->assumeRole(RoleKeys::OPERATIONS_TEAM);

        $plan = StandReservationPlan::create([
            'name' => 'Event Day Plan',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->endOfDay()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addDay()->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '1L',
                        'slot_reservations' => [
                            [
                                'callsign' => 'SBI30',
                                'slotstart' => now()->endOfDay()->subHour()->format('Y-m-d H:i:s'),
                                'slotend' => now()->endOfDay()->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->subDay(),
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        Livewire::test(StandReservationPlans::class)
            ->assertOk();

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'expired',
            'denied_by' => null,
        ]);
    }

    public function testItShowsVaaOnlyTheirOwnPlans()
    {
        $this->assumeRole(RoleKeys::VAA);

        $ownPlan = StandReservationPlan::create([
            'name' => 'Own Plan',
            'contact_email' => 'vaa@example.com',
            'payload' => ['reservations' => []],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => self::ACTIVE_USER_CID,
        ]);

        $otherPlan = StandReservationPlan::create([
            'name' => 'Other Plan',
            'contact_email' => 'ops@example.com',
            'payload' => ['reservations' => []],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => 1,
        ]);

        Livewire::test(StandReservationPlans::class)
            ->assertCanSeeTableRecords([$ownPlan])
            ->assertCanNotSeeTableRecords([$otherPlan]);
    }

    public function testItAllowsTechToViewAllPlansButNotReview()
    {
        $this->assumeRole(RoleKeys::WEB_TEAM);

        $plan = StandReservationPlan::create([
            'name' => 'Pending Plan',
            'contact_email' => 'ops@example.com',
            'payload' => ['reservations' => []],
            'approval_due_at' => now()->addDays(7),
            'status' => 'pending',
            'submitted_by' => 1,
        ]);

        Livewire::test(StandReservationPlans::class)
            ->assertCanSeeTableRecords([$plan])
            ->assertTableActionHidden('approve', $plan)
            ->assertTableActionHidden('deny', $plan);
    }

}
