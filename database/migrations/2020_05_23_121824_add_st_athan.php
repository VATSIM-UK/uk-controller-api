<?php

use App\Services\AirfieldService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddStAthan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the airfield
        DB::table('airfield')->insert(
            [
                'code' => 'EGSY',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'lowest', 'airfields' => ['EGGD', 'EGFF']]),
                'created_at' => Carbon::now(),
            ]
        );

        // Create the tower position
        DB::table('controller_positions')->insert(
            [
                'callsign' => 'EGSY_TWR',
                'frequency' => 122.87,
                'created_at' => Carbon::now(),
            ]
        );

        // Create the top down
        AirfieldService::createNewTopDownOrder(
            'EGSY',
            [
                'EGSY_TWR',
                'EGFF_R_APP',
                'LON_W_CTR',
                'LON_CTR',
            ]
        );

        // Add local squawk range
        $rangeOwner = DB::table('squawk_range_owner')->insertGetId([]);
        $unit = DB::table('squawk_unit')->insert(['unit' => 'EGSY', 'squawk_range_owner_id' => $rangeOwner]);
        DB::table('squawk_range')->insert(
            [
                'squawk_range_owner_id' => $rangeOwner,
                'start' => '3646',
                'stop' => '3657',
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
        $rangeOwner = DB::table('squawk_unit')->where('unit', 'EGSY')->pluck('id')->first();
        DB::table('squawk_range_owner')->where('id', $rangeOwner)->delete();
        DB::table('airfield')->where('code', 'EGSY')->delete();
        DB::table('controller_positions')->where('callsign', 'EGSY_TWR')->delete();
    }
}
