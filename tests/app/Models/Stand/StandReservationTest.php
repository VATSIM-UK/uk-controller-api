<?php

namespace App\Models\Stand;

use App\BaseFunctionalTestCase;
use Carbon\Carbon;

class StandReservationTest extends BaseFunctionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        StandReservation::all()->each(function (StandReservation $standReservation) {
            $standReservation->delete();
        });
        Carbon::setTestNow(Carbon::now());
    }

    public function testItReturnsActiveReservations()
    {
        // Already ended
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now()->subMinutes(5),
            ]
        );

        // Just ended
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now(),
            ]
        );

        $aboutToEnd = StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now()->addSecond(),
            ]
        );
        $veryActive = StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now()->addMinutes(15),
            ]
        );
        $onlyJustStarted = StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now(),
                'end' => Carbon::now()->addMinutes(15),
            ]
        );

        // Not started
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->addSecond(),
                'end' => Carbon::now()->addMinutes(15),
            ]
        );

        $activeReservations = StandReservation::active()->pluck('id')->toArray();
        $this->assertEquals([$aboutToEnd->id, $veryActive->id, $onlyJustStarted->id], $activeReservations);
    }

    public function testItReturnsUpcomingReservations()
    {
        // Already started
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->subMinutes(10),
                'end' => Carbon::now()->addMinutes(55),
            ]
        );

        // Just started
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now(),
                'end' => Carbon::now()->addMinutes(5),
            ]
        );

        $aboutToStart = StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->addSecond(),
                'end' => Carbon::now()->addMinute(),
            ]
        );

        $littleWhileOff = StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->addMinutes(5),
                'end' => Carbon::now()->addMinutes(15),
            ]
        );

        $justInRange = StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->addMinutes(10),
                'end' => Carbon::now()->addMinutes(20),
            ]
        );

        // Too far off
        StandReservation::create(
            [
                'stand_id' => 1,
                'start' => Carbon::now()->addMinutes(10)->addSecond(),
                'end' => Carbon::now()->addMinutes(15),
            ]
        );

        $upcomingReservations = StandReservation::upcoming(Carbon::now()->addMinutes(10))->pluck('id')->toArray();
        $this->assertEquals([$aboutToStart->id, $littleWhileOff->id, $justInRange->id], $upcomingReservations);
    }
}
