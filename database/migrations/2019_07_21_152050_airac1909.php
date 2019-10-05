<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Airac1909 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $bristolUnit = SquawkUnit::where('unit', 'EGGD')->firstOrFail();
        $ranges = $bristolUnit->ranges;

        // Find the affected range and update
        $range = $ranges->firstWhere('start', '5071');
        $range->start = '5072';
        $range->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $bristolUnit = SquawkUnit::where('unit', 'EGGD')->firstOrFail();
        $ranges = $bristolUnit->ranges;

        // Find the affected range and update
        $range = $ranges->firstWhere('start', '5072');
        $range->start = '5071';
        $range->save();
    }
}
