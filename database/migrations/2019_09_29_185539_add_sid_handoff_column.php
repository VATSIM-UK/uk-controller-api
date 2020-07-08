<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSidHandoffColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->unsignedBigInteger('handoff_id')
                ->after('initial_altitude')
                ->nullable()
                ->comment('The handoff order that applies to this departure');

            $table->foreign('handoff_id')
                ->references('id')
                ->on('handoffs');
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
            $table->dropForeign('sid_handoff_id_foreign');
            $table->dropColumn('handoff_id');
        });
    }
}
