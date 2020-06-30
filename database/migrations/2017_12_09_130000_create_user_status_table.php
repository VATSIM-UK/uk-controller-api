<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `user_status` (
                `id` TINYINT(3) UNSIGNED NOT NULL,
                `status` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (`id`) USING BTREE
            )COLLATE='utf8mb4_unicode_ci';"
        );

        DB::table('user_status')->insert(
            [
                [
                    'id' => 1,
                    'status' => 'Active'
                ],
                [
                    'id' => 2,
                    'status' => 'Banned'
                ],
                [
                    'id' => 3,
                    'status' => 'Disabled'
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_status');
    }
}
