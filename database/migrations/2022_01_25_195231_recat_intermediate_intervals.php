<?php

use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class RecatIntermediateIntervals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        WakeCategory::with('departureIntervals')->whereHas('scheme', function (Builder $scheme) {
            $scheme->where('key', 'RECAT_EU');
        })->get()
            ->each(function (WakeCategory $category) {
                $category->departureIntervals()->each(function (WakeCategory $relatedCategory) use ($category) {
                    $category->departureIntervals()->attach(
                        [
                            $relatedCategory->id => [
                                'interval' => $relatedCategory->pivot->interval,
                                'measurement_unit_id' => $relatedCategory->pivot->measurement_unit_id,
                                'intermediate' => true,
                            ]
                        ]
                    );
                });
            });
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
