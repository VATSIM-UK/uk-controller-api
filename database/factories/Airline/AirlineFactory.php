<?php

namespace Database\Factories\Airline;

use App\Models\Airline\Airline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AirlineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Airline::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'icao_code' => Str::upper($this->faker->unique()->lexify('???')),
            'name' => $this->faker->unique()->company(),
            'callsign' => $this->faker->unique()->word(),
            'is_cargo' => false,
        ];
    }
}
