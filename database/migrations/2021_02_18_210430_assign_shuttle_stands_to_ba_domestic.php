<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AssignShuttleStandsToBaDomestic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $stands = DB::table('stands')
            ->join('airline_stand', 'stands.id', '=', 'airline_stand.stand_id')
            ->join('airlines', 'airline_stand.airline_id', '=', 'airlines.id')
            ->where('airlines.icao_code', 'SHT')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGLL')->first()->id)
            ->pluck('stands.id');

        $ba = DB::table('airlines')->where('icao_code', 'BAW')->first()->id;
        $formattedStands = $stands->map(function (int $standId) use ($ba) {
            return [
                'airline_id' => $ba,
                'stand_id' => $standId,
                'destination' => 'EG',
                'created_at' => Carbon::now(),
            ];
        })->toArray();

        DB::table('airline_stand')->insert($formattedStands);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
