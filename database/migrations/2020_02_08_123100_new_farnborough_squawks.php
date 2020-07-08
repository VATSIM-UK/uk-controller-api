<?php

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
        $rangeOwnerId = DB::table('squawk_unit')->where('unit', 'EGLF')
            ->select(['squawk_range_owner_id'])
            ->first()
            ->squawk_range_owner_id;
        DB::table('squawk_range')
            ->where(
                [
                    'squawk_range_owner_id' => $rangeOwnerId,
                    'start' => '0430',
                    'stop' => '0446'
                ]
            )
            ->update(['stop' => '0456']);

        DB::table('squawk_range')
            ->where(
                [
                    'squawk_range_owner_id' => $rangeOwnerId,
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
        $rangeOwnerId = DB::table('squawk_unit')->where('unit', 'EGLF')
            ->select(['squawk_range_owner_id'])
            ->first()
            ->squawk_range_owner_id;
        DB::table('squawk_range')
            ->where(
                [
                    'squawk_range_owner_id' => $rangeOwnerId,
                    'start' => '0430',
                    'stop' => '0456'
                ]
            )
            ->update(['stop' => '0446']);

        DB::table('squawk_range')
            ->insert(
                [
                    'squawk_range_owner_id' => $rangeOwnerId,
                    'start' => '0450',
                    'stop' => '0456'
                ]
            );
    }
}
