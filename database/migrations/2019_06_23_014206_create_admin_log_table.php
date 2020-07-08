<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `admin_log` (
                `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                `user_id` INT(10) UNSIGNED NOT NULL COMMENT 'The user who performed the action',
                `request_uri` TEXT(65535) NOT NULL COMMENT 'The uri that was hit' COLLATE 'utf8mb4_unicode_ci',
                `request_body` TEXT(65535) NULL DEFAULT NULL COMMENT 'The body of the request, if any' COLLATE 'utf8mb4_unicode_ci',
                `log_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The time the action occurred',
                PRIMARY KEY (`id`) USING BTREE,
                INDEX `admin_log_user_id_foreign` (`user_id`) USING BTREE,
                CONSTRAINT `admin_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
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
        Schema::dropIfExists('admin_log');
    }
}
