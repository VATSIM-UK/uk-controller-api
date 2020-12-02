<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ScottishMslCalculations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create Antrim TMA
        DB::table('tma')->insert(
            [
                'name' => 'ATMA',
                'description' => 'Antrim TMA',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_airfield_id' => DB::table('airfield')->where('code', 'EGAA')->first()->id,
                'created_at' => Carbon::now(),
            ]
        );

        // Set standard high for Aberdeen and Sumburgh
        DB::table('airfield')->whereIn('code', ['EGPD', 'EGPB'])->update(['standard_high' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing to do
    }
}
