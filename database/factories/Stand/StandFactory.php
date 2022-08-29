<?php

namespace Database\Factories\Stand;

use App\Models\Stand\Stand;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Factories\Factory;

class StandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Stand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'airfield_id' => Airfield::factory()->create()->id,
            'identifier' => $this->standIdentifier(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'wake_category_id' => 1,
            'assignment_priority' => $this->faker->numberBetween(0, 1000),
        ];
    }

    private function standIdentifier(): string
    {
        return sprintf(
            '%d%s',
            $this->faker->numberBetween(0, 500),
            $this->faker->randomElement(['L', 'R', '', 'A']),
        );
    }
}
