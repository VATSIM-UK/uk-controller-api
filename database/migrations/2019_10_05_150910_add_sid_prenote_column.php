<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSidPrenoteColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->unsignedBigInteger('prenote_id')
                ->after('handoff_id')
                ->nullable();

            $table->foreign('prenote_id')
                ->references('id')
                ->on('prenotes')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->dropForeign('sid_prenote_id_foreign');
            $table->dropColumn('prenote_id');
        });
    }
}
