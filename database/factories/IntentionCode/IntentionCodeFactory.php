<?php

namespace Database\Factories\IntentionCode;

use App\Models\IntentionCode\IntentionCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntentionCodeFactory extends Factory
{
    protected $model = IntentionCode::class;

    const CODES = [
        'D1',
        'D2',
        'LL',
        'DW',
        'C2',
    ];

    const CONDITIONS = [
        [
            'type' => 'arrival_airfields',
            'airfields' => ['EGLL'],
        ],
        [
            'type' => 'arrival_airfield_pattern',
            'pattern' => 'EG',
        ],
        [
            'exit_point' => 'ETRAT',
            'exit_direction' => [90, 111],
        ],
    ];

    public function definition(): array
    {
        return [
            'code' => [
                'type' => 'single_code',
                'code' => $this->faker->unique()->randomElement(self::CODES),
            ],
            'conditions' => [
                $this->faker->unique()->randomElement(self::CONDITIONS)
            ],
            'priority' => $this->faker->unique()->numberBetween(0, 999),
        ];
    }
}
