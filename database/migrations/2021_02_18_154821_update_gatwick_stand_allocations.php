<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateGatwickStandAllocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gatwick = DB::table('airfield')->where('code', 'EGKK')->first()->id;

        // Mark some overflow stands as not for general use
        DB::table('stands')
            ->where('airfield_id', $gatwick)
            ->whereIn(
                'identifier',
                ['41E', '41', '41W', '42', '43E', '43', '43W', '64', '64L', '64R', '65', '66', '66L', '66R', '67', '68']
            )
            ->update(['general_use' => false, 'updated_at' => Carbon::now()]);

        // Mark the cargo stands as cargo stands
        DB::table('stands')
            ->where('airfield_id', $gatwick)
            ->where('identifier', ['153', '154', '158', '159'])
            ->update(
                [
                    'type_id' => DB::table('stand_types')->where('key', 'CARGO')->first()->id,
                    'updated_at' => Carbon::now(),
                ]
            );

        // Remove airline allocations on remote stands
        $stands = DB::table('stands')
            ->where('airfield_id', $gatwick)
            ->whereIn(
                'identifier',
                [
                    '41E',
                    '41',
                    '41W',
                    '42',
                    '43E',
                    '43',
                    '43W',
                    '64L',
                    '64',
                    '64R',
                    '65',
                    '66L',
                    '66',
                    '66R',
                    '67',
                    '68',
                    '150L',
                    '150',
                    '150R',
                    '151',
                    '152L',
                    '152',
                    '152R',
                    '153',
                    '154',
                    '158',
                    '159',
                    '160L',
                    '160',
                    '160R',
                    '161',
                    '170',
                    '171L',
                    '171',
                    '171R',
                    '172L',
                    '172',
                    '172R',
                    '173',
                    '174',
                    '175L',
                    '175',
                    '175R',
                    '176L',
                    '176',
                    '176R',
                    '177',
                    '178',
                    '180',
                ]
            )->pluck('id')->toArray();

        DB::table('airline_stand')->whereIn('stand_id', $stands)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
