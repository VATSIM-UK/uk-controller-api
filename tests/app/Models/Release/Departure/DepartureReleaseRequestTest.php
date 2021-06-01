<?php

namespace App\Models\Release\Departure;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class DepartureReleaseRequestTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::now());
    }

    public function testScopeActiveForIncludesActiveReleases()
    {
        DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinute(),
            ]
        );

        $this->assertTrue(DepartureReleaseRequest::activeFor('EZY890')->exists());
    }

    public function testScopeActiveForIgnoresWrongCallsign()
    {
        DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinute(),
            ]
        );

        $this->assertFalse(DepartureReleaseRequest::activeFor('EZY891')->exists());
    }

    public function testScopeActiveForIgnoresExpiredRequests()
    {
        DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->subMinute(),
            ]
        );

        $this->assertFalse(DepartureReleaseRequest::activeFor('EZY890')->exists());
    }

    public function testScopeActiveForIgnoresRejectedReleases()
    {
        $release = DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinute(),
            ]
        );
        $release->reject(self::ACTIVE_USER_CID);

        $this->assertFalse(DepartureReleaseRequest::activeFor('EZY890')->exists());
    }

    public function testScopeActiveForIgnoresExpiredApprovedReleases()
    {
        $release = DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinute(),
            ]
        );
        $release->released_at = Carbon::now()->subMinutes(2);
        $release->release_expires_at = Carbon::now()->subMinute();
        $release->save();


        $this->assertFalse(DepartureReleaseRequest::activeFor('EZY890')->exists());
    }

    public function testScopeActiveForIncludesActiveApprovedReleases()
    {
        $release = DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->addMinute(),
            ]
        );
        $release->released_at = Carbon::now()->subMinutes(2);
        $release->release_expires_at = Carbon::now()->addMinute();
        $release->save();

        $this->assertTrue(DepartureReleaseRequest::activeFor('EZY890')->exists());
    }

    public function testScopeActiveForIncludesActiveApprovedReleasesIfRequestHasExpired()
    {
        $release = DepartureReleaseRequest::create(
            [
                'callsign' => 'EZY890',
                'user_id' => self::ACTIVE_USER_CID,
                'controller_position_id' => 1,
                'target_controller_position_id' => 2,
                'expires_at' => Carbon::now()->subMinute(),
            ]
        );
        $release->released_at = Carbon::now()->subMinutes(2);
        $release->release_expires_at = Carbon::now()->addMinute();
        $release->save();

        $this->assertTrue(DepartureReleaseRequest::activeFor('EZY890')->exists());
    }
}
