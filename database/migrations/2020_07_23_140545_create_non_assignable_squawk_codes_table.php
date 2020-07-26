<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonAssignableSquawkCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_assignable_squawk_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 4)->comment('The code that is reserved');
            $table->string('description')->comment('Why is the code reserved');

            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('non_assignable_squawk_codes');
    }
}
