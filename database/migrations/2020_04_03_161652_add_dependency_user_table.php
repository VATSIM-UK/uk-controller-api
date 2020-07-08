<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddDependencyUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement(
            "CREATE TABLE `dependency_user` (
                `dependency_id` MEDIUMINT(7) UNSIGNED NOT NULL COMMENT 'The dependency',
                `user_id` INT(10) UNSIGNED NOT NULL COMMENT 'The user the dependency belongs to',
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`dependency_id`, `user_id`) USING BTREE,
                INDEX `dependency_user_user_id_foreign` (`user_id`) USING BTREE,
                CONSTRAINT `dependency_user_dependency_id_foreign` FOREIGN KEY (`dependency_id`) REFERENCES `dependencies` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT `dependency_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
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
        Schema::dropIfExists('dependency_user');
    }
}
