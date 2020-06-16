<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class BriCdfHoldLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $bristol = DB::table('hold')->where('fix', 'BRI')->get()[0];
        $cardiff = DB::table('hold')->where('fix', 'CDF')->get()[0];

        $bristolRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL',
            'target' => 'EGGD',
        ];

        DB::table('hold_restriction')->where('hold_id', $bristol->id)
            ->update(['restriction' => json_encode($bristolRestriction)]);


        $cardiffRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL',
            'target' => 'EGFF',
        ];

        DB::table('hold_restriction')->where('hold_id', $cardiff->id)
            ->update(['restriction' => json_encode($cardiffRestriction)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $bristol = DB::table('hold')->where('fix', 'BRI')->get()[0];
        $cardiff = DB::table('hold')->where('fix', 'CDF')->get()[0];

        $bristolRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL+1',
            'target' => 'EGGD',
        ];

        DB::table('hold_restriction')->where('hold_id', $bristol->id)
            ->update(['restriction' => json_encode($bristolRestriction)]);

        $cardiffRestriction = [
            'type' => 'minimum_level',
            'level' => 'MSL+1',
            'target' => 'EGFF',
        ];

        DB::table('hold_restriction')->where('hold_id', $cardiff->id)
            ->update(['restriction' => json_encode($cardiffRestriction)]);
    }
}
