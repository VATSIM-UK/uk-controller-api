<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Custom version of the Laravel Passport migration
 * required due to string primary keys.
 */
class CreateOauthRefreshTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `oauth_refresh_tokens` (
                `id` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `access_token_id` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                `revoked` TINYINT(1) NOT NULL,
                `expires_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`) USING BTREE,
                INDEX `oauth_refresh_tokens_access_token_id_index` (`access_token_id`) USING BTREE
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
        Schema::dropIfExists('oauth_refresh_tokens');
    }
}
