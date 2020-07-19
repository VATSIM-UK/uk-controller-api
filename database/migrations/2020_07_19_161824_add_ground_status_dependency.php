<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddGroundStatusDependency extends Migration
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
                    'key' => 'DEPENDENCY_GROUND_STATUSES',
                    'uri' => 'ground-status/dependency',
                    'local_file' => 'ground-statuses.json',
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
        DB::table('dependencies')->where('key', 'DEPENDENCY_GROUND_STATUSES')->delete();
    }
}
