<?php

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class AddControllerPositions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ControllerPosition::insert(
            [
                [
                    'callsign' => 'EGAA_APP',
                    'frequency' => 120.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAA_GND',
                    'frequency' => 121.75,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAA_R_APP',
                    'frequency' => 128.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAA_TWR',
                    'frequency' => 118.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAC_APP',
                    'frequency' => 130.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAC_R_APP',
                    'frequency' => 134.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAC_TWR',
                    'frequency' => 122.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAD_R_TWR',
                    'frequency' => 128.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAE_APP',
                    'frequency' => 123.62,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGAE_TWR',
                    'frequency' => 134.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBB_APP',
                    'frequency' => 123.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBB_DEL',
                    'frequency' => 121.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBB_F_APP',
                    'frequency' => 131,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBB_GND',
                    'frequency' => 121.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBB_TWR',
                    'frequency' => 118.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBE_R_TWR',
                    'frequency' => 123.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBJ_APP',
                    'frequency' => 128.55,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGBJ_TWR',
                    'frequency' => 122.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_DEL',
                    'frequency' => 121.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_F_APP',
                    'frequency' => 121.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_GND',
                    'frequency' => 121.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_N_APP',
                    'frequency' => 135,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_N_TWR',
                    'frequency' => 118.62,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_S_APP',
                    'frequency' => 118.57,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCC_S_TWR',
                    'frequency' => 119.4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCN_APP',
                    'frequency' => 126.22,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGCN_TWR',
                    'frequency' => 128.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGFF_APP',
                    'frequency' => 119.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGFF_R_APP',
                    'frequency' => 125.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGFF_TWR',
                    'frequency' => 133.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGD_APP',
                    'frequency' => 125.65,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGD_DEL',
                    'frequency' => 121.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGD_GND',
                    'frequency' => 121.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGD_R_APP',
                    'frequency' => 136.07,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGD_TWR',
                    'frequency' => 133.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGP_APP',
                    'frequency' => 119.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGP_F_APP',
                    'frequency' => 118.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGP_GND',
                    'frequency' => 119.998,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGP_TWR',
                    'frequency' => 126.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGW_APP',
                    'frequency' => 129.55,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGW_DEL',
                    'frequency' => 121.67,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGW_F_APP',
                    'frequency' => 128.75,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGW_GND',
                    'frequency' => 121.75,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGW_TWR',
                    'frequency' => 132.55,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGGX_FSS',
                    'frequency' => 131.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHH_APP',
                    'frequency' => 119.47,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHH_F_APP',
                    'frequency' => 118.65,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHH_GND',
                    'frequency' => 121.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHH_TWR',
                    'frequency' => 125.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHI_APP',
                    'frequency' => 122.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHI_GND',
                    'frequency' => 121.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHI_TWR',
                    'frequency' => 118.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SOLENT_APP',
                    'frequency' => 120.22,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHQ_APP',
                    'frequency' => 127.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHQ_GND',
                    'frequency' => 121.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGHQ_TWR',
                    'frequency' => 134.37,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJA_GND',
                    'frequency' => 130.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJA_TWR',
                    'frequency' => 125.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJB_APP',
                    'frequency' => 124.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJB_GND',
                    'frequency' => 121.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJB_TWR',
                    'frequency' => 119.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJJ_APP',
                    'frequency' => 120.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJJ_C_APP',
                    'frequency' => 125.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJJ_GND',
                    'frequency' => 121.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJJ_R_APP',
                    'frequency' => 118.55,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJJ_S_APP',
                    'frequency' => 120.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGJJ_TWR',
                    'frequency' => 119.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKA_APP',
                    'frequency' => 123.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKA_TWR',
                    'frequency' => 125.4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKB_APP',
                    'frequency' => 129.4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKB_TWR',
                    'frequency' => 134.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKK-S_APP',
                    'frequency' => 126.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKK_APP',
                    'frequency' => 126.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKK_DEL',
                    'frequency' => 121.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKK_F_APP',
                    'frequency' => 118.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKK_GND',
                    'frequency' => 121.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKK_TWR',
                    'frequency' => 124.22,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGKR_TWR',
                    'frequency' => 119.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLC_APP',
                    'frequency' => 128.02,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'THAMES_APP',
                    'frequency' => 132.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLC_GND',
                    'frequency' => 121.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLC_TWR',
                    'frequency' => 118.07,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLD_I_TWR',
                    'frequency' => 130.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLF_APP',
                    'frequency' => 134.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLF_TWR',
                    'frequency' => 122.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_1_GND',
                    'frequency' => 121.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_2_GND',
                    'frequency' => 121.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_3_GND',
                    'frequency' => 121.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_DEL',
                    'frequency' => 121.97,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_F_APP',
                    'frequency' => 120.4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_N_APP',
                    'frequency' => 119.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_N_TWR',
                    'frequency' => 118.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_P_GND',
                    'frequency' => 124.47,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_S_APP',
                    'frequency' => 134.97,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLL_S_TWR',
                    'frequency' => 118.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGLW_TWR',
                    'frequency' => 122.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGMC_R_APP',
                    'frequency' => 130.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGMC_APP',
                    'frequency' => 132.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGMC_TWR',
                    'frequency' => 126.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGMD_APP',
                    'frequency' => 120.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGMD_TWR',
                    'frequency' => 128.52,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNC_APP',
                    'frequency' => 123.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNC_TWR',
                    'frequency' => 123.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNH_APP',
                    'frequency' => 119.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNH_TWR',
                    'frequency' => 118.4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNJ_APP',
                    'frequency' => 119.12,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNJ_TWR',
                    'frequency' => 124.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNM_APP',
                    'frequency' => 134.57,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNM_DEL',
                    'frequency' => 121.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNM_F_APP',
                    'frequency' => 125.37,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNM_TWR',
                    'frequency' => 120.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNR_APP',
                    'frequency' => 123.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNR_F_APP',
                    'frequency' => 130.02,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNR_TWR',
                    'frequency' => 124.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNS_APP',
                    'frequency' => 120.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNS_F_APP',
                    'frequency' => 125.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNS_TWR',
                    'frequency' => 119,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNT_F_APP',
                    'frequency' => 125.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNT_GND',
                    'frequency' => 121.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNT_R_APP',
                    'frequency' => 124.37,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNT_TWR',
                    'frequency' => 119.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNV_APP',
                    'frequency' => 118.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNV_F_APP',
                    'frequency' => 128.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNV_TWR',
                    'frequency' => 119.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNX_APP',
                    'frequency' => 134.17,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNX_F_APP',
                    'frequency' => 120.12,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNX_GND',
                    'frequency' => 121.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGNX_TWR',
                    'frequency' => 124,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGOV_P_APP',
                    'frequency' => 123.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGOV_R_APP',
                    'frequency' => 125.22,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGOV_TWR',
                    'frequency' => 122.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPA_APP',
                    'frequency' => 118.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPA_I_TWR',
                    'frequency' => 118.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPA_TWR',
                    'frequency' => 118.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPB_APP',
                    'frequency' => 123.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPB_TWR',
                    'frequency' => 118.25,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPC_APP',
                    'frequency' => 119.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPC_TWR',
                    'frequency' => 119.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPD_APP',
                    'frequency' => 119.05,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPD_F_APP',
                    'frequency' => 128.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPD_GND',
                    'frequency' => 121.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPD_TWR',
                    'frequency' => 118.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPE_APP',
                    'frequency' => 122.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPE_R_APP',
                    'frequency' => 122.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPE_TWR',
                    'frequency' => 118.4,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPF_APP',
                    'frequency' => 119.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPF_F_APP',
                    'frequency' => 128.75,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPF_GND',
                    'frequency' => 121.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPF_TWR',
                    'frequency' => 118.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPH_APP',
                    'frequency' => 121.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPH_F_APP',
                    'frequency' => 128.97,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPH_GND',
                    'frequency' => 121.75,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPH_TWR',
                    'frequency' => 118.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPK_APP',
                    'frequency' => 129.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPK_F_APP',
                    'frequency' => 124.62,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPK_GND',
                    'frequency' => 121.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPK_TWR',
                    'frequency' => 118.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPL_APP',
                    'frequency' => 119.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPL_I_TWR',
                    'frequency' => 125.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPL_TWR',
                    'frequency' => 119.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPM_APP',
                    'frequency' => 123.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPM_TWR',
                    'frequency' => 123.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPN_APP',
                    'frequency' => 122.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPN_TWR',
                    'frequency' => 122.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPO_APP',
                    'frequency' => 123.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPO_TWR',
                    'frequency' => 123.5,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGPX_INFO',
                    'frequency' => 119.87,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGQQ_CTR',
                    'frequency' => 134.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGQS_P_APP',
                    'frequency' => 123.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGQS_R_APP',
                    'frequency' => 119.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGQS_TWR',
                    'frequency' => 118.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSC_APP',
                    'frequency' => 123.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSC_R_APP',
                    'frequency' => 124.97,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSC_TWR',
                    'frequency' => 125.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSH_APP',
                    'frequency' => 119.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSH_F_APP',
                    'frequency' => 128.32,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSH_TWR',
                    'frequency' => 124.25,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSS_APP',
                    'frequency' => 136.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'ESSEX_APP',
                    'frequency' => 120.62,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSS_DEL',
                    'frequency' => 121.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSS_GND',
                    'frequency' => 121.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGSS_TWR',
                    'frequency' => 123.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTB_GND',
                    'frequency' => 121.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTB_TWR',
                    'frequency' => 126.55,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTC_APP',
                    'frequency' => 122.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTC_TWR',
                    'frequency' => 134.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTE_N_APP',
                    'frequency' => 128.97,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTE_S_APP',
                    'frequency' => 123.57,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTE_TWR',
                    'frequency' => 119.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTK_APP',
                    'frequency' => 127.75,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTK_GND',
                    'frequency' => 121.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTK_TWR',
                    'frequency' => 133.42,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTO_I_TWR',
                    'frequency' => 122.25,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGTT_INFO',
                    'frequency' => 124.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVN_GND',
                    'frequency' => 121.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVN_P_APP',
                    'frequency' => 123.55,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVN_R_APP',
                    'frequency' => 127.25,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVN_TWR',
                    'frequency' => 123.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVN_Z_APP',
                    'frequency' => 119,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVV_CTR',
                    'frequency' => 135.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGVV_L_CTR',
                    'frequency' => 127.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGWD_CTR',
                    'frequency' => 135.27,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGWU_D_APP',
                    'frequency' => 130.35,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGWU_GND',
                    'frequency' => 121.57,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGWU_R_APP',
                    'frequency' => 126.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGWU_TWR',
                    'frequency' => 120.67,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGXC_APP',
                    'frequency' => 120.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGXC_GND',
                    'frequency' => 121.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGXC_P_APP',
                    'frequency' => 123.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGXC_TWR',
                    'frequency' => 122.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYD_GND',
                    'frequency' => 121.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYD_P_APP',
                    'frequency' => 123.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYD_R_APP',
                    'frequency' => 124.45,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYD_TWR',
                    'frequency' => 125.05,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYM_P_APP',
                    'frequency' => 123.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYM_R_APP',
                    'frequency' => 124.15,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'EGYM_TWR',
                    'frequency' => 118.325,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_CTR',
                    'frequency' => 127.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_C_CTR',
                    'frequency' => 127.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_D_CTR',
                    'frequency' => 134.9,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_E_CTR',
                    'frequency' => 118.47,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_N_CTR',
                    'frequency' => 133.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_SC_CTR',
                    'frequency' => 132.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_S_CTR',
                    'frequency' => 129.42,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_W_CTR',
                    'frequency' => 126.07,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LON_WN_CTR',
                    'frequency' => 129.37,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_CTR',
                    'frequency' => 135.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_E_CTR',
                    'frequency' => 121.22,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_ER_CTR',
                    'frequency' => 133.52,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_ES_CTR',
                    'frequency' => 129.6,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_ED_CTR',
                    'frequency' => 124.92,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_EJ_CTR',
                    'frequency' => 135.42,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_NE_CTR',
                    'frequency' => 118.82,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_NW_CTR',
                    'frequency' => 121.27,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_N_CTR',
                    'frequency' => 119.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_SE_CTR',
                    'frequency' => 120.52,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_SW_CTR',
                    'frequency' => 133.17,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'LTC_S_CTR',
                    'frequency' => 134.12,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'MAN_W_CTR',
                    'frequency' => 128.05,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'MAN_WL_CTR',
                    'frequency' => 125.95,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'MAN_E_CTR',
                    'frequency' => 133.8,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'MAN_CTR',
                    'frequency' => 118.7,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_CTR',
                    'frequency' => 135.52,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_C_CTR',
                    'frequency' => 127.27,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_D_CTR',
                    'frequency' => 135.85,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_E_CTR',
                    'frequency' => 121.32,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_N_CTR',
                    'frequency' => 129.22,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_R_CTR',
                    'frequency' => 129.1,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_S_CTR',
                    'frequency' => 134.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_WD_CTR',
                    'frequency' => 133.2,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'SCO_W_CTR',
                    'frequency' => 132.72,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'STC_A_CTR',
                    'frequency' => 123.77,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'STC_CTR',
                    'frequency' => 126.3,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'STC_E_CTR',
                    'frequency' => 130.97,
                    'created_at' => Carbon::now(),
                ],
                [
                    'callsign' => 'STC_W_CTR',
                    'frequency' => 124.82,
                    'created_at' => Carbon::now(),
                ],
            ]

        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ControllerPosition::truncate();
    }
}
