<?php

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AddAirfieldDefaultHandoffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Airfield::with('controllers')->get()->each(function (Airfield $airfield) {
                $applicableControllers = $this->getApplicableControllers($airfield->controllers);
                if ($applicableControllers->isEmpty()) {
                    return true;
                }

                $handoffKey = sprintf('AIRFIELD_%s_DEFAULT_HANDOFF', $airfield->code);
                HandoffService::createNewHandoffOrder(
                    $handoffKey,
                    sprintf('Default departure handoff for %s', $airfield->code),
                    $applicableControllers->map(function (ControllerPosition $controllerPosition) {
                        return $controllerPosition->callsign;
                    })->toArray()
                );

                $airfield->handoff_id = Handoff::where('key', $handoffKey)->firstOrFail()->id;
                $airfield->save();
                return true;
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

    private function getApplicableControllers(Collection $controllers): Collection
    {
        return $controllers->reject(function (ControllerPosition $controllerPosition) {
            return !$controllerPosition->isApproach() && !$controllerPosition->isEnroute();
        })->sortBy(function (ControllerPosition $position) {
            return $position->pivot->order;
        })->values();
    }
}
