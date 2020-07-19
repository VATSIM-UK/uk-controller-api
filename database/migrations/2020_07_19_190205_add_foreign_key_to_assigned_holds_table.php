<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToAssignedHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assigned_holds', function (Blueprint $table) {
            $table->foreign('callsign')
                ->references('callsign')
                ->on('network_aircraft')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assigned_holds', function (Blueprint $table) {
            $table->dropForeign('assigned_holds_callsign_foreign');
        });
    }
}
