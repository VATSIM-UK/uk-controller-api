<?php

namespace util\Traits;

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;

trait WithWakeCategories
{
    protected function setWakeCategoryForAircraft(string $icaoCode, string $code)
    {
        Aircraft::where('code', $icaoCode)->first()->wakeCategories()->sync(
            [WakeCategory::where('code', $code)->first()->id],
            true
        );
    }
}
