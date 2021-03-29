<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddWakeSchemesDependency extends Migration
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
                'key' => 'DEPENDENCY_WAKE_SCHEME',
                'action' => 'WakeController@getWakeSchemesDependency',
                'local_file' => 'wake-schemes.json',
                'created_at' => Carbon::now(),
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
        DB::table('dependencies')->where('key', 'DEPENDENCY_WAKE_SCHEME')->delete();
    }
}
