<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class WakeSchemeIntervalsDependencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DependencyService::setConcernedTablesForDependency(
            'DEPENDENCY_WAKE_SCHEME',
            [
                'wake_category_schemes',
                'wake_categories',
                'departure_wake_intervals',
            ]
        );
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
