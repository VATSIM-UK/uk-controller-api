<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->decimal('outbound_leg_value', 8, 1)
                ->unsigned()
                ->nullable()
                ->after('turn_direction')
                ->comment('The value of the outbound leg');

            $table->unsignedBigInteger('outbound_leg_unit')
                ->nullable()
                ->after('outbound_leg_value')
                ->comment('The unit in which the outbound leg is measured');

            $table->foreign('outbound_leg_unit')
                ->references('id')
                ->on('measurement_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->dropColumn('outbound_leg_value');
        });
    }
};
