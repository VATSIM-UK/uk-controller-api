<?php

namespace App\Console\Commands;

use App\Models\Airfield\Airfield;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\LocationService;
use App\Services\StandService;
use Illuminate\Console\Command;
use Location\Distance\Haversine;

class AllocateStandForArrival extends Command
{
    protected $signature = 'stands:assign-arrival';

    protected $description = 'Assigns arrival stands to all aircraft that require it';

    /**
     * How many minutes before arrival the stand should be assigned
     */
    private const ASSIGN_STAND_MINUTES_BEFORE = 15.0;

    /**
     * @var StandService
     */
    private $standService;

    public function __construct(StandService $standService)
    {
        parent::__construct();
        $this->standService = $standService;
    }

    public function handle()
    {
        $this->info('Allocating arrival stands');
        NetworkAircraft::all()->each(function (NetworkAircraft $aircraft) {
            if (!$this->shouldAttemptAllocation($aircraft)) {
                return;
            }

            $this->standService->allocateStandForAircraft($aircraft);
        });
        $this->info('Finished allocation of arrival stands');
    }

    private function shouldAttemptAllocation(NetworkAircraft $aircraft): bool
    {
        return ($arrivalAirfield = Airfield::where('code',  $aircraft->planned_destairport)->first()) !== null &&
            $aircraft->groundspeed &&
            $this->getTimeFromAirfieldInMinutes($aircraft, $arrivalAirfield) < self::ASSIGN_STAND_MINUTES_BEFORE;
    }

    /**
     * Ground speed is kts (nautical miles per hour), so for minutes multiply that by 60.
     *
     * @param NetworkAircraft $aircraft
     * @param Airfield $airfield
     * @return float
     */
    private function getTimeFromAirfieldInMinutes(NetworkAircraft $aircraft, Airfield $airfield): float
    {
        $distanceToAirfieldInNm = LocationService::metersToNauticalMiles(
            $aircraft->latLong->getDistance($airfield->coordinate, new Haversine())
        );
        $groundspeed = $aircraft->groundspeed === 0 ? 1 : $aircraft->groundspeed;

        return (float) ($distanceToAirfieldInNm / $groundspeed) * 60.0;
    }
}
