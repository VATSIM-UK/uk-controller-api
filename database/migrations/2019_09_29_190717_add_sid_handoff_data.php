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
            Sid::where('identifier', $data['identifier'])
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

        ];
    }
}
