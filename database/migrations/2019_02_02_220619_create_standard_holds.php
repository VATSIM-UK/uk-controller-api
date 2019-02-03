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
                'fix' => 'WILLO',
                'inbound_heading' => 285,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'left',
                'description' => 'WILLO',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
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
                'fix' => 'DAYNE',
                'inbound_heading' => 312,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 14000,
                'turn_direction' => 'right',
                'description' => 'DAYNE',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'fix' => 'ROSUN',
                'inbound_heading' => 172,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 24000,
                'turn_direction' => 'right',
                'description' => 'ROSUN',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
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
                'fix' => 'BIG',
                'inbound_heading' => 303,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'BIG',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'fix' => 'OCK',
                'inbound_heading' => 329,
                'minimum_altitude' => 7000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'OCK',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'fix' => 'BNN',
                'inbound_heading' => 117,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 17000,
                'turn_direction' => 'right',
                'description' => 'BNN',
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'fix' => 'LAM',
                'inbound_heading' => 263,
                'minimum_altitude' => 8000,
                'maximum_altitude' => 17000,
                'turn_direction' => 'left',
                'description' => 'LAM',
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
