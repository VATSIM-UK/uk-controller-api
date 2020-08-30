<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddEnrouteReleaseTypeDependency extends Migration
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
                    'key' => 'DEPENDENCY_ENROUTE_RELEASE_TYPES',
                    'uri' => 'release/enroute/types',
                    'local_file' => 'enroute-release-types.json',
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
        DB::table('dependencies')->where('key', 'DEPENDENCY_ENROUTE_RELEASE_TYPES')->delete();
    }
}
