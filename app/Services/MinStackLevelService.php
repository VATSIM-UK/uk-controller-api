<?php

namespace App\Services;

use App\Helpers\MinStack\MinStackCalculator;
use App\Models\Airfield;
use App\Models\MinStack\MslAirfield;
use App\Models\Tma;

class MinStackLevelService
{
    /**
     * @var array
     */
    private $calculatedMinStacks;
    /**
     * @var MinStackCalculator
     */
    private $minStackCalculator;

    /**
     * MinStackLevelService constructor.
     * @param MinStackCalculator $minStackCalculator
     */
    public function __construct(MinStackCalculator $minStackCalculator)
    {
        $this->minStackCalculator = $minStackCalculator;
    }

    /**
     * @param string $icao
     * @return int|null
     */
    public function getMinStackLevelForAirfield(string $icao) : ?int
    {
        $airfield = Airfield::where('code', $icao)->first();

        if ($airfield === null || $airfield->msl === null) {
            return null;
        }

        return $airfield->msl->msl;
    }

    /**
     * @param string $name
     * @return int|null
     */
    public function getMinStackLevelForTma(string $name) : ?int
    {
        $tma = Tma::where('name', $name)->first();
        if ($tma === null || $tma->msl === null) {
            return null;
        }

        return $tma->msl->msl;
    }

    /**
     * @return array
     */
    public function getAllAirfieldMinStackLevels() : array
    {
        $airfields = Airfield::all();
        $minStackLevels = [];

        $airfields->each(function (Airfield $airfield) use (&$minStackLevels) {
            if ($airfield->msl === null) {
                return;
            }

            $minStackLevels[$airfield->code] = $airfield->msl->msl;
        });

        return $minStackLevels;
    }

    /**
     * @return array
     */
    public function getAllTmaMinStackLevels() : array
    {
        $airfields = Tma::all();
        $minStackLevels = [];

        $airfields->each(function (Tma $tma) use (&$minStackLevels) {
            if ($tma->msl === null) {
                return;
            }

            $minStackLevels[$tma->name] = $tma->msl->msl;
        });

        return $minStackLevels;
    }

    public function updateAirfieldMinStackLevelsFromVatsimMetarServer() : void
    {
        $airfields = Airfield::all();

        $minStackLevels = [];
        $airfields->each(function (Airfield $airfield) use (&$minStackLevels) {
            if ($airfield->mslCalculation === null) {
                return;
            }

            $minStack = $this->getMinStackLevelForAirfield($airfield);

            if ($minStack === null) {
                return;
            }

            $minStackLevels[$airfield->id] = $minStack;
        });

        foreach ($minStackLevels as $airfield => $minStack) {
            MslAirfield::createOrUpdate(
                [
                    'airfield_id' => $airfield,
                    'msl' => $minStack
                ]
            );
        }
    }

    public function getMinStackForAirfield(Airfield $airfield) : ?int
    {
        if ($airfield->mslCalculation === null) {
            return null;
        }

        if ($airfield->mslCalculation['type'] === self::CALCULATION_TYPE_AIRFIELD) {
            return $this->minStackCalculator->calculateDirectMinStack(
                $airfield->code,
                $airfield->transition_altitude,
                $airfield->standard_high
            );
        }

        if ($airfield->mslCalculation['type'] === self::CALCULATION_TYPE_LOWEST) {
            return $this->calculateLowestOfMinStack($airfield);
        }

        return null;
    }
}
