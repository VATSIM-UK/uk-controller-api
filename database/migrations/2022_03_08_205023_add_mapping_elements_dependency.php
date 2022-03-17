<?php

use App\Services\DependencyService;
use App\Services\MappingService;
use Illuminate\Database\Migrations\Migration;

class AddMappingElementsDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            'DEPENDENCY_MAPPING_ELEMENTS',
            sprintf('%s@%s', MappingService::class, 'getMappingElementsDependency'),
            false,
            'mapping-elements.json',
            ['visual_reference_points']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency('DEPENDENCY_MAPPING_ELEMENTS');
    }
}
