<?php

use App\Models\Controller\Handoff;
use App\Models\Sid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddSidHandoffData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getHandoffData() as $data) {
            Sid::where(['identifier' => $data['identifier'], 'handoff_id' => null])
                ->update(['handoff_id' => Handoff::where('key', $data['handoff'])->firstOrFail()->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sids')->update(['handoff_id' => null]);
    }

    private function getHandoffData() : array
    {
        return [
            // EGKK
            [
                'identifier' => 'ADMAG2X',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'BIG2Z',
                'handoff' => 'EGKK_SID_BIG',
            ],
            [
                'identifier' => 'BIG2X',
                'handoff' => 'EGKK_SID_BIG',
            ],
            [
                'identifier' => 'BIG3P',
                'handoff' => 'EGKK_SID_BIG',
            ],
            [
                'identifier' => 'BIG8M',
                'handoff' => 'EGKK_SID_BIG',
            ],
            [
                'identifier' => 'BOGNA1X',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'BOGNA1M',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'FRANE1Z',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'FRANE1X',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'CLN5P',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'CLN1M',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'CLN1V',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'DAGGA1X',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'DAGGA1M',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'DVR2P',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'DVR1M',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'DVR1V',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'HARDY1X',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'HARDY5M',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'IMVUR1Z',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'NOVMA1X',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'KENET3P',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'LAM5W',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'LAM6V',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'LAM1Z',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'LAM2X',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'LAM6M',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'LAM5P',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'ODVIK2Z',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'SAM2M',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'SAM3P',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'SFD4Z',
                'handoff' => 'EGKK_SID_SFD_08',
            ],
            [
                'identifier' => 'SFD1X',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'SFD5M',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'SFD9P',
                'handoff' => 'EGKK_SID_SFD_08',
            ],
            [
                'identifier' => 'BOGNA1V',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'DAGGA1V',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'HARDY5V',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'KENET3W',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'SAM3W',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'SFD5V',
                'handoff' => 'EGKK_SID_WEST',
            ],
            [
                'identifier' => 'SFD9W',
                'handoff' => 'EGKK_SID_SFD_08',
            ],
            [
                'identifier' => 'TIGER1X',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'TIGER3M',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'TIGER3V',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'WIZAD1X',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'WIZAD4M',
                'handoff' => 'EGKK_SID_EAST',
            ],
            [
                'identifier' => 'WIZAD4V',
                'handoff' => 'EGKK_SID_EAST',
            ],

            // EGLL
            [
                'identifier' => 'BPK5K',
                'handoff' => 'EGLL_SID_NORTH_EAST',
            ],
            [
                'identifier' => 'BPK6J',
                'handoff' => 'EGLL_SID_NORTH_EAST',
            ],
            [
                'identifier' => 'BPK7G',
                'handoff' => 'EGLL_SID_NORTH_EAST',
            ],
            [
                'identifier' => 'BPK7F',
                'handoff' => 'EGLL_SID_NORTH_EAST',
            ],
            [
                'identifier' => 'BUZAD3K',
                'handoff' => 'EGLL_SID_NORTH_EAST',
            ],
            [
                'identifier' => 'BUZAD4J',
                'handoff' => 'EGLL_SID_NORTH_EAST',
            ],
            [
                'identifier' => 'CHK',
                'handoff' => 'EGLL_SID_CPT_09',
            ],
            [
                'identifier' => 'CPT3F',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'CPT3G',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'CPT4K',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'CPT5J',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'DET1J',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'DET1K',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'DET2F',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'DET2G',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'GASGU1J',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'GASGU1K',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'GOGSI1F',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'GOGSI1G',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'MAY2G',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MAY2J',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MAY2K',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MAY3F',
                'handoff' => 'EGLL_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MID3G',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'MID3J',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'MID3K',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'MID4F',
                'handoff' => 'EGLL_SID_SOUTH_WEST',
            ],
            [
                'identifier' => 'ULTIB1J',
                'handoff' => 'EGLL_SID_NORTH_WEST',
            ],
            [
                'identifier' => 'ULTIB1K',
                'handoff' => 'EGLL_SID_NORTH_WEST',
            ],
            [
                'identifier' => 'UMLAT1F',
                'handoff' => 'EGLL_SID_NORTH_WEST',
            ],
            [
                'identifier' => 'UMLAT1G',
                'handoff' => 'EGLL_SID_NORTH_WEST',
            ],
            [
                'identifier' => 'WOBUN3F',
                'handoff' => 'EGLL_SID_NORTH_WEST',
            ],
            [
                'identifier' => 'WOBUN3G',
                'handoff' => 'EGLL_SID_NORTH_WEST',
            ],

            // EGLC
            [
                'identifier' => 'BPK1A',
                'handoff' => 'EGLC_SID_BPK_CPT_27',
            ],
            [
                'identifier' => 'BPK1H',
                'handoff' => 'EGLC_SID_BPK_CPT_09',
            ],
            [
                'identifier' => 'BPK5T',
                'handoff' => 'EGLC_SID_BPK_CPT_27',
            ],
            [
                'identifier' => 'BPK5U',
                'handoff' => 'EGLC_SID_BPK_CPT_09',
            ],
            [
                'identifier' => 'CLN1A',
                'handoff' => 'EGLC_SID_CLN',
            ],
            [
                'identifier' => 'CLN1H',
                'handoff' => 'EGLC_SID_CLN',
            ],
            [
                'identifier' => 'CLN7T',
                'handoff' => 'EGLC_SID_CLN',
            ],
            [
                'identifier' => 'CLN7U',
                'handoff' => 'EGLC_SID_CLN',
            ],
            [
                'identifier' => 'CPT1A',
                'handoff' => 'EGLC_SID_BPK_CPT_27',
            ],
            [
                'identifier' => 'CPT1H',
                'handoff' => 'EGLC_SID_BPK_CPT_09',
            ],
            [
                'identifier' => 'CPT6T',
                'handoff' => 'EGLC_SID_BPK_CPT_27',
            ],
            [
                'identifier' => 'CPT6U',
                'handoff' => 'EGLC_SID_BPK_CPT_09',
            ],
            [
                'identifier' => 'DVR5T',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'DVR5U',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'EKNIV1A',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'EKNIV1H',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'LYD5T',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'LYD5U',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'SAM6T',
                'handoff' => 'EGLC_SID_SOUTH',
            ],
            [
                'identifier' => 'SAM6U',
                'handoff' => 'EGLC_SID_SOUTH',
            ],

            // EGSS
            [
                'identifier' => 'BUZAD2S',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'BUZAD7R',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'CLN1E',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'CLN4S',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'CLN8R',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'CPT2S',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'CPT4R',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'DET1D',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'DET1R',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'DET1S',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'LAM2S',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'LAM3R',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'LYD5S',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'LYD6R',
                'handoff' => 'EGSS_SID_EAST_SOUTH',
            ],
            [
                'identifier' => 'NUGBO1R',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'NUGBO1S',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'UTAVA1R',
                'handoff' => 'EGSS_SID_WEST',
            ],
            [
                'identifier' => 'UTAVA1S',
                'handoff' => 'EGSS_SID_WEST',
            ],

            // EGGW
            [
                'identifier' => 'CPT3B',
                'handoff' => 'EGGW_SID_WEST_26',
            ],
            [
                'identifier' => 'CPT6C',
                'handoff' => 'EGGW_SID_WEST_08',
            ],
            [
                'identifier' => 'DET2Y',
                'handoff' => 'EGGW_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'DET6C',
                'handoff' => 'EGGW_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'DET7B',
                'handoff' => 'EGGW_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MATCH1C',
                'handoff' => 'EGGW_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MATCH2B',
                'handoff' => 'EGGW_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'MATCH2Y',
                'handoff' => 'EGGW_SID_SOUTH_EAST',
            ],
            [
                'identifier' => 'OLNEY1B',
                'handoff' => 'EGGW_SID_WEST_26',
            ],
            [
                'identifier' => 'OLNEY1C',
                'handoff' => 'EGGW_SID_WEST_08',
            ],

            // EGCC
            [
                'identifier' => 'ASMIM1Z',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'ASMIM1S',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'DESIG1Z',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'DESIG1S',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'EKLAD1R',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'EKLAD1Y',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'KUXEM1R',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'KUXEM1Y',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'LISTO2R',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'LISTO2Y',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'LISTO2S',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'LISTO2Z',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'MONTY1R',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'MONTY1Y',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'MONTY1Z',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'MONTY1S',
                'handoff' => 'EGCC_SID_WEST',
            ],
            [
                'identifier' => 'POL5R',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'POL1Z',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'POL4S',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'POL1Y',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'SANBA1R',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'SANBA1Y',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'SONEX1R',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],
            [
                'identifier' => 'SONEX1Y',
                'handoff' => 'EGCC_SID_EAST_NORTH',
            ],

            // EGGP
            [
                'identifier' => 'BARTN1T',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'BARTN1V',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'NANTI2T',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'NANTI2V',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'POL4T',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'POL5V',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'REXAM2T',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'REXAM2V',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'WAL2T',
                'handoff' => 'EGGP_SID',
            ],
            [
                'identifier' => 'WAL2V',
                'handoff' => 'EGGP_SID',
            ],

            // EGNM
            [
                'identifier' => 'DOPEK2W',
                'handoff' => 'EGNM_SID',
            ],
            [
                'identifier' => 'DOPEK2X',
                'handoff' => 'EGNM_SID',
            ],
            [
                'identifier' => 'LAMIX2W',
                'handoff' => 'EGNM_SID',
            ],
            [
                'identifier' => 'LAMIX2X',
                'handoff' => 'EGNM_SID',
            ],
            [
                'identifier' => 'NELSA3W',
                'handoff' => 'EGNM_SID',
            ],
            [
                'identifier' => 'POL2X',
                'handoff' => 'EGNM_SID',
            ],

            // EGCN
            [
                'identifier' => 'ROGA20N',
                'handoff' => 'EGCN_SID',
            ],
            [
                'identifier' => 'ROGA20S',
                'handoff' => 'EGCN_SID',
            ],
            [
                'identifier' => 'ROGAG02',
                'handoff' => 'EGCN_SID',
            ],
            [
                'identifier' => 'UPTON1A',
                'handoff' => 'EGCN_SID',
            ],
            [
                'identifier' => 'UPTON1B',
                'handoff' => 'EGCN_SID',
            ],
            [
                'identifier' => 'UPTON1C',
                'handoff' => 'EGCN_SID',
            ],

            // EGJJ
            [
                'identifier' => 'BENIX3B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'BENIX5A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'CAN2A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'CAN2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'DIN2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'DIN3A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'KOKOS2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'KOKOS3A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'LERAK2A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'LERAK2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'ORIST1C',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'ORIST1D',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'ORTAC2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'ORTAC3A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'ORVAL1A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'ORVAL1B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'OYSTA2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'SKERY2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'SKERY3A',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'TUNIT2B',
                'handoff' => 'EGJJ_SID',
            ],
            [
                'identifier' => 'TUNIT3A',
                'handoff' => 'EGJJ_SID',
            ],

            // EGJB
            [
                'identifier' => 'CAN1E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'CAN1W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'DIN2E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'DIN2W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'GULDA1E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'GULDA1W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'KOKOS2E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'KOKOS2W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'LERAK1F',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'LERAK1X',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORIST1F',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORIST1X',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORTAC2W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORTAC3E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORTAC3W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORVAL1E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'ORVAL1W',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'SKERY3E',
                'handoff' => 'EGJB_SID',
            ],
            [
                'identifier' => 'SKERY3W',
                'handoff' => 'EGJB_SID',
            ],

            // EGGD
            [
                'identifier' => 'BADIM1X',
                'handoff' => 'EGGD_SID_27_BADIM',
            ],
            [
                'identifier' => 'WOTAN1Z',
                'handoff' => 'EGGD_SID_09_WOTAN',
            ],
            [
                'identifier' => 'BCN1X',
                'handoff' => 'EGGD_SID_27_BCN',
            ],
            [
                'identifier' => 'BCN1Z',
                'handoff' => 'EGGD_SID_09_BCN',
            ],
            [
                'identifier' => 'EXMOR1X',
                'handoff' => 'EGGD_SID_27_EXMOR',
            ],
            [
                'identifier' => 'EXMOR1Z',
                'handoff' => 'EGGD_SID_09_EXMOR',
            ],

            // EGFF
            [
                'identifier' => 'ALVIN1B',
                'handoff' => 'EGFF_SID_NORTH',
            ],
            [
                'identifier' => 'BCN1A',
                'handoff' => 'EGFF_SID_NORTH',
            ],
            [
                'identifier' => 'BCN1B',
                'handoff' => 'EGFF_SID_NORTH',
            ],
            [
                'identifier' => 'EXMOR1A',
                'handoff' => 'EGFF_SID_SOUTH',
            ],
            [
                'identifier' => 'EXMOR1B',
                'handoff' => 'EGFF_SID_SOUTH',
            ],

            // EGBB
            [
                'identifier' => 'ADMEX1D',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'ADMEX1M',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'COWLY2Y',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'CPT2Y',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'DTY2Y',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'DTY2Y',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'DTY4F',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'LUVUM1L',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'LUVUM1M',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'TNT1K',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'TNT4D',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'TNT4G',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'TNT6E',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'UMLUX1M',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'UNGAP1D',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'UNGAP1M',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'WCO2Y',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'WCO5D',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'WHI1L',
                'handoff' => 'EGBB_SID',
            ],
            [
                'identifier' => 'WHI4D',
                'handoff' => 'EGBB_SID',
            ],

            // EGNX
            [
                'identifier' => 'BPK2P',
                'handoff' => 'EGNX_SID_SOUTH_09',
            ],
            [
                'identifier' => 'DTY3N',
                'handoff' => 'EGNX_SID_SOUTH_27',
            ],
            [
                'identifier' => 'DTY4P',
                'handoff' => 'EGNX_SID_SOUTH_09',
            ],
            [
                'identifier' => 'POL2P',
                'handoff' => 'EGNX_SID_NORTH_09',
            ],
            [
                'identifier' => 'TNT2N',
                'handoff' => 'EGNX_SID_NORTH_27',
            ],
            [
                'identifier' => 'TNT3P',
                'handoff' => 'EGNX_SID_NORTH_09',
            ],

            // EGPH
            [
                'identifier' => 'GOSAM1C',
                'handoff' => 'EGPH_SID_SOUTH',
            ],
            [
                'identifier' => 'GOSAM1D',
                'handoff' => 'EGPH_SID_SOUTH',
            ],
            [
                'identifier' => 'GRICE3C',
                'handoff' => 'EGPH_SID_NORTH',
            ],
            [
                'identifier' => 'GRICE4D',
                'handoff' => 'EGPH_SID_NORTH',
            ],
            [
                'identifier' => 'TLA6C',
                'handoff' => 'EGPH_SID_SOUTH',
            ],
            [
                'identifier' => 'TLA6D',
                'handoff' => 'EGPH_SID_SOUTH',
            ],

            // EGPF
            [
                'identifier' => 'CLYDE3A',
                'handoff' => 'EGPF_SID_WEST',
            ],
            [
                'identifier' => 'CLYDE3B',
                'handoff' => 'EGPF_SID_WEST',
            ],
            [
                'identifier' => 'FOYLE3A',
                'handoff' => 'EGPF_SID_NORTH',
            ],
            [
                'identifier' => 'FOYLE3B',
                'handoff' => 'EGPF_SID_NORTH',
            ],
            [
                'identifier' => 'LOMON3A',
                'handoff' => 'EGPF_SID_NORTH',
            ],
            [
                'identifier' => 'LOMON3B',
                'handoff' => 'EGPF_SID_NORTH',
            ],
            [
                'identifier' => 'LUSIV1A',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'LUSIV1B',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'NORBO1H',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'NORBO1J',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'PTH4A',
                'handoff' => 'EGPF_SID_NORTH',
            ],
            [
                'identifier' => 'PTH4B',
                'handoff' => 'EGPF_SID_NORTH',
            ],
            [
                'identifier' => 'ROBBO2A',
                'handoff' => 'EGPF_SID_WEST',
            ],
            [
                'identifier' => 'ROBBO2B',
                'handoff' => 'EGPF_SID_WEST',
            ],
            [
                'identifier' => 'TLA5A',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'TLA6B',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'TRN3A',
                'handoff' => 'EGPF_SID_SOUTH',
            ],
            [
                'identifier' => 'TRN6B',
                'handoff' => 'EGPF_SID_SOUTH',
            ],

            // EGPK
            [
                'identifier' => 'DAUNT1K',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'LUCCO1K',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'NGY1K',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'NGY1L',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'SUDBY1L',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'SUMIN1L',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'TRN2K',
                'handoff' => 'EGPK_SID',
            ],
            [
                'identifier' => 'TRN2L',
                'handoff' => 'EGPK_SID',
            ],

            // EGNT
            [
                'identifier' => 'GIRLI1T',
                'handoff' => 'EGNT_SID',
            ],
            [
                'identifier' => 'GIRLI1Y',
                'handoff' => 'EGNT_SID',
            ],
            [
                'identifier' => 'GIRLI3X',
                'handoff' => 'EGNT_SID',
            ],

            // EGWU
            [
                'identifier' => 'BUZAD1Y',
                'handoff' => 'EGWU_SID_WEST',
            ],
            [
                'identifier' => 'BUZAD3X',
                'handoff' => 'EGWU_SID_WEST',
            ],
            [
                'identifier' => 'CPT4Y',
                'handoff' => 'EGWU_SID_WEST',
            ],
            [
                'identifier' => 'CPT5X',
                'handoff' => 'EGWU_SID_WEST',
            ],
            [
                'identifier' => 'DET4X',
                'handoff' => 'EGWU_SID_EAST',
            ],
            [
                'identifier' => 'DET4Y',
                'handoff' => 'EGWU_SID_EAST',
            ],
            [
                'identifier' => 'MATCH1X',
                'handoff' => 'EGWU_SID_EAST',
            ],
            [
                'identifier' => 'MATCH1Y',
                'handoff' => 'EGWU_SID_EAST',
            ],
        ];
    }
}
