<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandoffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handoffs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sid_id');
            $table->unsignedBigInteger('controller_position_id');
            $table->unsignedTinyInteger('order')->comment('The place on the handoff order of this controller');
            $table->timestamps();

            // Foreign references
            $table->foreign('sid_id')
                ->references('id')
                ->on('sid')
                ->onDelete('cascade');

            $table->foreign('controller_position_id')
                ->references('id')
                ->on('controller_positions')
                ->onDelete('cascade');

            // Unique keys
            $table->unique(['sid_id', 'order']);
            $table->unique(['sid_id', 'controller_position_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('handoffs');
    }
}
