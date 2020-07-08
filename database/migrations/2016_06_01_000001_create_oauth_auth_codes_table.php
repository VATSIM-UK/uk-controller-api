<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Custom version of the Laravel Passport migration
 * required due to string primary keys.
 */
class CreateOauthAuthCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `oauth_auth_codes` (
                `id` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `user_id` BIGINT(19) NOT NULL,
                `client_id` INT(10) NOT NULL,
                `scopes` TEXT(65535) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
                `revoked` TINYINT(1) NOT NULL,
                `expires_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`) USING BTREE
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
        Schema::dropIfExists('oauth_auth_codes');
    }
}
