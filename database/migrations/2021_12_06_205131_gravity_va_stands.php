<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GravityVaStands extends Migration
{
    const STANDS = [
        '351',
        '353',
        '355',
        '357L',
        '357R',
        '340R',
        '340L',
        '313',
        '309',
        '311',
        '351',
        '353',
        '355',
        '357L',
        '357R',
        '323',
        '325',
        '327',
        '329',
        '331',
        '318',
        '364',
        '363',
        '321',
        '319',
        '317',
        '316',
        '320',
        '322',
        '365',
        '335',
        '342',
        '616',
        '615',
        '614',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Airline
        $airline = DB::table('airlines')
            ->insertGetId(
                [
                    'icao_code' => 'GVY',
                    'name' => 'Gravity Virtual Airlines',
                    'callsign' => 'GRAVITY',
                    'is_cargo' => false, // They do passengers too
                ]
            );

        $heathrowStands = DB::table('stands')
            ->where(
                'airfield_id',
                DB::table('airfield')
                    ->where('code', 'EGLL')
                    ->first()
                    ->id
            )->get()->mapWithKeys(function ($stand) {
                return [$stand->identifier => $stand->id];
            })->toArray();

        DB::table('airline_stand')
            ->insert(
                array_map(
                    function ($stand) use ($heathrowStands, $airline) {
                        return [
                            'stand_id' => $heathrowStands[$stand],
                            'airline_id' => $airline,
                            'created_at' => Carbon::now(),
                        ];
                    },
                    self::STANDS
                )
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
