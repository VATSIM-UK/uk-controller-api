<?php

use App\Models\Controller\Prenote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class AddPrenotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Prenote::insert($this->getPrenoteData());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Prenote::all()->each(function (Prenote $prenote) {
            $prenote->delete();
        });
    }

    private function getPrenoteData() : array
    {
        return [
            [
                'key' => 'EGKK_SID_BIG_APP',
                'description' => 'Gatwick Biggin Departures: Director',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGKK_SID_BIG_LON',
                'description' => 'Gatwick Biggin Departures: London',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGLC_SID_CLN',
                'description' => 'London City Clacton Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGSS_SID_NUGBO',
                'description' => 'London Stansted NUGBO Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'EGSS_SID_DET_LYD',
                'description' => 'London Stansted Detling and Lydd Departures',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGKK_THAMES_APP',
                'description' => 'London Gatwick, Thames Pair: Director',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGKK_THAMES_LON',
                'description' => 'London Gatwick, Thames Pair: London',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGKK_LTMA_SOUTH_EAST',
                'description' => 'London Gatwick, LTMA Pair: TC South East',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGKK_LTMA_SOUTH_WEST',
                'description' => 'London Gatwick, LTMA Pair: TC South West',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGLL_LTMA_SOUTH_WEST',
                'description' => 'London Heathrow, LTMA Pair: TC South West',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGLL_LTMA_NORTH_EAST',
                'description' => 'London Heathrow, LTMA Pair: TC North East',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGLL_LTMA_SOUTH_EAST',
                'description' => 'London Heathrow, LTMA Pair: TC South East',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_ESSEX_LTMA_SOUTH_EAST',
                'description' => 'Essex Clutch,, LTMA Pair: TC South East',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_ESSEX_LTMA_NORTH_EAST',
                'description' => 'Essex Clutch, LTMA Pair: TC North East',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_ESSEX_LTMA_NORTH_WEST',
                'description' => 'Essex Clutch, LTMA Pair: TC North West',
                'created_at' => Carbon::now(),
            ],
            [
                'key' => 'PAIRING_EGCC_EGGP',
                'description' => 'Liverpool and Manchester Pair',
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
