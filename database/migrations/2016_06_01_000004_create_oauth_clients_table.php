<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Custom version of the Laravel Passport migration
 * required due to string primary keys.
 */
class CreateOauthClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `oauth_clients` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT(19) NULL DEFAULT NULL,
                `name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `secret` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `redirect` TEXT(65535) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `personal_access_client` TINYINT(1) NOT NULL,
                `password_client` TINYINT(1) NOT NULL,
                `revoked` TINYINT(1) NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`) USING BTREE,
                INDEX `oauth_clients_user_id_index` (`user_id`) USING BTREE
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
        Schema::dropIfExists('oauth_clients');
    }
}
