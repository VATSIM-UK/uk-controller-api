<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NetworkAircraftTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('network_aircraft')->insert(
            [
                [
                    'callsign' => 'BAW123',
                    'cid' => 1203533,
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '35123',
                    'planned_aircraft' => 'B738',
                    'planned_aircraft_short' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'EGLL',
                    'planned_altitude' => '15000',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now()->subMinutes(9),
                    'remarks' => 'BAW123 Remarks',
                ],
                [
                    'callsign' => 'BAW456',
                    'cid' => 1203534,
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '35123',
                    'planned_aircraft' => 'B738',
                    'planned_aircraft_short' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'EGLL',
                    'planned_altitude' => '15000',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => Carbon::now()->subMinutes(31),
                    'updated_at' => Carbon::now(),
                    'remarks' => 'BAW456 Remarks',
                ],
                [
                    'callsign' => 'BAW789',
                    'cid' => 1203535,
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '35123',
                    'planned_aircraft' => 'B738',
                    'planned_aircraft_short' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'EGLL',
                    'planned_altitude' => '15000',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => Carbon::now()->subMinutes(31),
                    'updated_at' => Carbon::now()->subMinutes(21),
                    'remarks' => 'BAW789 Remarks',
                ],
                [
                    'callsign' => 'RYR824',
                    'cid' => 1203536,
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '123',
                    'planned_aircraft' => 'B738',
                    'planned_aircraft_short' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'LEMD',
                    'planned_altitude' => '15001',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now()->subMinutes(10),
                    'remarks' => 'RYR824 Remarks',
                ],
            ]
        );
    }
}
