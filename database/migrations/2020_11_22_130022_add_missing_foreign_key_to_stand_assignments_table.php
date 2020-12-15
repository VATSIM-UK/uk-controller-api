<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeyToStandAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // The data type is wrong, fix this
        DB::statement('ALTER TABLE `stand_assignments` MODIFY COLUMN `stand_id` BIGINT(20) UNSIGNED');

        Schema::table('stand_assignments', function (Blueprint $table) {
            $table->foreign('stand_id')->references('id')->on('stands')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stand_assignments', function (Blueprint $table) {
            $table->dropForeign('stand_assignments_stand_id_foreign');
        });

        DB::statement('ALTER TABLE `stand_assignments` MODIFY COLUMN `stand_id` INT(10) UNSIGNED');
    }
}
