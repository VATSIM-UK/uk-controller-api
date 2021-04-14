<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSidsDependency extends Migration
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
                'key' => 'DEPENDENCY_SIDS',
                'action' => 'SidController@getSidsDependency',
                'local_file' => 'sids.json',
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
        DB::table('dependencies')->where('key', 'DEPENDENCY_SIDS')->delete();
    }
}
