<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldProfileHoldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `hold_profile_hold` (
                `hold_profile_id` INT(10) UNSIGNED NOT NULL COMMENT 'The id of the hold profile',
                `hold_id` INT(10) UNSIGNED NOT NULL COMMENT 'The id of the hold',
                PRIMARY KEY (`hold_profile_id`, `hold_id`) USING BTREE,
                INDEX `hold_profile_hold_hold_id_foreign` (`hold_id`) USING BTREE,
                CONSTRAINT `hold_profile_hold_hold_id_foreign` FOREIGN KEY (`hold_id`) REFERENCES `hold` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT `hold_profile_hold_hold_profile_id_foreign` FOREIGN KEY (`hold_profile_id`) REFERENCES `hold_profile` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
            )
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB;"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hold_profile_hold');
    }
}
