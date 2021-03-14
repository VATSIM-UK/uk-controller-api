<?php

namespace Database\Factories\Airfield;

use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\AppModelsAirfieldTerminal;
use Illuminate\Database\Eloquent\Factories\Factory;

class TerminalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Terminal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'airfield_id' => Airfield::factory()->create()->id,
            'key' => $this->faker->word(),
            'description' => $this->faker->sentence()
        ];
    }
}
