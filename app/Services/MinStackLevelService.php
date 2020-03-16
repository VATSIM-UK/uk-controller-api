<?php

namespace App\Services;

use App\Helpers\MinStack\MinStackCalculableInterface;
use App\Models\Airfield\Airfield;
use App\Models\MinStack\MslAirfield;
use App\Models\MinStack\MslTma;
use App\Models\Tma;
use Carbon\Carbon;
use Illuminate\Foundation\Application;

class MinStackLevelService
{
    /**
     * @var Application
     */
    private $application;

    /**
     * MinStackLevelService constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
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

    /**
     * Update all min stack levels in the database from the VATSIM metar server.
     *
     * @return array
     */
    public function updateAirfieldMinStackLevelsFromVatsimMetarServer() : array
    {
        $airfields = Airfield::all();

        $minStackLevels = [];
        $airfields->each(function (Airfield $airfield) use (&$minStackLevels) {
            if ($airfield->msl_calculation === null) {
                return;
            }

            $minStackCalculation = $this->application->makeWith(
                MinStackCalculableInterface::class,
                $airfield->msl_calculation
            );

            $minStackLevels[$airfield->id] = $minStackCalculation->calculateMinStack();
        });

        foreach ($minStackLevels as $airfield => $minStack) {
            if (!isset($minStack)) {
                continue;
            }

            MslAirfield::updateOrCreate(
                [
                    'airfield_id' => $airfield,
                ],
                [
                    'msl' => $minStack,
                    'generated_at' => Carbon::now(),
                ]
            );
        }

        $returnValue = [];
        foreach ($minStackLevels as $airfield => $minStackLevel) {
            $returnValue[$airfields->find($airfield)->code] = $minStackLevel;
        }

        return $returnValue;
    }

    /**
     * Update all TMA min stack levels in the database from the VATSIM metar server.
     *
     * @return array
     */
    public function updateTmaMinStackLevelsFromVatsimMetarServer() : array
    {
        $tmas = Tma::all();

        $minStackLevels = [];
        $tmas->each(function (Tma $tma) use (&$minStackLevels) {
            $minStackCalculation = $this->application->makeWith(
                MinStackCalculableInterface::class,
                $tma->mslAirfield->msl_calculation
            );

            $minStackLevels[$tma->id] = $minStackCalculation->calculateMinStack();
        });

        foreach ($minStackLevels as $tma => $minStack) {
            if (!isset($minStack)) {
                continue;
            }

            MslTma::updateOrCreate(
                [
                    'tma_id' => $tma,
                ],
                [
                    'msl' => $minStack,
                    'generated_at' => Carbon::now(),
                ]
            );
        }

        $returnValue = [];
        foreach ($minStackLevels as $tma => $minStackLevel) {
            $returnValue[$tmas->find($tma)->name] = $minStackLevel;
        }

        return $returnValue;
    }
}
