<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitDiscreetSquawkAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `unit_discreet_squawk_assignments` (
                `callsign` VARCHAR(255) NOT NULL COMMENT 'The aircraft to which the code is assigned',
                `code` VARCHAR(4) NOT NULL COMMENT 'The assigned code',
                `unit` VARCHAR(255) NOT NULL COMMENT 'The unit that assigned the code',
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`callsign`) USING BTREE,
                CONSTRAINT `unit_discreet_squawk_assignments_callsign_foreign` FOREIGN KEY (`callsign`) REFERENCES `network_aircraft`(`callsign`) ON DELETE CASCADE,
                CONSTRAINT `unit_discreet_squawk_assignments_unit_foreign` FOREIGN KEY (`unit`) REFERENCES `unit_discrete_squawk_ranges`(`unit`) ON DELETE CASCADE,
                UNIQUE INDEX `unit_discreet_squawk_assignments_code_unit_unique` (`code`, `unit`) USING BTREE,
                INDEX `unit_discreet_squawk_assignments_code` (`code`) USING BTREE,
                INDEX `unit_discreet_squawk_assignments_unit` (`unit`) USING BTREE
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
        Schema::dropIfExists('unit_discreet_squawk_assignments');
    }
}
