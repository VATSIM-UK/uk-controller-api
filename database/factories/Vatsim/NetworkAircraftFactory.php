<?php

namespace Database\Factories\Vatsim;

use App\Models\Vatsim\NetworkAircraft;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetworkAircraftFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NetworkAircraft::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'callsign' => $this->faker->word,
            'transponder_last_updated_at' => Carbon::now(),
        ];
    }
}
