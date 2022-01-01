<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompositeIndexToStandReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stand_reservations', function (Blueprint $table) {
            $table->index(['start', 'end'], 'stand_reservations_start_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stand_reservations', function (Blueprint $table) {
            $table->dropIndex('stand_reservations_start_end');
        });
    }
}
