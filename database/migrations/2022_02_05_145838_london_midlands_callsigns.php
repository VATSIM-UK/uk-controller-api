<?php

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LondonMidlandsCallsigns extends Migration
{
    const CHANGES = [
        'LON_CE_CTR' => 'LON_ME_CTR',
        'LON_CW_CTR' => 'LON_MW_CTR',
        'LON_CL_CTR' => 'LON_ML_CTR',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::CHANGES as $oldCallsign => $newCallsign) {
            ControllerPosition::where('callsign', $oldCallsign)->update(['callsign' => $newCallsign]);
        }
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
