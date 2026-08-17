<?php

namespace Database\Factories\Stand;

use App\Models\Stand\Stand;
use App\Models\Stand\StandRequest;
use App\Models\User\User;
use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StandRequest>
 */
class StandRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stand_id' => Stand::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'callsign' => NetworkAircraft::factory()->create()->callsign,
            'requested_time' => Carbon::now()->addMinutes(30),
        ];
    }
}
