<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitDiscreteSquawkCodeRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_discrete_squawk_range_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_discrete_squawk_range_id')
                ->comment('The squawk code that is referenced');
            $table->json('rule')->comment('Data about the rule');
            $table->timestamps();

            $table->foreign('unit_discrete_squawk_range_id', 'unit_discrete_squawk_range_id')
                ->references('id')
                ->on('unit_discrete_squawk_ranges')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_discrete_squawk_range_rules');
    }
}
