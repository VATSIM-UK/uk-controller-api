<?php

namespace Database\Factories\Controller;

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class ControllerPositionFactory extends Factory
{
    const CALLSIGNS = [
        'EGKK_GND',
        'EGKK_TWR',
        'EGLL_N_TWR',
        'EGGD_APP',
        'ESSEX_APP',
        'MAN_W_CTR',
        'LON_CTR',
        'SCO_CTR',
        'LON_W_CTR',
        'EGLL_DEL',
        'EGSS_R_APP',
        'EGGX_FSS',
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ControllerPosition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'callsign' => $this->faker->unique()->randomElement(self::CALLSIGNS),
            'frequency' => 123.450,
        ];
    }
}
