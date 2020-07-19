<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Events\GroundStatusAssignedEvent;
use App\Models\GroundStatus\GroundStatus;
use App\Models\Vatsim\NetworkAircraft;

class GroundStatusControllerTest extends BaseApiTestCase
{
    const GROUND_STATUS_UPDATE_URI = 'ground-status/BAW123';
    
    public function testItReturnsGroundStatusDependency()
    {
        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'ground-status/dependency')
            ->assertStatus(200)
            ->assertJson(GroundStatus::all()->toArray());
    }

    public function testItReturnsAllAircraftGroundStatuses()
    {
        $this->addGroundStatus('BAW123', 1);
        $this->addGroundStatus('BAW456', 4);

        $this->makeUnauthenticatedApiRequest(self::METHOD_GET, 'ground-status/')
            ->assertStatus(200)
            ->assertJson(
                [
                    'BAW123' => 1,
                    'BAW456' => 4,
                ]
            );
    }

    public function testItSetsGroundStatus()
    {
        $this->expectsEvents(GroundStatusAssignedEvent::class);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::GROUND_STATUS_UPDATE_URI,
            [
                'ground_status_id' => 4,
            ]
        )->assertStatus(201);

        $this->assertEquals(4, NetworkAircraft::find('BAW123')->groundStatus->first()->id);
    }

    public function testItUpdatesExistingGroundStatus()
    {
        $this->addGroundStatus('BAW123', 1);
        $this->expectsEvents(GroundStatusAssignedEvent::class);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::GROUND_STATUS_UPDATE_URI,
            [
                'ground_status_id' => 4,
            ]
        )->assertStatus(201);

        $this->assertEquals(4, NetworkAircraft::find('BAW123')->groundStatus->first()->id);
    }

    public function testItReturnsBadRequestNoStatusId()
    {
        $this->doesntExpectEvents(GroundStatusAssignedEvent::class);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::GROUND_STATUS_UPDATE_URI,
            [
            ]
        )->assertStatus(400);
    }

    public function testItReturnsBadRequestStatusIdNotInt()
    {
        $this->doesntExpectEvents(GroundStatusAssignedEvent::class);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::GROUND_STATUS_UPDATE_URI,
            [
                'ground_status_id' => 'abc',
            ]
        )->assertStatus(400);
    }

    public function testItReturnsNotFoundInvalidStatusId()
    {
        $this->doesntExpectEvents(GroundStatusAssignedEvent::class);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            self::GROUND_STATUS_UPDATE_URI,
            [
                'ground_status_id' => -55,
            ]
        )->assertStatus(404);
    }

    public function testItReturnsUnprocessableIfCallsignNotFound()
    {
        $this->doesntExpectEvents(GroundStatusAssignedEvent::class);
        $this->makeAuthenticatedApiRequest(
            self::METHOD_PUT,
            'ground-status/NOTHERE',
            [
                'ground_status_id' => 1,
            ]
        )->assertStatus(422);
    }

    private function addGroundStatus(string $callsign, int $statusId)
    {
        NetworkAircraft::find($callsign)->groundStatus()->sync([$statusId]);
    }
}
