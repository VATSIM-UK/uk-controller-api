<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class RemoveOldWakeDependencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::deleteDependency('DEPENDENCY_WAKE');
        DependencyService::deleteDependency('DEPENDENCY_RECAT');
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
