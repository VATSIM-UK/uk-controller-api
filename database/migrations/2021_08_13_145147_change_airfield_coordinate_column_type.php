<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeAirfieldCoordinateColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE airfield MODIFY COLUMN latitude DECIMAL(10, 8)');
        DB::statement('ALTER TABLE airfield MODIFY COLUMN longitude DECIMAL(11, 8)');
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
}
