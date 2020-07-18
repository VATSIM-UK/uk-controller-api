<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateCambridgeSquawkRange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rangeOwnerId = DB::table('squawk_unit')->where('unit', 'EGSC')
            ->select(['squawk_range_owner_id'])
            ->first()
            ->squawk_range_owner_id;

        DB::table('squawk_range')->where('squawk_range_owner_id', $rangeOwnerId)->delete();

        DB::table('squawk_range')->insert(
            [
                'squawk_range_owner_id' => $rangeOwnerId,
                'start' => '6160',
                'stop' => '6175',
                'rules' => 'A',
                'allow_duplicate' => false,
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
        $rangeOwnerId = DB::table('squawk_unit')->where('unit', 'EGSC')
            ->select(['squawk_range_owner_id'])
            ->first()
            ->squawk_range_owner_id;

        DB::table('squawk_range')->where('squawk_range_owner_id', $rangeOwnerId)->delete();

        DB::table('squawk_range')->insert(
            [
                [
                    'squawk_range_owner_id' => $rangeOwnerId,
                    'start' => '6160',
                    'stop' => '6176',
                    'rules' => 'A',
                    'allow_duplicate' => false,
                ],
                [
                    'squawk_range_owner_id' => $rangeOwnerId,
                    'start' => '6171',
                    'stop' => '6177',
                    'rules' => 'A',
                    'allow_duplicate' => false,
                ],
            ]
        );
    }
}
