<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWakeCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wake_categories', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('code')->comment('The code for the category');
            $table->string('description')->comment('The description for the category');
            $table->timestamps();

            // Keys
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wake_categories');
    }
}
