<?php

namespace Database\Factories\Stand;

use App\Models\Airfield\Terminal;
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
            'aerodrome_reference_code' => 'F', // A380
            'assignment_priority' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function withTerminal(int|Terminal $terminal): static
    {
        return $this->state(fn (array $attributes) => [
            'airfield_id' => $terminal->airfield_id,
            'terminal_id' => is_int($terminal) ? $terminal : $terminal->id,
        ]);
    }

    private function standIdentifier(): string
    {
        while (true) {
            $stand = sprintf(
                '%d%s',
                $this->faker->unique()->numberBetween(0, 500),
                $this->faker->randomElement(['L', 'R', '', 'A']),
            );

            // These stands are in our seeders, so avoid them, there's bound to be another.
            if (!in_array($stand, ['1L', '251', '32')) {
                return $stand;
            }
        }
    }
}
