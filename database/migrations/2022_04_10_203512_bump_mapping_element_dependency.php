<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class BumpMappingElementDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::touchDependencyByKey('DEPENDENCY_MAPPING_ELEMENTS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::touchDependencyByKey('DEPENDENCY_MAPPING_ELEMENTS');
    }
}
