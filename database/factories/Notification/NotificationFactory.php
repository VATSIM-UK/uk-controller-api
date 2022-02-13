<?php

namespace Database\Factories\Notification;

use App\Models\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->sentences(3, true),
            'link' => $this->faker->url(),
            'valid_from' => Carbon::now(),
            'valid_to' => Carbon::now()->addHours(2),
        ];
    }

    public function expired()
    {
        return $this->state(fn() => [
            'valid_from' => Carbon::now()->subHours(3), 
            'valid_to' => Carbon::now()->subHour()
        ]);
    }
}
