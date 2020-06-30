<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSrdNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `srd_notes` (
                `id` SMALLINT(5) UNSIGNED NOT NULL,
                `note_text` TEXT(65535) NOT NULL COLLATE 'utf8mb4_unicode_ci',
                PRIMARY KEY (`id`) USING BTREE
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
        Schema::dropIfExists('srd_notes');
    }
}
