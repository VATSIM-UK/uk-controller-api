<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSquawkAllocationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('squawk_allocation_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('callsign')->comment('The callsign the squawk was assigned to');
            $table->string('squawk')->comment('The assigned squawk code');
            $table->boolean('new')->comment('0 = updated existing allocation, 1 = New allocation');
            $table->unsignedInteger('allocated_by')->comment('Who allocated the squawk');
            $table->timestamp('allocated_at')->comment('What time the squawk allocation occured');

            // Foreign key so we can always reference the user.
            $table->foreign('allocated_by')
                ->references('id')
                ->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('squawk_allocation_history');
    }
}
