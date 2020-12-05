<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotBeforeToAirlineStandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->time('not_before')
                ->after('destination')
                ->index()
                ->nullable()
                ->comment('The earliest time in the day the stand can be allocated');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->drop('not_before');
        });
    }
}
