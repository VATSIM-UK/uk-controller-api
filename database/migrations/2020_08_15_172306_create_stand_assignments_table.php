<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStandAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `stand_assignments` (
                `callsign` VARCHAR(255) NOT NULL COMMENT 'The aircraft to which the stand is assigned',
                `stand_id` INTEGER(10) UNSIGNED NOT NULL COMMENT 'The stand that has been assigned',
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`callsign`) USING BTREE,
                CONSTRAINT `stand_assignments_callsign_foreign` FOREIGN KEY (`callsign`) REFERENCES `network_aircraft`(`callsign`) ON DELETE CASCADE,
                UNIQUE INDEX `stand_assignments_stand_id` (`stand_id`) USING BTREE
            )
            COLLATE='utf8mb4_unicode_ci';"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stand_assignments');
    }
}
