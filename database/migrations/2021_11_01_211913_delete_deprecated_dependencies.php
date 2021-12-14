<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class DeleteDeprecatedDependencies extends Migration
{
    const DEPENDENCIES_TO_DELETE = [
        'DEPENDENCY_AIRFIELD_OWNERSHIP',
        'DEPENDENCY_PRENOTE',
        'DEPENDENCY_HANDOFF',
        'DEPENDENCY_SID_HANDOFF',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::DEPENDENCIES_TO_DELETE as $dependency) {
            DependencyService::deleteDependency($dependency);
        }
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
