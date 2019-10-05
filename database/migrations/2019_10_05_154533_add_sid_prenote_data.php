<?php

use App\Models\Controller\Prenote;
use App\Models\Sid;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddSidPrenoteData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sidData = [];
        Sid::all()->each(function (Sid $sid) use (&$sidData) {
            $sidData[$sid->identifier] = $sid->id;
        });

        $prenoteData = [];
        Prenote::all()->each(function (Prenote $prenote) use (&$prenoteData) {
            $prenoteData[$prenote->key] = $prenote->id;
        });

        DB::table('sid_prenotes')->insert($this->getPrenoteData($sidData, $prenoteData));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid_prenotes')->truncate();
    }

    private function getPrenoteData(array $sids, array $prenotes) : array
    {
        return [
            [
                'sid_id' => $sids['BIG2X'],
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['BIG2X'],
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['BIG2Z'],
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['BIG2Z'],
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['CLN1A'],
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['CLN1H'],
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['CLN7T'],
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['CLN7U'],
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['NUGBO1S'],
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['NUGBO1R'],
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['DET1D'],
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['DET1R'],
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['DET1S'],
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['LYD6R'],
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'created_at' => Carbon::now(),
            ],
            [
                'sid_id' => $sids['LYD5S'],
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
