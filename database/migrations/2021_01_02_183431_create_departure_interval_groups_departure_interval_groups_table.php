<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureIntervalGroupsDepartureIntervalGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sid_departure_interval_group_sid_departure_interval_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_group_id')->comment('The group of the lead departure');
            $table->unsignedBigInteger('follow_group_id')->comment('The group of the following departure');
            $table->unsignedSmallInteger('interval')->comment('The time in seconds between departures in each group');

            $table->unique(['lead_group_id', 'follow_group_id'], 'departure_interval_group_ids');

            $table->foreign('lead_group_id', 'lead_group_id_foreign')
                ->references('id')
                ->on('sid_departure_interval_groups')->cascadeOnDelete();

            $table->foreign('follow_group_id', 'foreign_group_id_foreign')
                ->references('id')
                ->on('sid_departure_interval_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sid_departure_interval_group_sid_departure_interval_group');
    }
}
