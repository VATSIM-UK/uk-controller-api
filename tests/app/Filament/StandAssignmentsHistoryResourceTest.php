<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\StandAssignmentsHistoryResource;
use App\Filament\Resources\StandAssignmentsHistoryResource\Pages\ListStandAssignmentsHistories;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignmentsHistory;
use App\Models\User\RoleKeys;
use Carbon\Carbon;
use Livewire\Livewire;

class StandAssignmentsHistoryResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;
    use ChecksHasRoleFilamentActionVisibility;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }

    public function testItAllowsFilteredResultsByCallsign()
    {
        $item1 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => Stand::factory()->create()->id,
            ]
        );

        $item2 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW999',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => Stand::factory()->create()->id,
            ]
        );

        $item3 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => Stand::factory()->create()->id,
            ]
        );

        // Test filter before and after
        Livewire::test(ListStandAssignmentsHistories::class)
            ->assertCanSeeTableRecords([$item1, $item2, $item3])
            ->filterTable('callsign', ['isActive' => 'BAW123'])
            ->assertCanSeeTableRecords([$item1, $item3])
            ->assertCanNotSeeTableRecords([$item2]);
    }

    public function testItAllowsFilteredResultsByAirfield()
    {
        $airfield1 = Airfield::factory()->create();
        $airfield2 = Airfield::factory()->create();

        $item1 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => Stand::factory()->create(['airfield_id' => $airfield1->id])->id,
            ]
        );

        $item2 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW999',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => Stand::factory()->create(['airfield_id' => $airfield2->id])->id,
            ]
        );

        $item3 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW777',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => Stand::factory()->create(['airfield_id' => $airfield1->id])->id,
            ]
        );

        // Test filter before and after
        Livewire::test(ListStandAssignmentsHistories::class)
            ->assertCanSeeTableRecords([$item1, $item2, $item3])
            ->filterTable('airfield_and_stand', ['airfield' => $airfield1->id])
            ->assertCanSeeTableRecords([$item1, $item3])
            ->assertCanNotSeeTableRecords([$item2]);
    }

    public function testItAllowsFilteredResultsByStand()
    {
        $stand1 = Stand::factory()->create(['airfield_id' => 1]);
        $stand2 = Stand::factory()->create(['airfield_id' => 1]);

        $item1 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW123',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => $stand1->id,
            ]
        );

        $item2 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW999',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => $stand2->id,
            ]
        );

        $item3 = StandAssignmentsHistory::create(
            [
                'callsign' => 'BAW777',
                'assigned_at' => Carbon::now()->subDays(1),
                'type' => 'TEST',
                'context' => [],
                'stand_id' => $stand1->id,
            ]
        );

        // Test filter before and after
        Livewire::test(ListStandAssignmentsHistories::class)
            ->assertCanSeeTableRecords([$item1, $item2, $item3])
            ->filterTable('airfield_and_stand', ['airfield' => 1, 'stand' => $stand1->id])
            ->assertCanSeeTableRecords([$item1, $item3])
            ->assertCanNotSeeTableRecords([$item2]);
    }

    protected function getIndexText(): array
    {
        return ['Stand Assignment Histories'];
    }

    protected static function resourceClass(): string
    {
        return StandAssignmentsHistoryResource::class;
    }

    protected static function readOnlyResourceTableActions(): array
    {
        return ['view_context'];
    }

    protected static function resourceRecordClass(): string
    {
        return StandAssignmentsHistory::class;
    }

    protected static function resourceListingClass(): string
    {
        return ListStandAssignmentsHistories::class;
    }

    protected static function resourceId(): int|string|callable
    {
        return fn() => StandAssignmentsHistory::create(
            [
                'stand_id' => Stand::factory()->create()->id,
                'callsign' => 'BAW123',
                'assigned_at' => Carbon::now(),
                'type' => 'TEST',
                'context' => ['abc'],
            ]
        )->id;
    }

    protected function beforeListing(): void
    {
        StandAssignmentsHistory::create(
            [
                'stand_id' => Stand::factory()->create()->id,
                'callsign' => 'BAW123',
                'assigned_at' => Carbon::now(),
                'type' => 'TEST',
                'context' => ['abc'],
            ]
        );
    }

    public static function indexRoleProvider(): array
    {
        return [
            'None' => [null, false],
            'Contributor' => [RoleKeys::OPERATIONS_CONTRIBUTOR, true],
            'DSG' => [RoleKeys::DIVISION_STAFF_GROUP, true],
            'Web' => [RoleKeys::WEB_TEAM, true],
            'Operations' => [RoleKeys::OPERATIONS_TEAM, true],
        ];
    }
}
