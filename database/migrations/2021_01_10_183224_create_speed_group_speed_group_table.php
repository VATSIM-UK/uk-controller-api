<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpeedGroupSpeedGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speed_group_speed_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_speed_group_id')->comment('The first speed group to depart');
            $table->unsignedBigInteger('follow_speed_group_id')->comment('The second speed group depart');
            $table->integer('penalty')->comment('The time penalty in seconds for the second group');
            $table->timestamps();

            $table->unique(['lead_speed_group_id', 'follow_speed_group_id'], 'speed_group_pairs');
            $table->foreign('lead_speed_group_id', 'lead_speed_group_foreign')
                ->references('id')
                ->on('speed_groups')
                ->cascadeOnDelete();

            $table->foreign('follow_speed_group_id', 'follow_speed_group_foreign')
                ->references('id')
                ->on('speed_groups')
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
        Schema::dropIfExists('speed_group_speed_group');
    }
}
