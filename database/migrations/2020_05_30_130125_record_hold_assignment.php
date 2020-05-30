<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecordHoldAssignment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assigned_holds_history', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('callsign')->comment('The callsign of the aircraft');
            $blueprint->unsignedBigInteger('navaid_id')->nullable()->comment('The navaid the aircraft was assigned to hold at');
            $blueprint->unsignedInteger('assigned_by')->comment('The user who made the assignment');
            $blueprint->timestamp('assigned_at')->comment('The time the assignment was made');

            $blueprint->foreign('navaid_id')->references('id')->on('navaids')->onDelete('cascade');
            $blueprint->foreign('assigned_by')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assigned_holds_history');
    }
}
