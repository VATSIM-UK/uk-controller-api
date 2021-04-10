<?php

namespace Database\Factories\Plugin;

use App\Models\Model;
use App\Models\Airfield\Airfield;
use App\Models\Plugin\PluginEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PluginEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PluginEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event' => [
                'foo' => $this->faker->firstName(),
                'foo' => $this->faker->lastName(),
            ],
        ];
    }
}
