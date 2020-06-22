<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateMslTmaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `msl_tma` (
                `tma_id` INT(10) UNSIGNED NOT NULL,
                `msl` SMALLINT(5) UNSIGNED NOT NULL,
                `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`tma_id`) USING BTREE,
                CONSTRAINT `msl_tma_tma_id_foreign` FOREIGN KEY (`tma_id`) REFERENCES `tma` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
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
        Schema::dropIfExists('msl_tma');
    }
}
