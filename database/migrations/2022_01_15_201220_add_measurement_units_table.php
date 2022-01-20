<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMeasurementUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measurement_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit')->comment('The units');
            $table->timestamps();
        });

        DB::table('measurement_units')
            ->insert(
                [
                    [
                        'unit' => 's',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'unit' => 'nm',
                        'created_at' => Carbon::now(),
                    ]
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
        Schema::dropIfExists('measurement_units');
    }
}
