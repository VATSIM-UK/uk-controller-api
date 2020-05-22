<?php

use App\Models\Airfield\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewFarnboroughSids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create Farnborough (whoops)
        $farnborough = Airfield::where('code', 'EGLF')->firstOrFail()->id;

        // Create the new SIDs
        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $farnborough,
                    'identifier' => 'GWC2L',
                    'initial_altitude' => 3000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $farnborough,
                    'identifier' => 'GWC2F',
                    'initial_altitude' => 3000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $farnborough,
                    'identifier' => 'HAZEL2L',
                    'initial_altitude' => 3000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $farnborough,
                    'identifier' => 'HAZEL2F',
                    'initial_altitude' => 3000,
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
        DB::table('sid')->whereIn('identifier', ['GWC2L', 'GWC2F', 'HAZEL2L', 'HAZEL2F'])->delete();
    }
}
