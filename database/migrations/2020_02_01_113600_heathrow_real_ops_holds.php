<?php

use App\Models\Hold\Hold;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Hold\HoldRestriction;

class HeathrowRealOpsHolds extends Migration
{
    private $holds = [
        [
            'fix' => 'BILNI',
            'inbound_heading' => 107,
            'minimum_altitude' => 20000,
            'maximum_altitude' => 30000,
            'turn_direction' => 'left',
            'description' => 'BILNI - LL/KK Inbounds',
        ],
        [
            'fix' => 'DOMUT',
            'inbound_heading' => 040,
            'minimum_altitude' => 23000,
            'maximum_altitude' => 30000,
            'turn_direction' => 'right',
            'description' => 'DOMUT - LL/KK Inbounds'
        ],
        [
            'fix' => 'KATHY',
            'inbound_heading' => 040,
            'minimum_altitude' => 16000,
            'maximum_altitude' => 19000,
            'turn_direction' => 'left',
            'description' => 'KATHY - LL/KK Inbounds'
        ],
        [
            'fix' => 'TIGER',
            'inbound_heading' => 316,
            'minimum_altitude' => 15000,
            'maximum_altitude' => 24000,
            'turn_direction' => 'right',
            'description' => 'TIGER - LL Inbounds'
        ],
        [
            'fix' => 'WCO',
            'inbound_heading' => 154,
            'minimum_altitude' => 16000,
            'maximum_altitude' => 30000,
            'turn_direction' => 'left',
            'description' => 'WCO - LL Inbounds'
        ],
        [
            'fix' => 'HON',
            'inbound_heading' => 146,
            'minimum_altitude' => 16000,
            'maximum_altitude' => 20000,
            'turn_direction' => 'left',
            'description' => 'HON - LL Inbounds Left Low'
        ],
        [
            'fix' => 'HON',
            'inbound_heading' => 146,
            'minimum_altitude' => 20000,
            'maximum_altitude' => 30000,
            'turn_direction' => 'right',
            'description' => 'HON - LL Inbounds Right High'
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now();
        foreach ($this->holds as $key => $hold) {
            $this->holds[$key]['created_at'] = $now;
        }

        DB::table('hold')->insert($this->holds);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('hold')->whereIn('description', array_column($this->holds, 'description'))->delete();
    }
}
