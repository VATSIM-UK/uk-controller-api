<?php

use App\Services\AirfieldService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddEdinburghDelivery extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('controller_positions')
            ->insert(
                [
                    'callsign' => 'EGPH_DEL',
                    'frequency' => 121.970,
                    'created_at' => Carbon::now(),
                ]
            );
        AirfieldService::insertIntoOrderBefore('EGPH', 'EGPH_DEL', 'EGPH_GND');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        AirfieldService::removeFromTopDownsOrder('EGPH', 'EGPH_DEL');
        DB::table('controller_positions')
            ->where('callsign', 'EGPH_DEL')
            ->delete();
    }
}
