<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroundStatusHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ground_status_history', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The callsign for assignment');
            $table->unsignedBigInteger('ground_status_id')->comment('The ground status');
            $table->timestamp('assigned_at')->comment('What time the assignment was done');
            $table->unsignedInteger('assigned_by')->comment('Who did the assignment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ground_status_history');
    }
}
