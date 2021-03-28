<?php

namespace Database\Factories\Navigation;

use Illuminate\Support\Str;
use App\Models\Navigation\Navaid;
use Illuminate\Database\Eloquent\Factories\Factory;

class NavaidFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Navaid::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'identifier' => Str::upper($this->faker->unique()->lexify('???')),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude()
        ];
    }
}
