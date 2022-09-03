<?php

namespace Database\Factories\Notification;

use App\Models\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'body' => $this->faker->sentences(5, true),
            'link' => $this->faker->url(),
            'valid_from' => Carbon::now()->subHour(),
            'valid_to' => Carbon::now()->addHour(),
        ];
    }

    public function notStarted(): static
    {
        return $this->state(fn (array $values) => [
            'valid_from' => Carbon::now()->addMinutes(30),
            'valid_to' => Carbon::now()->addDay(),
        ]);
    }

    public function finished(): static
    {
        return $this->state(fn (array $values) => [
            'valid_from' => Carbon::now()->subHours(30),
            'valid_to' => Carbon::now()->subMinutes(10),
        ]);
    }
}
