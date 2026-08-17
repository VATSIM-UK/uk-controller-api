<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HandoffDependencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dependencies')->insert(
            [
                [
                    'key' => 'DEPENDENCY_HANDOFF',
                    'uri' => 'handoff',
                    'local_file' => 'handoffs.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'key' => 'DEPENDENCY_SID_HANDOFF',
                    'uri' => 'handoffs',
                    'local_file' => 'sid-handoffs.json',
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
        DB::table('handoffs')->whereIn('key', ['DEPENDENCY_SID_HANDOFF', 'DEPENDENCY_HANDOFF'])->delete();
    }
}
