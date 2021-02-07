<?php

use Illuminate\Database\Migrations\Migration;

class SetDeletedTimesOnVersions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('version')->where('allowed', false)
            ->update(['deleted_at' => DB::raw('`updated_at`')]);
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
