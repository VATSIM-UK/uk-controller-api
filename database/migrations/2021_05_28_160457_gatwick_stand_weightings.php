<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GatwickStandWeightings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gatwick = DB::table('airfield')->where('code', 'EGKK')->first()->id;

        // Update B772 stands to allow A359
        $b772 = DB::table('aircraft')->where('code', 'B772')->first();
        $a359 = DB::table('aircraft')->where('code', 'A359')->first();

        if ($b772 && $a359) {
            DB::table('stands')
                ->where('airfield_id', $gatwick)
                ->where('max_aircraft_id', $b772->id)
                ->update(['max_aircraft_id' => $a359->id]);
        }

        // Update 140-145 to max B753
        $b753 = DB::table('aircraft')->where('code', 'B753')->first();
        if ($b753) {
            DB::table('stands')
                ->where('airfield_id', $gatwick)
                ->whereIn('identifier', ['140', '141', '142', '143', '144', '145'])
                ->update(['max_aircraft_id' => $b753->id]);
        }
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
