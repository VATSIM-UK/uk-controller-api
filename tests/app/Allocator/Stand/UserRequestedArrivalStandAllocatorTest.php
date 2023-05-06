<?php

namespace App\Allocator\Stand;

use App\BaseFunctionalTestCase;
use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Models\StandRequest;
use App\Models\User\User;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;

class UserRequestedArrivalStandAllocatorTest extends BaseFunctionalTestCase
{
    private readonly UserRequestedArrivalStandAllocator $allocator;
    private readonly NetworkAircraft $aircraft;
    private readonly User $user;

    private readonly Stand $stand;

    public function setUp(): void
    {
        parent::setUp();
        $this->allocator = $this->app->make(UserRequestedArrivalStandAllocator::class);
        $this->user = User::factory()->create();
        $airfield = Airfield::factory()->create();
        $this->aircraft = NetworkAircraft::factory()->create(
            ['callsign' => 'BAW242', 'cid' => $this->user->id, 'planned_destairport' => $airfield->code]
        );
        $this->stand = Stand::factory()->create(['airfield_id' => $airfield->id]);
    }

    public function testItReturnsThePilotRequestedStand()
    {
        $request = StandRequest::factory()->create(
            ['callsign' => $this->aircraft->callsign, 'user_id' => $this->user->id, 'stand_id' => $this->stand->id]
        );

        $this->assertEquals($this->stand->id, $this->allocator->allocate($request->aircraft));
    }

    public function testItDoesntReturnUnavailableStands()
    {
        $newAircraft = NetworkAircraft::factory()->create();
        $this->stand->occupier()->sync([$newAircraft->callsign]);

        $request = StandRequest::factory()->create(
            ['callsign' => $this->aircraft->callsign, 'user_id' => $this->user->id, 'stand_id' => $this->stand->id]
        );

        $this->assertNull($this->allocator->allocate($request->aircraft));
    }

    public function testItDoesntReturnStandsTooEarly()
    {
        $request = StandRequest::factory()->create(
            ['callsign' => $this->aircraft->callsign, 'user_id' => $this->user->id, 'stand_id' => $this->stand->id,
                'requested_time' => Carbon::now()->addMinutes(41)]
        );

        $this->assertNull($this->allocator->allocate($request->aircraft));
    }

    public function testItDoesntReturnStandsTooLate()
    {
        $request = StandRequest::factory()->create(
            ['callsign' => $this->aircraft->callsign, 'user_id' => $this->user->id, 'stand_id' => $this->stand->id,
                'requested_time' => Carbon::now()->subMinutes(21)]
        );

        $this->assertNull($this->allocator->allocate($request->aircraft));
    }

    public function testItDoesntReturnNonRequestedStands()
    {
        $request = StandRequest::factory()->create(
            ['callsign' => $this->aircraft->callsign, 'user_id' => $this->user->id, 'stand_id' => $this->stand->id]
        );
        $request->delete();

        $this->assertNull($this->allocator->allocate($request->aircraft));
    }
}
