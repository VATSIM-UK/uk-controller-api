<?php

namespace App\Observers;

use App\Models\Hold\Hold;
use App\Services\DependencyService;
use Illuminate\Support\Facades\Log;

class HoldObserver
{
    /**
     * Handle the Hold "created" event.
     *
     * @param  \App\Models\Hold\Hold  $hold
     * @return void
     */
    public function created(Hold $hold)
    {
        Log::info("Touching hold dependency because of the the creation of hold {$hold->id} in navaid {$hold->navaid_id}");
        $this->touchDependency();
    }

    /**
     * Handle the Hold "updated" event.
     *
     * @param  \App\Models\Hold\Hold  $hold
     * @return void
     */
    public function updated(Hold $hold)
    {
        Log::info("Touching hold dependency because of the the update of hold {$hold->id} in navaid {$hold->navaid_id}");
        $this->touchDependency();
    }

    /**
     * Handle the Hold "deleted" event.
     *
     * @param  \App\Models\Hold\Hold  $hold
     * @return void
     */
    public function deleted(Hold $hold)
    {
        Log::info("Touching hold dependency because of the the deletion of hold {$hold->id} in navaid {$hold->navaid_id}");
        $this->touchDependency();
    }

    /**
     * Wrapper function to touch the stand dependency.
     *
     * @return void
     */
    private function touchDependency() : void
    {
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }
}
