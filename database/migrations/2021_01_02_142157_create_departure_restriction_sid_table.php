<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureRestrictionSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_restriction_sid', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departure_restriction_id')->comment('The restriction this applies to');
            $table->unsignedInteger('sid_id')->comment('The sids that this interval applies to');
            $table->timestamps();

            $table->foreign('departure_restriction_id')
                ->references('id')
                ->on('departure_restrictions')->cascadeOnDelete();

            $table->foreign('sid_id')->references('id')->on('sid')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_restriction_sid');
    }
}
