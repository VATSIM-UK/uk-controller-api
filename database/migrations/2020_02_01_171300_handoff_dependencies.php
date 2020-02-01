<?php

use App\Models\Hold\Hold;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Hold\HoldRestriction;

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
