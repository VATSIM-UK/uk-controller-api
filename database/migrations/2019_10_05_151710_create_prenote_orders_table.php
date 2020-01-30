<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrenoteOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prenote_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prenote_id')
                ->comment('The prenote that the entry belongs to');

            $table->unsignedBigInteger('controller_position_id')
                ->comment('The controller position');

            $table->unsignedTinyInteger('order')
                ->comment('The place of the controller position in the prenote order');

            $table->timestamps();

            // Foreign keys
            $table->foreign('prenote_id')
                ->references('id')
                ->on('prenotes')
                ->onDelete('cascade');

            $table->foreign('controller_position_id')
                ->references('id')
                ->on('controller_positions')
                ->onDelete('cascade');

            // Unique keys
            $table->unique(['prenote_id', 'controller_position_id']);
            $table->unique(['prenote_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prenote_orders');
    }
}
