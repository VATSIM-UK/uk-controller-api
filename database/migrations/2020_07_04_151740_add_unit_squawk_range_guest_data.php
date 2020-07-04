<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUnitSquawkRangeGuestData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('unit_squawk_range_guests')->insert(
            [
                [
                    'primary_unit' => 'ESSEX',
                    'guest_unit' => 'EGSS',
                    'created_at' => Carbon::now(),
                ],
                [
                    'primary_unit' => 'ESSEX',
                    'guest_unit' => 'EGGW',
                    'created_at' => Carbon::now(),
                ],
                [
                    'primary_unit' => 'THAMES',
                    'guest_unit' => 'EGLL',
                    'created_at' => Carbon::now(),
                ],
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
        DB::table('unit_squawk_range_guests')->delete();
    }
}
