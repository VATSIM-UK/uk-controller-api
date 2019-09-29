<?php

use App\Models\Controller\Handoff;
use App\Models\Sid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        ];
    }
}
