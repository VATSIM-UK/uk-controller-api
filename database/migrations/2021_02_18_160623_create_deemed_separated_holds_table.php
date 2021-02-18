<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeemedSeparatedHoldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deemed_separated_holds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('first_hold_id')->comment('The first hold in the pair');
            $table->unsignedInteger('second_hold_id')->comment('The second hold in the pair');
            $table->unsignedInteger('vsl_insert_distance')
                ->comment('The distance at and below which holding aircraft should populate the VSL');
            $table->unique(['first_hold_id', 'second_hold_id'], 'deemed_separated_holds_pairs');

            $table->foreign('first_hold_id')->references('id')->on('holds')->cascadeOnDelete();
            $table->foreign('second_hold_id')->references('id')->on('holds')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deemed_separated_holds');
    }
}
