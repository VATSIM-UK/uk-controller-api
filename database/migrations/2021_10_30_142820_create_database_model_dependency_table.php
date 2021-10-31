<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseModelDependencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_model_dependency', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('database_model_id')->comment('The model');
            $table->unsignedMediumInteger('dependency_id')->comment('The dependency');
            $table->timestamps();

            $table->foreign('database_model_id', 'database_model_dependency_model')
                ->references('id')
                ->on('database_models')
                ->cascadeOnDelete();

            $table->foreign('dependency_id', 'database_model_dependency_dependency')
                ->references('id')
                ->on('dependencies')
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
        Schema::dropIfExists('database_model_dependency');
    }
}
