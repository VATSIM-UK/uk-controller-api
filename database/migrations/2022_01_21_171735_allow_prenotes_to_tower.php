<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AllowPrenotesToTower extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->doUpdate(true);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->doUpdate(false);
    }

    private function doUpdate(bool $receivesPrenotes): void
    {
        DB::table('controller_positions')
            ->join('top_downs', 'controller_positions.id', '=', 'top_downs.controller_position_id')
            ->whereIn('top_downs.airfield_id', $this->getAirfieldsWithGroundOrDelivery())
            ->where('callsign', 'LIKE', '%_TWR')
            ->update(
                [
                    'controller_positions.receives_prenotes' => $receivesPrenotes,
                    'controller_positions.updated_at' => Carbon::now()
                ]
            );
    }

    private function getAirfieldsWithGroundOrDelivery(): array
    {
        $controllerPositions = DB::table('controller_positions')
            ->where('callsign', 'LIKE', '%_DEL')
            ->orWhere('callsign', 'LIKE', '%_GND')
            ->pluck('id')
            ->toArray();

        return DB::table('top_downs')
            ->whereIn('controller_position_id', $controllerPositions)
            ->pluck('airfield_id')
            ->toArray();
    }
}
