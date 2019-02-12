<?php

use App\Models\Hold\HoldProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HoldProfileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $holds = [
            [
                'name' => 'Hold Profile 1',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'Hold Profile 2',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        HoldProfile::insert($holds);
    }
}
