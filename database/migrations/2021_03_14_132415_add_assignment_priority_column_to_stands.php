<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssignmentPriorityColumnToStands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'stands',
            function (Blueprint $table) {
                $table->unsignedInteger('assignment_priority')
                    ->after('general_use')
                    ->default(100)
                    ->comment(
                        'How high a priority the stand gets when looking for assignments, lower value is higher priority'
                    );
                $table->index('assignment_priority');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'stands',
            function (Blueprint $table) {
                $table->dropColumn('assignment_priority');
            }
        );
    }
}
