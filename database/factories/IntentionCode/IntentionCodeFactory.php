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
        ]
    ];

    public function definition(): array
    {
        return [
            'description' => 'A desc',
            'code' => [
                'type' => 'single_code',
                'code' => $this->faker->randomElement(self::CODES),
            ],
            'conditions' => [
                $this->faker->randomElement(self::CONDITIONS)
            ],
            'priority' => $this->faker->unique()->numberBetween(0, 99999),
        ];
    }
}
