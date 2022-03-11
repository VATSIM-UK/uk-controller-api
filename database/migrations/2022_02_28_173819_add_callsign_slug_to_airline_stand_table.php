<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCallsignSlugToAirlineStandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->string('callsign_slug')
                ->nullable()
                ->after('destination')
                ->index()
                ->comment('Prefer this stand for callsigns matching the slug');
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
            $table->dropColumn('callsign_slug');
        });
    }
}
