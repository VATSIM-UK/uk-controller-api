<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlightRuleIdToAirfieldPairingPrenotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airfield_pairing_prenotes', function (Blueprint $table) {
            $table->unsignedBigInteger('flight_rule_id')
                ->after('prenote_id')
                ->nullable()
                ->comment('The type of flight rules that this applies to');

            $table->foreign('flight_rule_id', 'airfield_pairing_flight_rules')
                ->references('id')
                ->on('flight_rules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airfield_pairing_prenotes', function (Blueprint $table) {
            //
        });
    }
}
