<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSquawkReservedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'squawk_reserved',
            function (Blueprint $table) {
                $table->increments('id');
                $table->text('squawk', 4);
            }
        );

        $reserved_squawks = ["1200", "7500", "7700", "7600", "7000", "7777", "1000", "2000", "7007", "0024","0033","0450","1177","4520","7001","7002","7003","7004","7005","7006","7010","7401", "0002","7776","0010","0011","0012","0013","0440","2620","2677","3660","4517","4572","5077","6170","7045","7366","2200"];

        $insert_array = [];
        foreach ($reserved_squawks as $squawk) {
            $insert_array[] = ['squawk' => $squawk];
        }

        DB::table('squawk_reserved')->insert($insert_array);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('squawk_reserved');
    }
}
