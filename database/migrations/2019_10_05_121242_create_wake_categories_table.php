<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
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
