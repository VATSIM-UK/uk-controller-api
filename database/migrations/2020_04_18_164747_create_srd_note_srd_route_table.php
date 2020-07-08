<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSrdNoteSrdRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `srd_note_srd_route` (
                `srd_note_id` SMALLINT(5) UNSIGNED NOT NULL,
                `srd_route_id` SMALLINT(5) UNSIGNED NOT NULL,
                PRIMARY KEY (`srd_note_id`, `srd_route_id`) USING BTREE,
                INDEX `srd_note_srd_route_srd_route_id_foreign` (`srd_route_id`) USING BTREE,
                CONSTRAINT `srd_note_srd_route_srd_note_id_foreign` FOREIGN KEY (`srd_note_id`) REFERENCES `srd_notes` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE,
                CONSTRAINT `srd_note_srd_route_srd_route_id_foreign` FOREIGN KEY (`srd_route_id`) REFERENCES `srd_routes` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
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
        Schema::dropIfExists('srd_note_srd_route');
    }
}
