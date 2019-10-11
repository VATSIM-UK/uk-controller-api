<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Database\Migrations\Migration;

class UpdateCambridgeSquawkRange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cambridge = SquawkUnit::where('unit', 'EGSC')->firstOrFail();

        // Delete the old ranges
        $cambridge->ranges->each(function (Range $range) {
            $range->delete();
        });

        // Add new ranges

        Range::create(
            [
                'squawk_range_owner_id' => $cambridge->rangeOwner->id,
                'start' => '6160',
                'stop' => '6175',
                'rules' => 'A',
                'allow_duplicate' => false,
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
        $cambridge = SquawkUnit::where('unit', 'EGSC')->firstOrFail();

        // Delete the old ranges
        $cambridge->ranges->each(function (Range $range) {
            $range->delete();
        });

        // Add new ranges
        Range::insert(
            [
                [
                    'squawk_range_owner_id' => $cambridge->rangeOwner->id,
                    'start' => '6160',
                    'stop' => '6176',
                    'rules' => 'A',
                    'allow_duplicate' => false,
                ],
                [
                    'squawk_range_owner_id' => $cambridge->rangeOwner->id,
                    'start' => '6171',
                    'stop' => '6177',
                    'rules' => 'A',
                    'allow_duplicate' => false,
                ],
            ]
        );
    }
}
