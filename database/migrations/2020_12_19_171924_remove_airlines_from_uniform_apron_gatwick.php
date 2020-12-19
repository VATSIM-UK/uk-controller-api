<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveAirlinesFromUniformApronGatwick extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $standsToRemoveFromAirlines = DB::table('stands')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGKK')->first()->id)
            ->whereIn(
                'identifier',
                [
                    '230',
                    '230L',
                    '230R',
                    '231',
                    '231L',
                    '231R',
                    '232',
                    '232L',
                    '232R',
                    '233',
                    '233L',
                    '233R',
                    '234',
                    '234L',
                    '234R',
                    '235',
                    '235L',
                    '235R',
                ]
            )
            ->pluck('id')
            ->toArray();

        DB::table('airline_stand')
            ->whereIn('stand_id', $standsToRemoveFromAirlines)
            ->delete();
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
