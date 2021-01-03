<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureRestrictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_restrictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->comment('The type of interval');
            $table->integer('interval')->comment('The time period for this interval');
            $table->timestamp('expires_at')->comment('The time at which the departure interval expires');
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('departure_restriction_types')->cascadeOnDelete();
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_restrictions');
    }
}
