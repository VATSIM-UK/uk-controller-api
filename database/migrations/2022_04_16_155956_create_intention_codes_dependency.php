<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class CreateIntentionCodesDependency extends Migration
{
    private const DEPENDENCY_KEY = 'DEPENDENCY_INTENTION_CODES';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::createDependency(
            self::DEPENDENCY_KEY,
            sprintf('%s@getIntentionCodesDependency', DependencyService::class),
            false,
            'intention-codes.json',
            ['intention_codes']
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
