<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirfieldPairingSquawkAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `airfield_pairing_squawk_assignments` (
                `callsign` VARCHAR(255) NOT NULL COMMENT 'The aircraft to which the code is assigned',
                `code` VARCHAR(4) NOT NULL COMMENT 'The assigned code',
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`callsign`) USING BTREE,
                CONSTRAINT `airfield_pairing_squawk_assignments_callsign_foreign` FOREIGN KEY (`callsign`) REFERENCES `network_aircraft`(`callsign`) ON DELETE CASCADE,
                UNIQUE INDEX `airfield_pairing_squawk_assignments` (`code`) USING BTREE
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
        Schema::dropIfExists('airfield_pairing_squawk_assignments');
    }
}
