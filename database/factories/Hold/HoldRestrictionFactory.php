<?php

namespace Database\Factories\Hold;

use App\Models\Hold\HoldRestriction;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoldRestrictionFactory extends Factory
{
    protected $model = HoldRestriction::class;

    public function definition()
    {
        return [
            'restriction' => [
                'type' => 'level-block',
                'levels' => $this->faker->unique()->randomElements(
                    [7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000],
                    2
                ),
            ],
        ];
    }

    public function withLevelBlockRestriction(array $levels): static
    {
        return $this->state(fn () => [
            'restriction' => [
                'type' => 'level-block',
                'levels' => $levels,
            ]
        ]);
    }

    public function withMinimumLevelRestriction(
        string $level,
        string $target,
        int $override = null,
        string $runway = null
    ): static {
        return $this->state(function () use ($level, $target, $override, $runway) {
            $data = [
                'type' => 'minimum-level',
                'level' => $level,
                'target' => $target,
            ];

            if ($override) {
                $data['override'] = $override;
            }

            if ($runway) {
                $data['runway'] = [
                    'designator' => $runway,
                    'type' => 'any',
                ];
            }

            return ['restriction' => $data];
        });
    }
}
