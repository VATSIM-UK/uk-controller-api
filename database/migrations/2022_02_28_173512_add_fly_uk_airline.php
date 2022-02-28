<?php

use App\Models\Airline\Airline;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlyUkAirline extends Migration
{
    private const ICAO_CODE = 'UKV';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Airline::create(
            [
                'icao_code' => self::ICAO_CODE,
                'name' => 'FlyUK (Virtual)',
                'callsign' => 'Skyways',
                'is_cargo' => false,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Airline::where('icao_code', self::ICAO_CODE)->delete();
    }
}
