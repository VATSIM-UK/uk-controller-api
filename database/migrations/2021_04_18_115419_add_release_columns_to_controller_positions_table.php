<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReleaseColumnsToControllerPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controller_positions', function (Blueprint $table) {
            $table->boolean('requests_departure_releases')
                ->after('frequency')
                ->default(false)
                ->comment('Whether the controller position can request departure releases');

            $table->boolean('receives_departure_releases')
                ->after('requests_departure_releases')
                ->default(false)
                ->comment('Whether the controller position can receive departure release requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('controller_positions', function (Blueprint $table) {
            $table->dropColumn('requests_departure_releases');
            $table->dropColumn('receives_departure_releases');
        });
    }
}
