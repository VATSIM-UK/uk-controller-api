<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialHeadingToSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->smallInteger('initial_heading')
                ->nullable()
                ->comment('The initial heading for the plugin to set for this SID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->drop('initial_heading');
        });
    }
}
