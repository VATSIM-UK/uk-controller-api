<?php

use App\Models\Measurement\MeasurementUnit;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MeasurementUnit::where('unit', 's')
            ->update(['description' => 'Seconds']);

        MeasurementUnit::where('unit', 'nm')
            ->update(['description' => 'Nautical Miles']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
