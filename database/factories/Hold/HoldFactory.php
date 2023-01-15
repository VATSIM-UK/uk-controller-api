<?php

namespace Database\Factories\Hold;

use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hold::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'navaid_id' => Navaid::factory()->create()->id,
            'inbound_heading' => $this->faker->numberBetween(0, 360),
            'minimum_altitude' => $this->faker->numberBetween(7000, 24000),
            'maximum_altitude' => $this->faker->numberBetween(7000, 24000),
            'turn_direction' => $this->faker->randomElement(['left', 'right']),
            'outbound_leg_value' => $this->faker->randomFloat(1, 0.5, 100.5),
            'outbound_leg_unit' => 2,
            'description' => $this->faker->word
        ];
    }
}
