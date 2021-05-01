<?php

namespace App\Services;

use App\Events\MinStacksUpdatedEvent;
use App\Helpers\MinStack\MinStackCalculableInterface;
use App\Helpers\MinStack\MinStackCalculator;
use App\Models\Airfield\Airfield;
use App\Models\MinStack\MslAirfield;
use App\Models\MinStack\MslTma;
use App\Models\Tma;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

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
    public function getMinStackLevelForAirfield(string $icao): ?int
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
    public function getMinStackLevelForTma(string $name): ?int
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
    public function getAllAirfieldMinStackLevels(): array
    {
        $airfields = Airfield::all();
        $minStackLevels = [];

        $airfields->each(
            function (Airfield $airfield) use (&$minStackLevels) {
                if ($airfield->msl === null) {
                    return;
                }

                $minStackLevels[$airfield->code] = $airfield->msl->msl;
            }
        );

        return $minStackLevels;
    }

    /**
     * @return array
     */
    public function getAllTmaMinStackLevels(): array
    {
        $airfields = Tma::all();
        $minStackLevels = [];

        $airfields->each(
            function (Tma $tma) use (&$minStackLevels) {
                if ($tma->msl === null) {
                    return;
                }

                $minStackLevels[$tma->name] = $tma->msl->msl;
            }
        );

        return $minStackLevels;
    }

    public function updateMinimumStackLevelsFromMetars(Collection $metars): void
    {
        // Update MSLs for airfields. If they've not changed, then nothing has changed.
        $changedAirfieldMinimumStackLevels = $this->updateAirfieldMinimumStackLevels($metars);
        if ($changedAirfieldMinimumStackLevels->isEmpty()) {
            return;
        }

        // Check for updated TMA MSLs
        $changedTmaMinimumStackLevels = $this->updateTmaMinimumStackLevels($changedAirfieldMinimumStackLevels);
        event(
            new MinStacksUpdatedEvent(
                $changedAirfieldMinimumStackLevels->toArray(), $changedTmaMinimumStackLevels->toArray()
            )
        );
    }

    private function updateAirfieldMinimumStackLevels(Collection $metars): Collection
    {
        // Get all the current MSLs
        $currentMinimumStackLevels = MslAirfield::with('airfield')->get()->mapWithKeys(
            function (MslAirfield $msl) {
                return [
                    $msl->airfield->code => [
                        'airfield_id' => $msl->airfield_id,
                        'msl' => $msl->msl,
                    ],
                ];
            }
        );

        // Get new minimum stack levels
        $newMinimumStackLevels = new Collection();
        foreach (Airfield::with('mslCalculationAirfields')->get() as $airfield) {
            $relevantMetars = $metars->whereIn('airfield_id', $airfield->mslCalculationAirfields->pluck('id'))
                ->whereNotNull('qnh');

            if ($relevantMetars->isEmpty()) {
                continue;
            }

            $newMinimumStackLevels->put(
                $airfield->code,
                [
                    'airfield_id' => $airfield->id,
                    'msl' => $this->calculateAirfieldMsl($airfield, $relevantMetars),
                ]
            );
        }

        // Work out which MSLs have changed and upsert
        $updatedMinimumStackLevels = $this->getUpdatedMsls($newMinimumStackLevels, $currentMinimumStackLevels);
        if ($updatedMinimumStackLevels->isNotEmpty()) {
            MslAirfield::upsert(
                $updatedMinimumStackLevels->values()->toArray(),
                ['airfield_id'],
                ['msl']
            );
        }

        // Return them in correct format for event
        return $updatedMinimumStackLevels->mapWithKeys(
            function (array $mslData, string $airfieldCode) {
                return [$airfieldCode => $mslData['msl']];
            }
        );
    }

    private function getUpdatedMsls(
        Collection $newMinimumStackLevels,
        Collection $currentMinimumStackLevels
    ): Collection {
        $updated = new Collection();
        foreach ($newMinimumStackLevels as $key => $newMsl) {
            if (!isset($currentMinimumStackLevels[$key]) || $currentMinimumStackLevels[$key] !== $newMsl) {
                $updated->put($key, $newMsl);
            }
        }

        return $updated;
    }

    private function calculateAirfieldMsl(Airfield $airfield, Collection $relevantMetars): int
    {
        return MinStackCalculator::calculateMinStack($airfield, $relevantMetars->pluck('qnh')->min());
    }

    private function updateTmaMinimumStackLevels(Collection $updatedAirfieldMsls): Collection
    {
        // Get all the current MSLs
        $currentMinimumStackLevels = MslTma::with('tma')->get()->mapWithKeys(
            function (MslTma $msl) {
                return [
                    $msl->tma->name => [
                        'tma_id' => $msl->tma->id,
                        'msl' => $msl->msl,
                    ],
                ];
            }
        );

        // Get all the new MSLs
        $newMinimumStackLevels = Tma::with('msl', 'mslAirfield')->get()->filter(function (Tma $tma) use ($updatedAirfieldMsls) {
            return $updatedAirfieldMsls->has($tma->mslAirfield->code);
        })->mapWithKeys(
            function (Tma $tma) use ($updatedAirfieldMsls) {
                return [
                    $tma->name => [
                        'tma_id' => $tma->id,
                        'msl' => $updatedAirfieldMsls[$tma->mslAirfield->code],
                    ],
                ];
            }
        );

        // Check which ones have changed and upsert if required
        $updatedMinimumStackLevels = $this->getUpdatedMsls($newMinimumStackLevels, $currentMinimumStackLevels);
        if ($updatedMinimumStackLevels->isNotEmpty()) {
            MslTma::upsert(
                $updatedMinimumStackLevels->values()->toArray(),
                ['tma_id'],
                ['msl']
            );
        }

        // Map to output format
        return $updatedMinimumStackLevels->mapWithKeys(
            function (array $msldata, string $tmaName) {
                return [$tmaName => $msldata['msl']];
            }
        );
    }
}
