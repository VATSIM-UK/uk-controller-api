<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddBigginSquawks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $rangeOwner = DB::table('squawk_range_owner')->insertGetId([]);
        DB::table('squawk_unit')->insert(
            [
                'unit' => 'EGKB',
                'squawk_range_owner_id' => $rangeOwner,
            ]
        );
        // Create the range
        DB::table('squawk_range')->insert(
            [
                'start' => '7047',
                'stop' => '7047',
                'rules' => 'A',
                'allow_duplicate' => true,
                'squawk_range_owner_id' => $rangeOwner,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $unit = DB::table('squawk_unit')->where('unit', 'EGKB')->select('squawk_range_owner_id')->first();
        DB::table('squawk_unit')->where('unit', 'EGKB')->delete();
        DB::table('squawk_range_owner')->where('id', $unit->squawk_range_owner_id)->delete();
    }
}
