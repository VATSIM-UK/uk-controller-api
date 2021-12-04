<?php

namespace Database\Factories\Metars;

use App\Models\Airfield\Airfield;
use App\Models\Metars\Metar;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetarFactory extends Factory
{
    const VALID_METARS = [
        '041450Z AUTO 26015KT 9999 BKN036 08/01 Q0998',
        '041450Z AUTO 29019KT 9999 BKN030 BKN046 07/03 Q1000',
        '041450Z 33010KT 9999 VCSH SCT020CB FEW025 04/02 Q0998',
        '041350Z AUTO 26019KT 9999 SCT032/// 07/01 Q0998',
        '041450Z 27010G20KT 9999 FEW030 07/M00 Q0998 NOSIG RMK BLU BLU',
    ];

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Metar::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $airfield = Airfield::factory()->create();

        return [
            'airfield_id' => $airfield->id,
            'raw' => sprintf('%s %s', $airfield->code, $this->faker->randomElement(self::VALID_METARS)),
            'parsed' => $this->faker->randomElements(),
        ];
    }
}
