<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\Prenote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddAirfieldPairingPrenoteData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airfields = [];
        Airfield::all()->each(function (Airfield $airfield) use (&$airfields) {
            $airfields[$airfield->code] = $airfield->id;
        });

        $prenotes = [];
        Prenote::all()->each(function (Prenote $prenote) use (&$prenotes) {
            $prenotes[$prenote->key] = $prenote->id;
        });

        DB::table('airfield_pairing_prenotes')->insert($this->getPairingData($airfields, $prenotes));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('airfield_pairing_prenotes')->truncate();
    }

    private function getPairingData(array $airfields, array $prenotes) : array
    {
        return [
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGLC'],
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGLC'],
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGKB'],
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGKB'],
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGMC'],
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGMC'],
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGHH'],
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGHI'],
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGLL'],
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGGW'],
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGKK'],
                'destination_airfield_id' => $airfields['EGSS'],
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGKK'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGHI'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGHH'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGKA'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGSS'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGGW'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGSC'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGLC'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGKB'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGLL'],
                'destination_airfield_id' => $airfields['EGMC'],
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGSS'],
                'destination_airfield_id' => $airfields['EGKK'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGSS'],
                'destination_airfield_id' => $airfields['EGLC'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGSS'],
                'destination_airfield_id' => $airfields['EGHI'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGSS'],
                'destination_airfield_id' => $airfields['EGHH'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGW'],
                'destination_airfield_id' => $airfields['EGKK'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGW'],
                'destination_airfield_id' => $airfields['EGLC'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGW'],
                'destination_airfield_id' => $airfields['EGKB'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGW'],
                'destination_airfield_id' => $airfields['EGMC'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGW'],
                'destination_airfield_id' => $airfields['EGHI'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGW'],
                'destination_airfield_id' => $airfields['EGHH'],
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGCC'],
                'destination_airfield_id' => $airfields['EGGP'],
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'created_at' => Carbon::now(),
            ],
            [
                'origin_airfield_id' => $airfields['EGGP'],
                'destination_airfield_id' => $airfields['EGCC'],
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
