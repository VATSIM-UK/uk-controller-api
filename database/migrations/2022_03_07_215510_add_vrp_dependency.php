<?php

use App\Services\DependencyService;
use App\Services\VrpService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVrpDependency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            'DEPENDENCY_VISUAL_REFERENCE_POINTS',
            sprintf('%s@%s', VrpService::class, 'getVrpDependency'),
            false,
            'visual-reference-points.json',
            ['visual_reference_points', 'airfield_visual_reference_point']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency('DEPENDENCY_VISUAL_REFERENCE_POINTS');
    }
}
