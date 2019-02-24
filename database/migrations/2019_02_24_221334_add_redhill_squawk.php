<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkRangeOwner;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRedhillSquawk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $owner = new SquawkRangeOwner;
        $owner->save();

        SquawkUnit::create(
            [
                'unit' => 'EGKR',
                'squawk_range_owner_id' => $owner->id,
            ]
        );

        Range::create(
            [
                'squawk_range_owner_id' => $owner->id,
                'start' => '3767',
                'stop' => '3767',
                'rules' => 'A',
                'allow_duplicate' => true,
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
        $unit = SquawkUnit::where('unit', '=', 'EGKR')->first();
        $unit->delete();
        Range::where('squawk_range_owner_id', '=', $unit->squawk_range_owner_id);
        SquawkRangeOwner::find($unit->squawk_range_owner_id)->delete();
    }
}
