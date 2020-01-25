<?php

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddPrenoteOrderData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prenotes = [];
        Prenote::all()->each(function (Prenote $prenote) use (&$prenotes) {
            $prenotes[$prenote->key] = $prenote->id;
        });

        $controllers = [];
        ControllerPosition::all()->each(function (ControllerPosition $controller) use (&$controllers) {
            $controllers[$controller->callsign] = $controller->id;
        });

        DB::table('prenote_orders')->insert($this->getOrderData($prenotes, $controllers));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('prenote_orders')->truncate();
    }

    private function getOrderData(array $prenotes, array $controllers) : array
    {
        return [

            // Gatwick Biggin App
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['EGKK_F_APP'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['EGKK_APP'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['LTC_SW_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_APP'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Gatwick Biggin London
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['LTC_SW_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGKK_SID_BIG_LON'],
                'controller_position_id' => $controllers['EGKK_APP'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],

            // EGLC Clacton
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LTC_NE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LTC_N_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LTC_E_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LON_E_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LON_C_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGLC_SID_CLN'],
                'controller_position_id' => $controllers['THAMES_APP'],
                'order' => 9,
                'created_at' => Carbon::now(),
            ],

            // Stansted NUGBO
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['LTC_NW_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['LTC_N_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['LON_C_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_NUGBO'],
                'controller_position_id' => $controllers['ESSEX_APP'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],

            // Stansted DET/LYD
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['THAMES_APP'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LTC_SE_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LON_D_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['EGSS_SID_DET_LYD'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Gatwick - Thames Pairing, App
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['EGKK_F_APP'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['EGKK_APP'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['LTC_SW_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_APP'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Gatwick - Thames Pairing, London
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LTC_SE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LON_D_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_THAMES_LON'],
                'controller_position_id' => $controllers['EGKK_APP'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Gatwick - LTMA South East
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_SE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_D_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['EGKK_APP'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Gatwick - LTMA South West
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LTC_SW_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGKK_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['EGKK_APP'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],

            // Heathrow LTMA South West
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LTC_SW_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['EGLL_S_APP'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_WEST'],
                'controller_position_id' => $controllers['EGLL_N_APP'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Heathrow LTMA North East
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_NE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_N_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_E_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_E_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_C_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['EGLL_N_APP'],
                'order' => 9,
                'created_at' => Carbon::now(),
            ],

            // Heathrow LTMA South East
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_SE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_D_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['EGLL_S_APP'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGLL_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['EGLL_N_APP'],
                'order' => 9,
                'created_at' => Carbon::now(),
            ],

            // Essex LTMA South East
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_SE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_S_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_D_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_S_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_SOUTH_EAST'],
                'controller_position_id' => $controllers['ESSEX_APP'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],

            // Essex LTMA North East
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_NE_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_N_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LTC_E_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_E_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_C_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 8,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_EAST'],
                'controller_position_id' => $controllers['ESSEX_APP'],
                'order' => 9,
                'created_at' => Carbon::now(),
            ],

            // Essex LTMA North West
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['LTC_NW_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['LTC_N_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['LTC_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['LON_C_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['LON_SC_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 6,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_ESSEX_LTMA_NORTH_WEST'],
                'controller_position_id' => $controllers['ESSEX_APP'],
                'order' => 7,
                'created_at' => Carbon::now(),
            ],

            // Manchester Liverpool Pairing
            [
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'controller_position_id' => $controllers['MAN_WL_CTR'],
                'order' => 1,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'controller_position_id' => $controllers['MAN_W_CTR'],
                'order' => 2,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'controller_position_id' => $controllers['MAN_CTR'],
                'order' => 3,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'controller_position_id' => $controllers['LON_N_CTR'],
                'order' => 4,
                'created_at' => Carbon::now(),
            ],
            [
                'prenote_id' => $prenotes['PAIRING_EGCC_EGGP'],
                'controller_position_id' => $controllers['LON_CTR'],
                'order' => 5,
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
