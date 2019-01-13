<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkRangeOwner;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBigginSquawks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the range owner
        $rangeOwner = new SquawkRangeOwner();
        $rangeOwner->save();

        // Create the unit
        $unit = new SquawkUnit();
        $unit->unit = 'EGKB';
        $unit->squawk_range_owner_id = $rangeOwner->id;
        $unit->save();

        // Create the range
        $squawkRange = new Range();
        $squawkRange->start = '7047';
        $squawkRange->stop = '7047';
        $squawkRange->rules = 'A';
        $squawkRange->allow_duplicate = true;
        $squawkRange->squawkRangeOwner()->associate($rangeOwner);
        $squawkRange->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $unit = SquawkUnit::where('unit', '=', 'EGKB')->first();

        if ($unit !== null) {
            $unit->delete();
            SquawkRangeOwner::where('id', '=', $unit->squawk_range_owner_id)->delete();
        }
    }
}
