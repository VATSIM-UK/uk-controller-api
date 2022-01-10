<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCidToStandReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stand_reservations', function (Blueprint $table) {
            $table->unsignedInteger('cid')
                ->nullable()
                ->after('callsign')
                ->comment('The vatsim CID that this reservation belongs to');

            $table->index('cid', 'stand_reservations_cid');
            $table->index(['cid', 'start', 'end'], 'stand_reservations_cid_start_end');
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
            $table->dropIndex('stand_reservations_cid');
            $table->dropIndex('stand_reservations_start_end');
            $table->dropColumn('cid');
        });
    }
}
