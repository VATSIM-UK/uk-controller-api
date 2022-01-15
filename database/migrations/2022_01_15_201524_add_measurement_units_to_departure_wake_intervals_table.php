<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMeasurementUnitsToDepartureWakeIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departure_wake_intervals', function (Blueprint $table) {
            $table->unsignedBigInteger('measurement_unit_id')
                ->after('interval')
                ->comment('The units of measurement for the interval');
        });

        DB::table('departure_wake_intervals')
            ->update(['measurement_unit_id' => DB::table('measurement_units')->where('unit', 's')->first()->id]);

        DB::table('departure_wake_intervals')
            ->where('lead_wake_category_id', DB::table('wake_categories')->where('code', 'H')->first()->id)
            ->where('following_wake_category_id', DB::table('wake_categories')->where('code', 'H')->first()->id)
            ->update([
                'measurement_unit_id' => DB::table('measurement_units')->where('unit', 'nm')->first()->id,
                'interval' => 4,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departure_wake_intervals', function (Blueprint $table) {
            $table->dropColumn('measurement_unit_id');
        });
    }
}
