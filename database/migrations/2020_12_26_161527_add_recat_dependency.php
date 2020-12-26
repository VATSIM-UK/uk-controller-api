<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRecatDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dependencies')
            ->insert(
                [
                    'key' => 'DEPENDENCY_RECAT',
                    'uri' => 'wake-category/recat/dependency',
                    'local_file' => 'recat.json',
                    'created_at' => Carbon::now(),
                ],
            );
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
