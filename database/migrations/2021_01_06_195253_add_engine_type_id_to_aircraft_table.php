<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEngineTypeIdToAircraftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->unsignedBigInteger('aircraft_engine_type_id')->nullable()->after('code');
            $table->foreign('aircraft_engine_type_id', 'aircraft_engine_type_id')
                ->references('id')
                ->on('aircraft_engine_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropForeign('aircraft_engine_type_id');
            $table->dropColumn('aircraft_engine_type_id');
        });
    }
}
