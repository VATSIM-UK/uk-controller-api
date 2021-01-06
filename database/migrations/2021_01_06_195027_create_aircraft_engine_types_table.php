<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAircraftEngineTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft_engine_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique()->comment('The type');
        });

        DB::table('aircraft_engine_types')->insert(
            [
                [
                    'type' => 'Jet',
                ],
                [
                    'type' => 'Piston',
                ],
                [
                    'type' => 'Turboprop',
                ],
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aircraft_engine_types');
    }
}
