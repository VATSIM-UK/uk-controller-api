<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GlasgowJet2Stands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $jet2 = DB::table('airlines')->where('icao_code', 'EXS')->first()->id;
        DB::table('airline_stand')
            ->join('stands', 'stands.id', '=', 'airline_stand.stand_id')
            ->join('airfield', 'stands.airfield_id', '=', 'airfield.id')
            ->join('airlines', 'airline_stand.airline_id', '=', 'airlines.id')
            ->where('airfield.code', 'EGPF')
            ->where('airlines.icao_code', 'ESX')
            ->update(['airline_stand.airline_id' => $jet2]);
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
