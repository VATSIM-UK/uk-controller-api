<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateMslAirfieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `msl_airfield` (
                `airfield_id` INT(10) UNSIGNED NOT NULL,
                `msl` SMALLINT(5) NOT NULL,
                `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`airfield_id`) USING BTREE,
                CONSTRAINT `msl_airfield_airfield_id_foreign` FOREIGN KEY (`airfield_id`) REFERENCES `airfield` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
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
        Schema::dropIfExists('msl_airfield');
    }
}
