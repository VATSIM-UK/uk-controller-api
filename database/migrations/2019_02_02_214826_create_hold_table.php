<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHoldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hold', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('fix')->comment('The holding fix');
            $table->smallInteger('inbound_heading')->comment('The heading');
            $table->mediumInteger('minimum_altitude')->comment('The minimum altitude allowed in the hold');
            $table->mediumInteger('maximum_altitude')->comment('The maximum altitude allowed in the hold');
            $table->enum('turn_direction', ['left', 'right'])->comment('The turn direction at the holding fix');
            $table->string('description')
                ->comment(
                    'How the hold should be described, usually the holding fix, ' .
                    'but may be different in the event of multi-purpose holds'
                );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hold');
    }
}
