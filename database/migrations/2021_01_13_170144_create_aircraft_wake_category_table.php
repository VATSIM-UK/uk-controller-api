<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAircraftWakeCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft_wake_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aircraft_id')->comment('The aircraft');
            $table->unsignedTinyInteger('wake_category_id')->comment('The wake category');
            $table->timestamps();

            $table->unique(['aircraft_id', 'wake_category_id'], 'aircraft_wake_category_unique');
            $table->foreign('aircraft_id')
                ->references('id')
                ->on('aircraft')
                ->cascadeOnDelete();
            $table->foreign('wake_category_id')
                ->references('id')
                ->on('wake_categories')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aircraft_wake_category');
    }
}
