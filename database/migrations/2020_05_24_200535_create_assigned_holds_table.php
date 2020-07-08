<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAssignedHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `assigned_holds` (
                `callsign` VARCHAR(255) NOT NULL COMMENT 'The aircraft that is holding',
                `navaid_id` BIGINT UNSIGNED NOT NULL COMMENT 'The navaid that the aircraft has been assigned to hold',
                `created_at` TIMESTAMP NULL,
                `updated_at` TIMESTAMP NULL,
                PRIMARY KEY (`callsign`) USING BTREE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci'"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assigned_holds');
    }
}
