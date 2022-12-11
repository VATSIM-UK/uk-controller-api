<?php

use App\Services\DependencyService;
use App\Services\IntentionCode\FirExitPointService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    private const DEPENDENCY_KEY = 'DEPENDENCY_FIR_EXIT_POINTS';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            self::DEPENDENCY_KEY,
            sprintf('%s@%s', FirExitPointService::class, 'getFirExitDependency'),
        false,
            'fir_exit_points.json',
            ['fir_exit_points']
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
};
