<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDepartureRestrictionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_restriction_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Key for this type');
            $table->string('description')->comment('Description of the type');
            $table->timestamps();
        });

        DB::table('departure_restriction_types')
            ->insert(
                [
                    [
                        'key' => 'mdi',
                        'description' => 'Minimum Departure Interval',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'key' => 'adi',
                        'description' => 'Average Departure Interval',
                        'created_at' => Carbon::now(),
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
        Schema::dropIfExists('departure_restriction_types');
    }
}
