<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStandAssignmentsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stand_assignments_history', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The callsign to which the stand was assigned');
            $table->unsignedBigInteger('stand_id')->comment('The stand that was assigned');
            $table->unsignedInteger('user_id')->nullable()->comment('The user that allocated the squawk');
            $table->timestamp('assigned_at')->comment('The time the squawk was allocated');
            $table->softDeletes();

            $table->index('callsign');
            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
            $table->foreign('stand_id')->references('id')->on('stands')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stand_assignments_history');
    }
}
