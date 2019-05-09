<?php

use App\Models\Hold\Hold;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStandardHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $holds = [
            // EGKK
            [
                'id' => 1,
                'fix' => 'WILLO',
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 2,
                'fix' => 'TIMBA',
                'inbound_heading' => 309,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'TIMBA',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],

            // EGCC
            [
                'id' => 3,
                'fix' => 'DAYNE',
                'inbound_heading' => 312,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'DAYNE',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 4,
                'fix' => 'ROSUN',
                'inbound_heading' => 172,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'ROSUN',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 5,
                'fix' => 'MIRSI',
                'inbound_heading' => 62,
                'minimum_altitude' => 6000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'MIRSI',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],

            // EGLL
            [
                'id' => 6,
                'fix' => 'BIG',
                'inbound_heading' => 303,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'BIG',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 7,
                'fix' => 'OCK',
                'inbound_heading' => 329,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'OCK',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 8,
                'fix' => 'BNN',
                'inbound_heading' => 117,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 17000,
                'turn_direction' => 'right',
                'description' => 'BNN',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 9,
                'fix' => 'LAM',
                'inbound_heading' => 263,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 17000,
                'turn_direction' => 'left',
                'description' => 'LAM',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // ESSEX
            [
                'id' => 10,
                'fix' => 'LOREL',
                'inbound_heading' => 188,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'left',
                'description' => 'LOREL',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 11,
                'fix' => 'ABBOT',
                'inbound_heading' => 265,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'ABBOT',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // SEVERN
            [
                'id' => 12,
                'fix' => 'BRI',
                'inbound_heading' => 91,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'BRI',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 13,
                'fix' => 'CDF',
                'inbound_heading' => 298,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'CDF',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // EGGP
            [
                'id' => 14,
                'fix' => 'TIPOD',
                'inbound_heading' => 117,
                'minimum_altitude' => 6000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'right',
                'description' => 'TIPOD',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 15,
                'fix' => 'KEGUN',
                'inbound_heading' => 4,
                'minimum_altitude' => 6000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'KEGUN',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // LL INBOUND
            [
                'id' => 16,
                'fix' => 'BRASO',
                'inbound_heading' => 263,
                'minimum_altitude' => 18000,
                'maximum_altitude' => 24000,
                'turn_direction' => 'left',
                'description' => 'LOGAN1H - BRASO',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 17,
                'fix' => 'LOGAN',
                'inbound_heading' => 289,
                'minimum_altitude' => 25000,
                'maximum_altitude' => 39000,
                'turn_direction' => 'left',
                'description' => 'LOGAN1H - LOGAN',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 18,
                'fix' => 'SABER',
                'inbound_heading' => 263,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'LOGAN1H - SABER',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // OKESI - Because CTP
            [
                'id' => 19,
                'fix' => 'OKESI',
                'inbound_heading' => 103,
                'minimum_altitude' => 11000,
                'maximum_altitude' => 36000,
                'turn_direction' => 'left',
                'description' => 'OKESI',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // EGPH
            [
                'id' => 20,
                'fix' => 'TWEED',
                'inbound_heading' => 196,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'left',
                'description' => 'TWEED',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 21,
                'fix' => 'STIRA',
                'inbound_heading' => 234,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'STIRA',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // EGPF
            [
                'id' => 22,
                'fix' => 'LANAK',
                'inbound_heading' => 303,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'LANAK',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 23,
                'fix' => 'GOW',
                'inbound_heading' => 229,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'GOW',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // EGBB
            [
                'id' => 24,
                'fix' => 'GROVE',
                'inbound_heading' => 104,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'GROVE',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 25,
                'fix' => 'CHASE',
                'inbound_heading' => 150,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'CHASE',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // EGNX
            [
                'id' => 26,
                'fix' => 'ROKUP',
                'inbound_heading' => 293,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'ROKUP',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'id' => 27,
                'fix' => 'PIGOT',
                'inbound_heading' => 6,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 12000,
                'turn_direction' => 'left',
                'description' => 'PIGOT',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        Hold::insert($holds);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Hold::truncate();
    }
}
