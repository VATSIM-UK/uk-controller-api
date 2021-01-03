<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDepartureIntervalGroupMembers extends Migration
{
    const GROUPS = [
        'EGBB_SID' => [
            'ADMEX1D',
            'ADMEX1M',
            'COWLY2Y',
            'CPT2Y',
            'DTY2Y',
            'DTY4F',
            'LUVUM1L',
            'LUVUM1M',
            'TNT1K',
            'TNT4G',
            'TNT4D',
            'TNT6E',
            'UMLUX1M',
            'UNGAP1D',
            'UNGAP1M',
            'WCO2Y',
            'WCO5D',
            'WHI1L',
            'WHI4D',
        ],
        'EGCC_SANBA_23' => [
            'SANBA1R',
            'SANBA1Y',
        ],
        'EGCC_LISTO_23' => [
            'LISTO2R',
            'LISTO2Y',
        ],
        'EGCC_EKLAD_KUXEM_23' => [
            'EKLAD1R',
            'EKLAD1Y',
            'KUXEM1R',
            'KUXEM1Y',
        ],
        'EGCC_MONTY_23' => [
            'MONTY1R',
            'MONTY1Y',
        ],
        'EGCC_NORTH_EAST_23' => [
            'POL5R',
            'POL1Y',
            'SONEX1R',
            'SONEX1Y',
        ],
        'EGCC_LISTO_05' => [
            'LISTO2Z',
            'LISTO2S',
        ],
        'EGCC_MONTY_05' => [
            'MONTY1Z',
            'MONTY1S',
        ],
        'EGCC_ASMIM_05' => [
            'ASMIM1Z',
            'ASMIM1S',
        ],
        'EGCC_DESIG_05' => [
            'DESIG1Z',
            'DESIG1S',
        ],
        'EGCC_POL_05' => [
            'POL1Z',
            'POL4S',
        ],
        'EGCN_ROGAG_02' => [
            'ROGAG2C',
        ],
        'EGCN_UPTON_02' => [
            'UPTON2C',
        ],
        'EGCN_ROGAG_20' => [
            'ROGAG2A',
        ],
        'EGCN_UPTON_20' => [
            'UPTON2A',
        ],
        'EGFF_SID' => [
            'BCN1A',
            'BCN1B',
            'EXMOR1A',
            'EXMOR1B',
            'ALVIN1B',
        ],
        'EGGD_SID' => [
            'BCN1X',
            'BCN1Z',
            'BADIM1X',
            'WOTAN1Z',
            'EXMOR1X',
            'EXMOR1Z',
        ],
        'EGGP_SID_EAST' => [
            'BARTN1T',
            'BARTN1V',
            'POL4T',
            'POL5V',
        ],
        'EGGP_SID' => [
            'WAL2V',
            'WAL2T',
            'POL4T',
            'POL5V',
            'NANTI2V',
            'NANTI2T',
        ],
        'EGGW_CPT_26' => [
            'CPT3B',
        ],
        'EGGW_DET_MATCH_26' => [
            'DET2Y',
            'DET7B',
            'MATCH2Y',
            'MATCH2B',
        ],
        'EGGW_OLNEY_26' => [
            'OLNEY1B',
        ],
        'EGGW_CPT_08' => [
            'CPT6C',
        ],
        'EGGW_DET_MATCH_08' => [
            'DET6C',
            'MATCH1C',
        ],
        'EGGW_OLNEY_08' => [
            'OLNEY1C',
        ],
        'EGJB_SID' => [
            'ORTAC2W',
            'DIN2W',
            'DIN2E',
            'CAN1W',
            'CAN1E',
            'KOKOS2E',
            'KOKOS2W',
            'LERAK1F',
            'LERAK1X',
            'ORIST1F',
            'ORIST1X',
            'ORVAL1E',
            'ORVAL1W',
            'GULDA1W',
            'GULDA1E',
            'ORTAC3E',
            'ORTAC3W',
            'SKERY3E',
            'SKERY3W',
        ],
        'EGJJ_SID' => [
            'ORTAC3A',
            'ORTAC2B',
            'SKERY3A',
            'SKERY2B',
            'BENIX5A',
            'BENIX3B',
            'DIN3A',
            'DIN2B',
            'KOKOS3A',
            'KOKOS2B',
            'CAN2A',
            'CAN2B',
            'LERAK2A',
            'LERAK2B',
            'TUNIT3A',
            'TUNIT2B',
            'ORIST1C',
            'ORIST1D',
            'ORVAL1A',
            'ORVAL1B',
            'OYSTA2B',
        ],
        'EGKK_EAST_26' => [
            'ADMAG2X',
            'CLN1M',
            'CLN1V',
            'DVR1M',
            'DVR1V',
            'FRANE1X',
            'LAM2X',
            'LAM6M',
            'LAM6V',
        ],
        'EGKK_BIG_26' => [
            'BIG2X',
            'BIG8M',
        ],
        'EGKK_WEST_26' => [
            'BOGNA1M',
            'BOGNA1V',
            'BOGNA1X',
            'HARDY1X',
            'HARDY5M',
            'HARDY5V',
            'NOVMA1X',
            'NOVMA1M',
            'NOVMA1V',
            'SAM2M',
        ],
        'EGKK_SFD_26' => [
            'SFD1X',
            'SFD5M',
            'SFD5V',
        ],
        'EGKK_RELIEF_26' => [
            'DAGGA1M',
            'DAGGA1V',
            'DAGGA1X',
            'TIGER1X',
            'TIGER3M',
            'TIGER3V',
            'WIZAD1X',
            'WIZAD4M',
            'WIZAD4V',
        ],
        'EGKK_EAST_08' => [
            'CLN5P',
            'FRANE1Z',
            'ODVIK1Z',
            'DVR2P'
        ],
        'EGKK_BIG_08' => [
            'BIG3P',
            'BIG2Z',
        ],
        'EGKK_WEST_08' => [
            'KENET3P',
            'KENET3W',
            'IMVUR1Z',
        ],
        'EGKK_SFD_08' => [
            'SFD4Z',
            'SFD9P',
            'SFD9W',
        ],
        'EGLC_SID_NORTH_WEST' => [
            'BPK1A',
            'BPK5U',
            'BPK1H',
            'BPK5T',
            'CPT1A',
            'CPT1H',
        ],
        'EGLC_SID_SOUTH_EAST' => [
            'CLN1A',
            'CLN1H',
            'LYD5T',
            'LYD5U',
            'EKNIV1A',
            'EKNIV1H',
            'DVRF5T',
            'DVRF5U',
        ],
        'EGLL_NORTH_27' => [
            'BPK7F',
            'BPK7G',
            'UMLAT1F',
            'UMLAT1G',
            'WOBUN3F',
            'WOBUN3G',
        ],
        'EGLL_WEST_27L' => [
            'CPT3F',
            'GOGSI2F',
        ],
        'EGLL_WEST_27R' => [
            'CPT3G',
            'GOGSI2G',
        ],
        'EGLL_MAXIT_27' => [
            'MAXIT1F',
            'MAXIT1G',
        ],
        'EGLL_DET_27' => [
            'DET2F',
            'DET2G',
        ],
        'EGLL_NORTH_09' => [
            'BPK5K',
            'BPK6J',
            'ULTIB1J',
            'ULTIB1K',
            'BUZAD3K',
            'BUZAD4J',
        ],
        'EGLL_CPT_09' => [
            'CPT4K',
            'CPT5J',
            'CHK',
        ],
        'EGLL_GASGU_09' => [
            'GASGU2J',
            'GASGU2K',
        ],
        'EGLL_MODMI_09' => [
            'MODMI1K',
            'MODMI1J',
        ],
        'EGLL_DET_09L' => [
            'DET1K',
        ],
        'EGLL_DET_09R' => [
            'DET1J',
        ],
        'EGLF_SID' => [
            'GWC2F',
            'GWC2L',
            'HAZEL2F',
            'HAZEL2L',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::GROUPS as $group => $sids) {
            $groupId = DB::table('sid_departure_interval_groups')
                ->where('key', $group)
                ->first()
                ->id;

            DB::table('sid')->whereIn('identifier', $sids)
                ->update(['sid_departure_interval_group_id' => $groupId, 'updated_at' => Carbon::now()]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid')->update(['sid_departure_interval_group_id' => null, 'updated_at' => Carbon::now()]);
    }
}
