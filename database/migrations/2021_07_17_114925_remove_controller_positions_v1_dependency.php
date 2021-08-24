<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class RemoveControllerPositionsV1Dependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::deleteDependency('DEPENDENCY_CONTROLLER_POSITIONS');
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
