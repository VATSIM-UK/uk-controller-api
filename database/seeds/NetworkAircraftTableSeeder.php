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
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '35123',
                    'planned_aircraft' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'EGLL',
                    'planned_altitude' => '15000',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now()->subMinutes(9),
                ],
                [
                    'callsign' => 'BAW456',
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '35123',
                    'planned_aircraft' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'EGLL',
                    'planned_altitude' => '15000',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => Carbon::now()->subMinutes(31),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'BAW789',
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '35123',
                    'planned_aircraft' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'EGLL',
                    'planned_altitude' => '15000',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => Carbon::now()->subMinutes(31),
                    'updated_at' => Carbon::now()->subMinutes(21),
                ],
                [
                    'callsign' => 'RYR824',
                    'latitude' => 54.66,
                    'longitude'=> -6.21,
                    'altitude' => '35123',
                    'groundspeed' => '123',
                    'planned_aircraft' => 'B738',
                    'planned_depairport' => 'EGKK',
                    'planned_destairport' => 'LEMD',
                    'planned_altitude' => '15001',
                    'transponder' => '1234',
                    'transponder_last_updated_at' => Carbon::now(),
                    'planned_flighttype' => 'I',
                    'planned_route' => 'DIRECT',
                    'created_at' => '2020-05-30 17:30:00',
                    'updated_at' => Carbon::now()->subMinutes(10),
                ],
            ]
        );
    }
}
