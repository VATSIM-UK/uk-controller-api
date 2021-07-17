<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
class RemoveInitialAltitudesDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::deleteDependency('DEPENDENCY_INITIAL_ALTITUDES');
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
