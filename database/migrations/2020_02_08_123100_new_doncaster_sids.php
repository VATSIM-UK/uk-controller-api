<?php

use App\Models\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewDoncasterSids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the new SIDs
        $doncaster = Airfield::where('code', 'EGCN')->first()->id;
        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $doncaster,
                    'identifier' => 'UPTON2A',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $doncaster,
                    'identifier' => 'UPTON2B',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $doncaster,
                    'identifier' => 'UPTON2C',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $doncaster,
                    'identifier' => 'ROGAG1A',
                    'initial_altitude' => 16000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $doncaster,
                    'identifier' => 'ROGAG1C',
                    'initial_altitude' => 16000,
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
        // Delete new SIDs
        DB::table('sid')->whereIn('identifier', ['UPTON2A', 'UPTON2B', 'UPTON2C', 'ROGAG1A', 'ROGAG1C'])->delete();
    }
}
