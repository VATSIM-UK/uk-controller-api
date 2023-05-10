<?php

namespace App\Observers;

use App\Filament\Helpers\SelectOptions;
use App\Models\Stand\Stand;

class StandObserver
{
    public function created(Stand $model): void
    {
        $this->clearCache($model);
    }

    public function updated(Stand $model): void
    {
        $this->clearCache($model);
    }

    public function deleted(Stand $model): void
    {
        $this->clearCache($model);
    }

    private function clearCache(Stand $stand): void
    {
        SelectOptions::clearStandsForAirfieldCache($stand->airfield);
    }
}
