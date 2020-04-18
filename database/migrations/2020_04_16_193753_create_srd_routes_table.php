<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSrdRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srd_routes', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('origin')->comment('The origin navaid or airport for the route');
            $table->string('destination')->comment('The destination navaid or airport for the route');
            $table->integer('minimum_level')->nullable()->comment('The minimum flight level for the route');
            $table->integer('maximum_level')->comment('The maximum flight level for the route');
            $table->string('route_segment')->comment('The route segment');
            $table->string('sid')->comment('The SID used at the start of the route')->nullable();
            $table->string('star')->comment('The STAR used at the end of the route')->nullable();

            // Indexes for optimised searching
            $table->index('origin');
            $table->index('destination');
            $table->index('minimum_level');
            $table->index('maximum_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srd_routes');
    }
}
