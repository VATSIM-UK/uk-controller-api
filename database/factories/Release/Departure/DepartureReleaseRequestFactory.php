<?php

namespace Database\Factories\Release\Departure;

use App\Models\Controller\ControllerPosition;
use App\Models\Release\Departure\DepartureReleaseRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use TestingUtils\Traits\WithSeedUsers;

class DepartureReleaseRequestFactory extends Factory
{
    use WithSeedUsers;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DepartureReleaseRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'callsign' => 'BAW123',
            'user_id' => $this->activeUser()->id,
            'controller_position_id' => ControllerPosition::factory()->create()->id,
            'target_controller_position_id' => ControllerPosition::factory()->create()->id,
            'expires_at' => Carbon::now()->addMinutes($this->faker->randomDigit()),
        ];
    }
}
