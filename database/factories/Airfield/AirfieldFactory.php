<?php

namespace Database\Factories\Airfield;

use App\Models\Model;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Factories\Factory;

class AirfieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Airfield::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->lexify('EG??'),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'transition_altitude' => 3000,
            'standard_high' => 1,
        ];
    }
}
