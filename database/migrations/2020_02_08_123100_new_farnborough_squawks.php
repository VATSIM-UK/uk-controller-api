<?php

use App\Models\Airfield;
use App\Models\Squawks\SquawkUnit;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewFarnboroughSquawks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the ranges
        $farnborough = SquawkUnit::where('unit', 'EGLF')->firstOrFail()->squawk_range_owner_id;
        DB::table('squawk_range')
            ->where(
                [
                    'squawk_range_owner_id' => $farnborough,
                    'start' => '0430',
                    'stop' => '0446'
                ]
            )
            ->update(['stop' => '0456']);

        DB::table('squawk_range')
            ->where(
                [
                    'squawk_range_owner_id' => $farnborough,
                    'start' => '0450',
                    'stop' => '0456'
                ]
            )
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Update the ranges
        $farnborough = SquawkUnit::where('unit', 'EGLF')->firstOrFail()->squawk_range_owner_id;
        DB::table('squawk_range')
            ->where(
                [
                    'squawk_range_owner_id' => $farnborough,
                    'start' => '0430',
                    'stop' => '0456'
                ]
            )
            ->update(['stop' => '0446']);

        DB::table('squawk_range')
            ->insert(
                [
                    'squawk_range_owner_id' => $farnborough,
                    'start' => '0450',
                    'stop' => '0456'
                ]
            );
    }
}
