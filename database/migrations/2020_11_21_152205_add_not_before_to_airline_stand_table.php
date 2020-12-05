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
            $table->time('from')
                ->after('destination')
                ->index()
                ->nullable()
                ->comment('The earliest time in the day the stand can be allocated');
            $table->time('to')
                ->after('from')
                ->index()
                ->nullable()
                ->comment('The latest time in the day the stand can be allocated');
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
            $table->drop('from');
            $table->drop('to');
        });
    }
}
