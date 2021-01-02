<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIntervalGroupToSidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->unsignedBigInteger('sid_departure_interval_group_id')
                ->nullable()
                ->after('prenote_id')
                ->comment('The group that the SID is in for intervals');

            $table->foreign('sid_departure_interval_group_id', 'sids_departure_interval_group')
                ->references('id')
                ->on('sid_departure_interval_groups');
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
            $table->dropForeign('sids_departure_interval_group');
            $table->dropColumn('sid_departure_interval_group_id');
        });
    }
}
