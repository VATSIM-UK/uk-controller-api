<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
                `callsign` VARCHAR(255) NOT NULL,
                `code` VARCHAR(4) NOT NULL,
                `type` ENUM('CCAMS', 'ORCAM', 'PAIR', 'UNIT') NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`callsign`) USING BTREE,
                CONSTRAINT `squawk_assignments_callsign_foreign` FOREIGN KEY (`callsign`) REFERENCES `network_aircraft`(`callsign`) ON DELETE CASCADE,
                INDEX `squawk_assignments_type`  (`type`) USING BTREE,
                UNIQUE INDEX `squawk_assignments_code_type` (`code`, `type`) USING BTREE
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
