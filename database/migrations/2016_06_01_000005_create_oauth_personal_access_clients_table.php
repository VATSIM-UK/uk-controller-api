<?php


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Custom version of the Laravel Passport migration
 * required due to string primary keys.
 */
class CreateOauthPersonalAccessClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `oauth_personal_access_clients` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `client_id` INT(10) NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`id`) USING BTREE,
                INDEX `oauth_personal_access_clients_client_id_index` (`client_id`) USING BTREE
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
        Schema::dropIfExists('oauth_personal_access_clients');
    }
}
