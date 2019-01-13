<?php

namespace App\Services;

use App\BaseFunctionalTestCase;
use App\Models\Squawks\Allocation;
use App\Models\Squawks\AllocationHistory;
use App\Models\User\User;
use Carbon\Carbon;

class SquawkAllocationServiceTest extends BaseFunctionalTestCase
{
    /**
     * @var SquawkAllocationService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = $this->app->make(SquawkAllocationService::class);
        $this->actingAs(User::find(self::ACTIVE_USER_CID));
    }

    public function testItConstructs()
    {
        $this->assertInstanceOf(SquawkAllocationService::class, $this->service);
    }

    public function testCreateOrUpdateAllocationCreatesAnAllocation()
    {
        $this->notSeeInDatabase(
            'squawk_allocation',
            [
                'callsign' => 'BAW123AF',
            ]
        );

        Carbon::setTestNow(Carbon::now());
        $this->service->createOrUpdateAllocation(
            'BAW123AF',
            '1234'
        );

        $this->seeInDatabase(
            'squawk_allocation',
            [
                'callsign' => 'BAW123AF',
                'squawk' => '1234',
                'allocated_by' => self::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testCreateOrUpdateAllocationReturnsCreatedAllocation()
    {
        $this->notSeeInDatabase(
            'squawk_allocation',
            [
                'callsign' => 'BAW123AF',
            ]
        );

        $allocation = $this->service->createOrUpdateAllocation(
            'BAW123AF',
            '1234'
        );

        $this->assertTrue($allocation->isNewAllocation());
        $this->assertEquals('1234', $allocation->squawk());
    }

    public function testCreateOrUpdateAllocationUpdatesAnAllocation()
    {
        Allocation::create(
            [
                'callsign' => 'BAW123AF',
                'squawk' => '0000',
                'allocated_by' => self::DISABLED_USER_CID,
                'allocated_at' => '2000-01-01 00:00:01',
            ]
        );

        $this->service->createOrUpdateAllocation(
            'BAW123AF',
            '1234'
        );

        $this->seeInDatabase(
            'squawk_allocation',
            [
                'callsign' => 'BAW123AF',
                'squawk' => '1234',
                'allocated_by' => self::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()->toDateTimeString(),
            ]
        );
    }

    public function testCreateOrUpdateAllocationReturnsAllocationOnUpdated()
    {
        Allocation::create(
            [
                'callsign' => 'BAW123AF',
                'squawk' => '0000',
                'allocated_by' => self::DISABLED_USER_CID,
                'allocated_at' => '2000-01-01 00:00:01',
            ]
        );

        $allocation = $this->service->createOrUpdateAllocation(
            'BAW123AF',
            '1234'
        );

        $this->assertFalse($allocation->isNewAllocation());
        $this->assertEquals('1234', $allocation->squawk());
    }

    public function testItFiresOffAnAllocationEventToRecordHistory()
    {
        Carbon::setTestNow(Carbon::now());
        $this->service->createOrUpdateAllocation(
            'BAW123AF',
            '1234'
        );

        $this->seeInDatabase(
            'squawk_allocation_history',
            [
                'callsign' => 'BAW123AF',
                'squawk' => '1234',
                'allocated_by' => self::ACTIVE_USER_CID,
                'allocated_at' => Carbon::now()->toDateTimeString(),
                'new' => true,
            ]
        );
    }

    public function testDeleteOldHistoryDeletesAnythingOlderThanAllowedNumberOfMonths()
    {
        $this->seeInDatabase('squawk_allocation_history', ['callsign' => 'NAX123']);
        $this->seeInDatabase('squawk_allocation_history', ['callsign' => 'NAX456']);
        $this->service->deleteOldAuditHistory();
        $this->seeInDatabase('squawk_allocation_history', ['callsign' => 'NAX123']);
        $this->notSeeInDatabase('squawk_allocation_history', ['callsign' => 'NAX456']);
    }
}
