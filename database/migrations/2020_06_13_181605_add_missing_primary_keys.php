<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingPrimaryKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hold_profile_hold', function (Blueprint $table) {
            $table->primary(['hold_profile_id', 'hold_id']);
        });

        Schema::table('srd_note_srd_route', function (Blueprint $table) {
            $table->primary(['srd_note_id', 'srd_route_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
