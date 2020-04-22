<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDependencyUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependency_user', function (Blueprint $table) {
            $table->unsignedMediumInteger('dependency_id')->comment('The dependency');
            $table->unsignedInteger('user_id')->comment('The user the dependency belongs to');
            $table->timestamps();

            $table->primary(['dependency_id', 'user_id']);
            $table->foreign('dependency_id')->references('id')->on('dependencies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependency_user');
    }
}
