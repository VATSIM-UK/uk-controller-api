<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\StandAssignmentsHistoryResource;
use App\Filament\Resources\StandAssignmentsHistoryResource\Pages\ListStandAssignmentsHistories;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignmentsHistory;
use Carbon\Carbon;

class StandAssignmentsHistoryResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;
    use ChecksDefaultFilamentActionVisibility;

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now()->startOfSecond());
    }


    protected function getIndexText(): array
    {
        return ['Stand Assignment Histories'];
    }

    protected function resourceClass(): string
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
                'context' => [],
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
                'context' => [],
            ]
        );
    }
}
