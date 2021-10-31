<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseTableDependencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('database_table_dependency', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('database_table_id')->comment('The database table');
            $table->unsignedMediumInteger('dependency_id')->comment('The dependency');
            $table->timestamps();

            $table->foreign('database_table_id', 'database_table_dependency_database')
                ->references('id')
                ->on('database_tables')
                ->cascadeOnDelete();

            $table->foreign('dependency_id', 'database_table_dependency_dependency')
                ->references('id')
                ->on('dependencies')
                ->cascadeOnDelete();

            $table->unique(['database_table_id', 'dependency_id'], 'database_table_dependency_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('database_table_dependency');
    }
}
