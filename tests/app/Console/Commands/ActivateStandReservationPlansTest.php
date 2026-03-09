<?php

namespace App\Console\Commands;

use App\BaseFunctionalTestCase;
use App\Imports\Stand\StandReservationsImport as Importer;
use App\Models\Stand\StandReservationPlan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Mockery;

class ActivateStandReservationPlansTest extends BaseFunctionalTestCase
{
    private $mockImporter;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockImporter = Mockery::mock(Importer::class);
        $this->app->instance(Importer::class, $this->mockImporter);
    }

    public function testItImportsApprovedPlansWhenEventStartHasBegun(): void
    {
        $plan = StandReservationPlan::create([
            'name' => 'Speedbird 24',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->subHour()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addHours(2)->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '531',
                        'slot_reservations' => [
                            [
                                'callsign' => 'BAW1234',
                                'slotstart' => now()->subMinutes(30)->format('Y-m-d H:i:s'),
                                'slotend' => now()->addMinutes(10)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(2),
            'approved_at' => now()->subDay(),
            'status' => 'approved',
            'imported_reservations' => null,
        ]);

        $this->mockImporter->shouldReceive('importReservations')
            ->once()
            ->with(Mockery::on(fn (Collection $rows): bool => $rows->count() === 1 && $rows->first()->get('stand') === '531'))
            ->andReturn(1);

        $this->assertEquals(0, Artisan::call('stand-reservations:activate-plans'));

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'imported_reservations' => 1,
        ]);
    }

    public function testItSkipsPlansWithFutureEventStart(): void
    {
        $plan = StandReservationPlan::create([
            'name' => 'Future Event',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->addDay()->format('Y-m-d H:i:s'),
                'event_finish' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '531',
                        'slot_reservations' => [
                            [
                                'callsign' => 'BAW9999',
                                'slotstart' => now()->addDay()->format('Y-m-d H:i:s'),
                                'slotend' => now()->addDay()->addMinutes(30)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(2),
            'approved_at' => now()->subDay(),
            'status' => 'approved',
            'imported_reservations' => null,
        ]);

        $this->mockImporter->shouldReceive('importReservations')->never();

        $this->assertEquals(0, Artisan::call('stand-reservations:activate-plans'));

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'imported_reservations' => null,
        ]);
    }

    public function testItSystemDeniesPendingPlansAfterEventStart(): void
    {
        $plan = StandReservationPlan::create([
            'name' => 'Expired Approval',
            'contact_email' => 'ops@example.com',
            'payload' => [
                'event_start' => now()->subMinutes(10)->format('Y-m-d H:i:s'),
                'event_finish' => now()->addHour()->format('Y-m-d H:i:s'),
                'stand_slots' => [
                    [
                        'airport' => 'EGLL',
                        'stand' => '531',
                        'slot_reservations' => [
                            [
                                'callsign' => 'BAW1000',
                                'slotstart' => now()->subMinutes(5)->format('Y-m-d H:i:s'),
                                'slotend' => now()->addMinutes(25)->format('Y-m-d H:i:s'),
                            ],
                        ],
                    ],
                ],
            ],
            'approval_due_at' => now()->addDays(2),
            'status' => 'pending',
        ]);

        $this->mockImporter->shouldReceive('importReservations')->never();

        $this->assertEquals(0, Artisan::call('stand-reservations:activate-plans'));

        $this->assertDatabaseHas('stand_reservation_plans', [
            'id' => $plan->id,
            'status' => 'denied',
            'denied_by' => StandReservationPlan::AUTOMATION_DENIED_BY_USER_ID,
            'denied_reason' => StandReservationPlan::AUTOMATION_EVENT_STARTED_PRIOR_TO_APPROVAL_REASON,
        ]);
    }
}
