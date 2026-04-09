<?php

use App\Models\Controller\ControllerPosition;
use App\Models\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NotificationTableSeeder extends Seeder
{
    public function run()
    {
        $notification = Notification::create(
            [
                'title' => 'Foo',
                'body' => 'Bar',
                'link' => 'https://vatsim.uk',
                'valid_from' => Carbon::now()->subHour(),
                'valid_to' => Carbon::now()->addHour(),
            ]
        );

        // Seed pivot data required for Filament relation manager tests
        $notification->controllers()->attach(ControllerPosition::findOrFail(1));
    }
}
