<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement(
            "CREATE TABLE `admin` (
                `user_id` INT(10) UNSIGNED NOT NULL,
                `email` VARCHAR(255) NOT NULL COMMENT 'Email address for user, used as username' COLLATE 'utf8mb4_unicode_ci',
                `password` VARCHAR(255) NOT NULL COMMENT 'Hashed admin password for the user' COLLATE 'utf8mb4_unicode_ci',
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`user_id`) USING BTREE,
                CONSTRAINT `admin_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
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
        Schema::dropIfExists('admin');
    }
}
