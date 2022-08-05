<?php

namespace Database\Factories\Vatsim;

use App\Models\User\User;
use App\Models\Vatsim\NetworkAircraft;
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
            'callsign' => $this->faker->unique()->word,
            'transponder_last_updated_at' => null,
        ];
    }

    public function asUser(int|User $user): static
    {
        return $this->state(fn(array $attributes) => [
            'cid' => is_int($user) ? $user : $user->id,
        ]);
    }
}
