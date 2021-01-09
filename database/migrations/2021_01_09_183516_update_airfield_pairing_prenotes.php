<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAirfieldPairingPrenotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('airfield_pairing_prenotes')
            ->update(
                ['flight_rule_id' => DB::table('flight_rules')->where('euroscope_key', 'I')->first()->id],
            );
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
