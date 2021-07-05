<?php

namespace Database\Factories\Prenote;

use App\Models\Controller\ControllerPosition;
use App\Models\Prenote\PrenoteMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use TestingUtils\Traits\WithSeedUsers;

class PrenoteMessageFactory extends Factory
{
    use WithSeedUsers;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PrenoteMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'callsign' => 'BAW123',
            'departure_airfield' => $this->faker->unique()->word(),
            'user_id' => $this->activeUser()->id,
            'controller_position_id' => ControllerPosition::factory()->create()->id,
            'target_controller_position_id' => ControllerPosition::factory()->create()->id,
            'expires_at' => Carbon::now()->addMinutes($this->faker->randomDigit()),
        ];
    }
}
