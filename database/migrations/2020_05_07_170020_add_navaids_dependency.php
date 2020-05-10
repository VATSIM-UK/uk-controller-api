<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNavaidsDependency extends Migration
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
                    [
                        'key' => 'DEPENDENCY_NAVAIDS',
                        'uri' => 'navaid/dependency',
                        'local_file' => 'navaids.json',
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
        DB::table('dependencies')
            ->where('key', 'DEPENDENCY_NAVAIDS')
            ->delete();
    }
}
