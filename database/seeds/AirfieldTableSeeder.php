<?php

use App\Models\Airfield\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AirfieldTableSeeder extends Seeder
{
    public function run()
    {
        $airfield1 = Airfield::create(
            [
                'code' => 'EGLL',
                'latitude' => 51.4775,
                'longitude' => -0.461389,
                'elevation' => 1,
                'transition_altitude' => 6000,
                'standard_high' => true,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        $airfield1->mslCalculationAirfields()->sync([$airfield1->id]);

        $airfield2 = Airfield::create(
            [
                'code' => 'EGBB',
                'latitude' => 52.453889,
                'longitude' => -1.748056,
                'elevation' => 1,
                'transition_altitude' => 6000,
                'standard_high' => false,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );
        $airfield2->mslCalculationAirfields()->sync([$airfield2->id]);

        Airfield::create(
            [
                'code' => 'EGKR',
                'latitude' => 51.213611,
                'longitude' => -0.138611,
                'elevation' => 1,
                'transition_altitude' => 6000,
                'standard_high' => true,
                'created_at' => Carbon::now()->subHour(),
                'updated_at' => Carbon::now(),
            ]
        );

        Airfield::findOrFail(1)->prenotePairings()
            ->attach(1, ['destination_airfield_id' => 2, 'prenote_id' => 1, 'flight_rule_id' => 1,]);
    }
}
