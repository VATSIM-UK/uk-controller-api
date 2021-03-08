<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddFirProximityMeasuringPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $egtt = DB::table('flight_information_regions')->where('identification_code', 'EGTT')->first()->id;
        $egpx = DB::table('flight_information_regions')->where('identification_code', 'EGPX')->first()->id;

        DB::table('fir_proximity_measuring_points')
            ->insert(
                DB::table('squawk_reservation_measurement_points')
                    ->select(['latitude', 'longitude', 'description'])
                    ->get()
                    ->map(function ($point) use ($egtt, $egpx) {
                        return [
                            'flight_information_region_id' =>
                                Str::contains($point->description, 'EGTT') ? $egtt : $egpx,
                            'latitude' => $point->latitude,
                            'longitude' => $point->longitude,
                            'description' => $point->description,
                        ];
                    })
                    ->toArray()
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
