<?php

namespace App\Observers;

use App\Models\Stand\Stand;
use App\Services\StandService;
use App\Services\DependencyService;
use Illuminate\Support\Facades\Log;

class StandObserver
{
    /**
     * Handle the Stand "created" event.
     *
     * @param  \App\Models\Stand\Stand  $stand
     * @return void
     */
    public function created(Stand $stand)
    {
        Log::info("Touching stand dependency because of the the creation of stand {$stand->id}");
        $this->touchDependency();
    }

    /**
     * Handle the Stand "updated" event.
     *
     * @param  \App\Models\Stand\Stand  $stand
     * @return void
     */
    public function updated(Stand $stand)
    {
        Log::info("Touching stand dependency because of the the update of stand {$stand->id}");
        $this->touchDependency();
    }

    /**
     * Handle the Stand "deleted" event.
     *
     * @param  \App\Models\Stand\Stand  $stand
     * @return void
     */
    public function deleted(Stand $stand)
    {
        Log::info("Touching stand dependency because of the the deletion of stand {$stand->id}");
        $this->touchDependency();
    }

    /**
     * Wrapper function to touch the stand dependency.
     *
     * @return void
     */
    private function touchDependency() : void
    {
        DependencyService::touchDependencyByKey(StandService::STAND_DEPENDENCY_KEY);
    }
}
