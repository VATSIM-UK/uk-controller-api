<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorSquawkRangeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Firstly, create the range owner table
        Schema::create('squawk_range_owner', function (Blueprint $table) {
            $table->increments('id')->comment('The id of the owner');
        });

        // We need to drop the squawk_range table - as this will become superseded.
        Schema::dropIfExists('squawk_range');

        // Next, create the new squawk range table. Every range should be owned by exactly one owner.
        Schema::create('squawk_range', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('squawk_range_owner_id')->comment('Which squawk owner owns this range');
            $table->foreign('squawk_range_owner_id')
                ->references('id')
                ->on('squawk_range_owner')
                ->onDelete('cascade');
            $table->char('start', 4)->comment('Starting squawk of the range');
            $table->char('stop', 4)->comment('Ending squawk of the range');
            $table->enum('rules', ['A', 'V', 'I'])->default('A')->comment('Flight rules for which range may be used');
            $table->boolean('allow_duplicate')->default(0)
                ->comment('Can squawks in this range be assigned multiple times');
        });

        // Next, we have a "Unit" range owner - that is, a specific airfield or ATC unit.
        Schema::create('squawk_unit', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unit')->comment('Unit name, e.g. EGKK, LON, SCO');
            $table->unsignedInteger('squawk_range_owner_id')->comment('Associated squawk owner');
            $table->unique('squawk_range_owner_id');
            $table->foreign('squawk_range_owner_id')
                ->references('id')
                ->on('squawk_range_owner')
                ->onDelete('cascade');
        });

        // Finally, we create the squawk_general range owner - squawks for particular routes, ORCAM, CCAMS or pairings
        Schema::create('squawk_general', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('squawk_range_owner_id')->comment('Associated squawk owner');
            $table->unique('squawk_range_owner_id');
            $table->foreign('squawk_range_owner_id')
                ->references('id')
                ->on('squawk_range_owner')
                ->onDelete('cascade');
            $table->string('departure_ident', 5)->nullable()->comment('Departure identifier e.g. EGKK, EG or CCAMS');
            $table->string('arrival_ident', 5)->nullable()->comment('Arrival identifier e.g. EGKK, EG or CCAMS');
            $table->unique(['departure_ident', 'arrival_ident']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the squawk general_table
        Schema::dropIfExists('squawk_general');

        // Drop the squawk_unit table
        Schema::dropIfExists('squawk_unit');

        // Drop squawk_range and recreate.
        Schema::dropIfExists('squawk_range');
        Schema::create('squawk_range', function (Blueprint $table) {
            $table->increments('id');
            $table->text('departure_ident')->nullable();
            $table->text('arrival_ident')->nullable();
            $table->text('start', 4);
            $table->text('stop', 4);
        });


        // Drop the range owner table
        Schema::dropIfExists('squawk_range_owner');
    }
}
