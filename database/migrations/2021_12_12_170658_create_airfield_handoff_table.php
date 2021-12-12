<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirfieldHandoffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airfield_handoff', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airfield_id')->comment('The airfield in the handoff order');
            $table->unsignedBigInteger('handoff_id')->comment('The handoff order');
            $table->unsignedBigInteger('flight_rule_id')->comment('The flight rules this applies to');
            $table->timestamps();

            $table->foreign('airfield_id')->references('id')->on('airfield')->cascadeOnDelete();
            $table->foreign('handoff_id')->references('id')->on('handoffs')->cascadeOnDelete();
            $table->foreign('flight_rule_id')->references('id')->on('flight_rules')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airfield_handoff');
    }
}
