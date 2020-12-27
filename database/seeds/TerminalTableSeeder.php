<?php

use App\Models\Airfield\Terminal;
use Illuminate\Database\Seeder;

class TerminalTableSeeder extends Seeder
{
    public function run()
    {
        Terminal::create(
            [
                'airfield_id' => 1,
                'key' => 'T1',
                'description' => 'Terminal 1',
            ]
        );

        Terminal::create(
            [
                'airfield_id' => 1,
                'key' => 'T2',
                'description' => 'Terminal 2',
            ]
        );
    }
}
