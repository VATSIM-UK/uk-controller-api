<?php

use App\Models\Airfield\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Handle EGLL SIDs
        DB::statement(
            'UPDATE `sid`
                SET `handoff_id` = (SELECT `id` FROM `handoffs` WHERE `key` = "EGLL_SID_SOUTH_WEST")
              WHERE `identifier` IN ("MAXIT1F", "MAXIT1G", "MODMI1J", "MODMI1K")'
        );

        // Do Farnborough
        $handoffId = DB::table('handoffs')->insertGetId(
            [
                'key' => 'EGLF_SID',
                'description' => 'Farnborough Departures',
                'created_at' => Carbon::now(),
            ]
        );

        $controllers = [
            'LTC_SW_CTR',
            'LTC_S_CTR',
            'LTC_CTR',
            'LON_S_CTR',
            'LON_SC_CTR',
            'LON_CTR',
            'EGLF_APP'
        ];

        $i = 0;
        while ($i < count($controllers)) {
            DB::table('handoff_orders')
                ->insert(
                    [
                        'handoff_id' => $handoffId,
                        'controller_position_id' => DB::table('controller_positions')
                            ->where('callsign', $controllers[$i])
                            ->select('id')
                            ->value('id'),
                        'order' => $i + 1,
                        'created_at' => Carbon::now(),
                    ]
                );
            $i++;
        }

        DB::table('sid')->whereIn('identifier', ['GWC1L', 'GWC1F', 'HAZEL1L', 'HAZEL1F'])
            ->update(['handoff_id' => $handoffId]);

        // Doncaster SIDs
        DB::statement(
            'UPDATE `sid`
                SET `handoff_id` = (SELECT `id` FROM `handoffs` WHERE `key` = "EGCN_SID")
              WHERE `identifier` IN ("UPTON2A", "UPTON2B", "UPTON2C", "ROGAG1A", "ROGAG1C")'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Handle EGLL SIDs
        DB::statement(
            'UPDATE sid
                SET handoff_id = NULL
              WHERE identifier IN ("MAXIT1F", "MAXIT1G", "MODMI1J", "MODMI1K")'
        );

        // EGLF
        DB::table('sid')->whereIn('identifier', ['GWC1L', 'GWC1F', 'HAZEL1L', 'HAZEL1F'])
            ->update(['handoff_id' => null]);
        DB::table('handoffs')->where('key', 'EGLF_SID')->delete();

        // Doncaster SIDs
        DB::statement(
            'UPDATE `sid`
                SET `handoff_id` = NULL
              WHERE `identifier` IN ("UPTON2A", "UPTON2B", "UPTON2C", "ROGAG1A", "ROGAG1C")'
        );
    }
}
