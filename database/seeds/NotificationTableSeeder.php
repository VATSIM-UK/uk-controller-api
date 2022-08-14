<?php

use App\Models\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationTableSeeder extends Seeder
{
    public function run()
    {
        Notification::create(
            [
                'title' => 'Foo',
                'body' => 'Bar',
                'link' => 'https://vatsim.uk',
                'valid_from' => Carbon::now()->subHour(),
                'valid_to' => Carbon::now()->addHour(),
            ]
        );
    }
}
