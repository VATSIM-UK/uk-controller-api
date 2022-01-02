<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRunwayConstraintToSidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->foreign('runway_id', 'sid_runway_id')
                ->references('id')
                ->on('runways')
                ->cascadeOnDelete();

            $table->unique(['runway_id', 'identifier'], 'sid_runway_identifier');
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
            $table->dropIndex('sid_runway_identifier');
            $table->dropIndex('sid_runway_id');
        });
    }
}
