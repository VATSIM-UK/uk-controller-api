<?php

use App\Models\Measurement\MeasurementUnit;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        MeasurementUnit::create(
            [
                'unit' => 'min',
                'description' => 'Minutes',
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
        //
    }
};
