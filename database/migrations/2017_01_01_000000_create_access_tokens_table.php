<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Custom version of the Laravel Passport migration
 * required due to string primary keys.
 */
class CreateAccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `oauth_access_tokens` (
                `id` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `user_id` BIGINT(19) NULL DEFAULT NULL,
                `client_id` INT(10) NOT NULL,
                `name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                `scopes` TEXT(65535) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                `revoked` TINYINT(1) NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                `expires_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`) USING BTREE,
                INDEX `oauth_access_tokens_user_id_index` (`user_id`) USING BTREE
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
        Schema::dropIfExists('oauth_access_tokens');
    }
}
