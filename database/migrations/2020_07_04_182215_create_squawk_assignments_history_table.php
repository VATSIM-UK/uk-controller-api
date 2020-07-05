<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquawkAssignmentsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('squawk_assignments_history', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The callsign to which the squawk was assigned');
            $table->string('code')->comment('The code that was assigned');
            $table->string('type')->comment('The type of allocation');
            $table->unsignedInteger('user_id')->nullable()->comment('The user that allocated the squawk');
            $table->timestamp('allocated_at')->comment('The time the squawk was allocated');

            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('squawk_assignments_history');
    }
}
