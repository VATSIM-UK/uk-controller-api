<?php

namespace Database\Factories\Airfield;

use App\Models\Model;
use App\Models\Airfield\Airfield;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AirfieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Airfield::class;

    private const SEEDED_AIRFIELDS = ['EGBB', 'EGKR', 'EGLL'];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->getValidAirfieldCode(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'transition_altitude' => 3000,
            'standard_high' => 1,
        ];
    }

    private function getValidAirfieldCode(): string
    {
        while (true) {
            if (!in_array($icao = $this->getAirfieldIcao(), self::SEEDED_AIRFIELDS)) {
                return $icao;
            }
        }
    }

    private function getAirfieldIcao(): string
    {
        return Str::upper($this->faker->lexify('EG??'));
    }
}
