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
        Schema::create('engine_types', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique()->comment('The type');
            $table->string('euroscope_type')->unique()->comment('The type in euroscope');
        });

        DB::table('aircraft_engine_types')->insert(
            [
                [
                    'type' => 'Jet',
                    'euroscope_type' => 'J',
                ],
                [
                    'type' => 'Piston',
                    'euroscope_type' => 'P',
                ],
                [
                    'type' => 'Turboprop',
                    'euroscope_type' => 'T',
                ],
                [
                    'type' => 'Electric',
                    'euroscope_type' => 'E',
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
        Schema::dropIfExists('engine_types');
    }
}
