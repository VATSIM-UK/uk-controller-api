<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AirfieldPairingPrenotesTidy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('airfield_pairing_prenotes')
            ->whereNull('flight_rule_id')
            ->update(
                [
                    'flight_rule_id' => DB::table('flight_rules')->where('euroscope_key', 'I')->first()->id,
                    'updated_at' => Carbon::now()
                ]
            );
        DB::statement('ALTER TABLE airfield_pairing_prenotes MODIFY COLUMN flight_rule_id BIGINT UNSIGNED NOT NULL');
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
