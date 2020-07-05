<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrcamSquawkAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `orcam_squawk_assignments` (
                `callsign` VARCHAR(255) NOT NULL,
                `code` VARCHAR(4) NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`callsign`) USING BTREE,
                CONSTRAINT `orcam_squawk_assignments_callsign_foreign` FOREIGN KEY (`callsign`) REFERENCES `network_aircraft`(`callsign`) ON DELETE CASCADE,
                UNIQUE INDEX `orcam_squawk_assignments` (`code`) USING BTREE
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
        Schema::dropIfExists('orcam_squawk_assignments');
    }
}
