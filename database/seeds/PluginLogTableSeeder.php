<?php

use App\Models\Plugin\PluginLog;
use Illuminate\Database\Seeder;

class PluginLogTableSeeder extends Seeder
{
    public function run()
    {
        PluginLog::factory()->create();
    }
}
