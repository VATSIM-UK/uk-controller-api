<?php

namespace Database\Factories\Vatsim;

use App\Models\Controller\ControllerPosition;
use App\Models\User\User;
use App\Models\Vatsim\NetworkControllerPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class NetworkControllerPositionFactory extends Factory
{
    protected $model = NetworkControllerPosition::class;

    private const FREQUENCIES = [
        123.450,
        129.425,
        133.800,
        126.075,
        118.575,
        120.200,
    ];

    public function definition()
    {
        return [
            'cid' => $this->faker->numberBetween(800000, 1900000),
            'callsign' => $this->faker->unique()->word(),
            'frequency' => $this->faker->randomElement(self::FREQUENCIES),
            'controller_position_id' => null,
        ];
    }

    public function withControllerPosition(int|ControllerPosition $position): static
    {
        $position = is_int($position) ? ControllerPosition::findOrFail($position) : $position;

        return $this->state(fn(array $attributes) => [
            'callsign' => $position->callsign,
            'frequency' => $position->frequency,
            'controller_position_id' => $position->id,
        ]);
    }

    public function asUser(int|User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'cid' => is_int($user) ? $user : $user->id,
        ]);
    }
}
