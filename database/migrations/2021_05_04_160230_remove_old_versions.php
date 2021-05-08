<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveOldVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('version')
            ->where('allowed', true)
            ->where('version', '<>', '2.14.1')
            ->update(['allowed' => false, 'updated_at' => Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
