<?php

use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Migrations\Migration;

class AddArrivalWakeIntervals extends Migration
{
    const INTERVALS = [
        'A' => [
            'A' => 4,
            'B' => 4,
            'C' => 5,
            'D' => 5,
            'E' => 6,
            'F' => 8,
        ],
        'B' => [
            'A' => 3.5,
            'B' => 3.5,
            'C' => 4,
            'D' => 4,
            'E' => 5,
            'F' => 7,
        ],
        'C' => [
            'A' => 3,
            'B' => 3,
            'C' => 3,
            'D' => 3,
            'E' => 4,
            'F' => 6,
        ],
        'D' => [
            'F' => 5,
        ],
        'E' => [
            'F' => 5,
        ],
        'F' => [
            'F' => 5,
        ],
        'J' => [
            'H' => 6,
            'UM' => 7,
            'LM' => 7,
            'S' => 7,
            'L' => 8,
        ],
        'H' => [
            'J' => 4,
            'H' => 4,
            'UM' => 5,
            'LM' => 5,
            'S' => 6,
            'L' => 7,
        ],
        'UM' => [
            'UM' => 3,
            'LM' => 4,
            'S' => 4,
            'L' => 6,
        ],
        'LM' => [
            'S' => 3,
            'L' => 5,
        ],
        'S' => [
            'S' => 3,
            'L' => 4,
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $categories = WakeCategory::all();

        foreach (self::INTERVALS as $leadCategory => $intervals) {
            $categories->firstOrFail('code', $leadCategory)
                ->arrivalIntervals()
                ->sync(
                    collect($intervals)->mapWithKeys(
                        fn(float $interval, string $followingCategory) => [
                            $categories->firstOrFail(
                                'code',
                                $followingCategory
                            )->id => ['interval' => $interval]
                        ]
                    )
                );
        }
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
