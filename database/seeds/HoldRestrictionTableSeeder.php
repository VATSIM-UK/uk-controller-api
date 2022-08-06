<?php

use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use Illuminate\Database\Seeder;

class HoldRestrictionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Hold::findOrFail(1)
            ->restrictions()
            ->save(HoldRestriction::factory()->make());
    }
}
