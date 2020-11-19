<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStandTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stand_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable()->comment('The stand type key');
            $table->timestamps();

            $table->unique('key');
        });

        DB::table('stand_types')
            ->insert(
                [
                    [
                        'key' => 'DOMESTIC',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'key' => 'INTERNATIONAL',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'key' => 'PASSENGER',
                        'created_at' => Carbon::now(),
                    ],
                    [
                        'key' => 'CARGO',
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
        Schema::dropIfExists('stand_types');
    }
}
