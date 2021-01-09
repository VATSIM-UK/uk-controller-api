<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateFlightRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_rules', function (Blueprint $table) {
            $table->id();
            $table->string('euroscope_key')->unique()->comment('The key for the rules in EuroScope');
            $table->string('description')->comment('Description');
            $table->timestamps();
        });

        DB::table('flight_rules')
            ->insert(
                [
                    [
                        'euroscope_key' => 'V',
                        'description' => 'VFR',
                    ],
                    [
                        'euroscope_key' => 'I',
                        'description' => 'IFR',
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
        Schema::dropIfExists('flight_rules');
    }
}
