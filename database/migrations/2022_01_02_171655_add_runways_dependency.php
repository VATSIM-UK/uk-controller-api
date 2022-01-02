<?php

use App\Services\DependencyService;
use App\Services\RunwayService;
use Illuminate\Database\Migrations\Migration;

class AddRunwaysDependency extends Migration
{
    private const DEPENDENCY_KEY = 'DEPENDENCY_RUNWAYS';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            self::DEPENDENCY_KEY,
            sprintf('%s@%s', RunwayService::class, 'getRunwaysDependency'),
            false,
            'runways.json',
            ['runways', 'runway_runway']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DependencyService::deleteDependency(self::DEPENDENCY_KEY);
    }
}
