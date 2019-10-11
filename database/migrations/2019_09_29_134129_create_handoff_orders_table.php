<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandoffOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handoff_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('handoff_id')
                ->comment('The handoff order that the entry belongs to');

            $table->unsignedBigInteger('controller_position_id')
                ->comment('The controller position');

            $table->unsignedTinyInteger('order')
                ->comment('The place of the controller position in the handoff order');

            $table->timestamps();

            // Foreign keys
            $table->foreign('handoff_id')
                ->references('id')
                ->on('handoffs')
                ->onDelete('cascade');

            $table->foreign('controller_position_id')
                ->references('id')
                ->on('controller_positions')
                ->onDelete('cascade');

            // Unique keys
            $table->unique(['handoff_id', 'controller_position_id']);
            $table->unique(['handoff_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('handoff_orders');
    }
}
