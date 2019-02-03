<?php

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
        HoldRestriction::create(
            [
                'hold_id' => 1,
                'restriction' => json_encode(['foo' => 'bar'])
            ]
        );
    }
}
