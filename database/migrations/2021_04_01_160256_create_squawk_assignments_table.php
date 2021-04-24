<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSquawkAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `squawk_assignments` (
                `callsign` VARCHAR(255) NOT NULL COMMENT 'The aircraft to which the code is assigned',
                `code` VARCHAR(4) NOT NULL COMMENT 'The assigned code',
                `assignment_type` VARCHAR(255) NOT NULL COMMENT 'The type of assignment that was made',
                `created_at` TIMESTAMP,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`callsign`) USING BTREE,
                CONSTRAINT `squawk_assignments_callsign_foreign` FOREIGN KEY (`callsign`) REFERENCES `network_aircraft`(`callsign`) ON DELETE CASCADE,
                UNIQUE INDEX `squawk_assignments` (`code`) USING BTREE
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
        Schema::dropIfExists('squawk_assignments');
    }
}
