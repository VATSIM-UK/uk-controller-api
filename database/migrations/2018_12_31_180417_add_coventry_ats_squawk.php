<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Database\Migrations\Migration;

class AddCoventryAtsSquawk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ranges = SquawkUnit::where([['unit', '=', 'EGBE'], ['squawk_range_owner_id', '=', 30]])
            ->firstOrFail()
            ->ranges;

        foreach ($ranges as $range) {
            $range->delete();
        }

        Range::Create(
            [
                'squawk_range_owner_id' => SquawkUnit::where('unit', '=', 'EGBE')->firstOrFail()->squawk_range_owner_id,
                'start' => '0420',
                'stop' => '0420',
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
        // Cant roll this entire thing back so just delete the new range
        $ranges = SquawkUnit::where([['unit', '=', 'EGBE'], ['squawk_range_owner_id', '=', '30']])
            ->firstOrFail()
            ->ranges;

        foreach ($ranges as $range) {
            $range->delete();
        }
    }
}
