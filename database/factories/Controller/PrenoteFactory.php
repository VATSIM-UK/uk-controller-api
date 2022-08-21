<?php

namespace Database\Factories\Controller;

use App\Models\Controller\Prenote;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrenoteFactory extends Factory
{
    protected $model = Prenote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'description' => $this->faker->unique()->sentence(),
        ];
    }
}
