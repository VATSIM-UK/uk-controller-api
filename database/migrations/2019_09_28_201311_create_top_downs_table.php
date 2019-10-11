<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopDownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_downs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('airfield_id');
            $table->unsignedBigInteger('controller_position_id');
            $table->unsignedTinyInteger('order')->comment('The place in the top down order of this controller');
            $table->timestamps();

            // Foreign keys
            $table->foreign('airfield_id')
                ->references('id')
                ->on('airfield')
                ->onDelete('cascade');
            $table->index('airfield_id');

            $table->foreign('controller_position_id')
                ->references('id')
                ->on('controller_positions')
                ->onDelete('cascade');
            $table->index('controller_position_id');

            // Unique keys
            $table->unique(['airfield_id', 'controller_position_id']);
            $table->unique(['airfield_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_downs');
    }
}
