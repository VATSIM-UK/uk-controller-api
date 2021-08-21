<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StanstedAirlineStandRearrangements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $stansted = DB::table('airfield')->where('code', 'EGSS')->first()->id;
        $allStanstedStands = DB::table('stands')
            ->where('airfield_id', $stansted)
            ->get()
            ->map(function ($stand) {
                return $stand->id;
            })
            ->toArray();

        /*
         * First, all the non L/R stands, lets adjust their priority so the L/Rs are always preferred and give us some
         * wiggle room.
         */
        DB::table('stands')
            ->where('airfield_id', $stansted)
            ->whereIn('identifier',
                      [
                          '11',
                          '12',
                          '13',
                          '15',
                          '22',
                          '23',
                          '24',
                          '32',
                          '33',
                          '34',
                          '43',
                          '44',
                          '50',
                          '51',
                          '52',
                          '53',
                          '61',
                          '62',
                          '63'
                      ]
            )
            ->update(['assignment_priority' => 5, 'updated_at' => Carbon::now()]);

        // Update EZY's stands
        $easyJet = DB::table('airlines')->where('icao_code', 'EZY')->first()->id;
        DB::table('airline_stand')
            ->where('airline_id', $easyJet)
            ->whereIn('stand_id', $allStanstedStands)
            ->delete();


        $newStandPriorities = [];

        // Add the destination specific stand
        $newStandPriorities[] = [
            'airline_id' => $easyJet,
            'stand_id' => DB::table('stands')->where('airfield_id', $stansted)->where('identifier', 30)->first()->id,
            'destination' => 'EG',
            'priority' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        // Add the higher priority 30 stands
        $newStandPriorities = array_merge(
            $newStandPriorities,
            DB::table('stands')
                ->where('airfield_id', $stansted)
                ->whereIn('identifier', ['31', '32', '32L', '32R', '33', '33L', '33R', '34', '34L', '34R'])
                ->get()
                ->map(function ($stand) use ($easyJet) {
                    return [
                        'airline_id' => $easyJet,
                        'stand_id' => $stand->id,
                        'destination' => null,
                        'priority' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                })->toArray()
        );

        // Add the lower-priority 20 stands
        $newStandPriorities = array_merge(
            $newStandPriorities,
            DB::table('stands')
                ->where('airfield_id', $stansted)
                ->whereIn(
                    'identifier',
                    ['20', '21', '22', '22L', '22R', '23', '23L', '23R', '24', '24L', '24R', '25L', '25R']
                )
                ->get()
                ->map(function ($stand) use ($easyJet) {
                    return [
                        'airline_id' => $easyJet,
                        'stand_id' => $stand->id,
                        'destination' => null,
                        'priority' => 5,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                })->toArray()
        );

        // Add the RYR remotes
        $ryanAir = DB::table('airlines')->where('icao_code', 'RYR')->first()->id;
        DB::table('airline_stand')
            ->where('airline_id', $ryanAir)
            ->whereIn('stand_id', $allStanstedStands)
            ->update(['priority' => 1, 'updated_at' => Carbon::now()]);

        $newStandPriorities = array_merge(
            $newStandPriorities,
            DB::table('stands')
                ->where('airfield_id', $stansted)
                ->whereIn(
                    'identifier',
                    ['90L', '90R', '91L', '91R', '92L', '92R', '93L', '93R']
                )
                ->get()
                ->map(function ($stand) use ($ryanAir) {
                    return [
                        'airline_id' => $ryanAir,
                        'stand_id' => $stand->id,
                        'destination' => null,
                        'priority' => 5,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                })->toArray()
        );

        // Channex remotes
        $channex = DB::table('airlines')->where('icao_code', 'EXS')->first()->id;
        DB::table('airline_stand')
            ->where('airline_id', $channex)
            ->whereIn('stand_id', $allStanstedStands)
            ->update(['priority' => 1, 'updated_at' => Carbon::now()]);

        $newStandPriorities = array_merge(
            $newStandPriorities,
            DB::table('stands')
                ->where('airfield_id', $stansted)
                ->whereIn(
                    'identifier',
                    ['81L', '81R', '82L', '82R', '83L', '83R', '84L', '84R', '85L', '85R']
                )
                ->get()
                ->map(function ($stand) use ($channex) {
                    return [
                        'airline_id' => $channex,
                        'stand_id' => $stand->id,
                        'destination' => null,
                        'priority' => 5,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                })->toArray()
        );

        DB::table('airline_stand')->insert($newStandPriorities);
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
