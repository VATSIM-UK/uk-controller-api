<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDependenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependencies', function(Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('uri')->comment('Where the dependency can be downloaded from');
            $table->string('local_file')->comment('The local file where the dependency should be stored');
            $table->dateTime('updated_at');

            // Keys
            $table->unique('local_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependencies');
    }
}
