<?php

namespace Database\Factories\Plugin;

use App\Models\Plugin\PluginLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class PluginLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PluginLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => 'FATAL_EXCEPTION',
            'message' => $this->faker->sentence(),
            'metadata' => [
                'foo' => $this->faker->sentence(),
                'bar' => $this->faker->sentence(),
            ],
        ];
    }
}
