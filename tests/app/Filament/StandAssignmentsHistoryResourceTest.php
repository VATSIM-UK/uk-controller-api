<?php

namespace App\Filament;

use App\BaseFilamentTestCase;
use App\Filament\AccessCheckingHelpers\ChecksListingFilamentAccess;
use App\Filament\Resources\StandAssignmentsHistoryResource;
use App\Models\Stand\Stand;
use App\Models\Stand\StandAssignmentsHistory;
use Carbon\Carbon;

class StandAssignmentsHistoryResourceTest extends BaseFilamentTestCase
{
    use ChecksListingFilamentAccess;

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
