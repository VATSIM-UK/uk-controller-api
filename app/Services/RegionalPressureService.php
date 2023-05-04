<?php

namespace App\Services;

use App\Events\RegionalPressuresUpdatedEvent;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use App\Models\AltimeterSettingRegions\RegionalPressureSetting;
use Illuminate\Support\Collection;

/**
 * Service for generating the regional pressure settings based on the airfields
 * that comprise each Altimeter Setting Region.
 */
class RegionalPressureService
{
    public function updateRegionalPressuresFromMetars(Collection $metars): void
    {
        // Get all the current values and format
        $currentRegionalPressures = RegionalPressureSetting::with('altimeterSettingRegion')->get()->mapWithKeys(
            function (RegionalPressureSetting $regionalPressureSetting) {
                return [
                    $regionalPressureSetting->altimeterSettingRegion->key => [
                        'altimeter_setting_region_id' => $regionalPressureSetting->altimeterSettingRegion->id,
                        'value' => $regionalPressureSetting->value,
                    ],
                ];
            }
        );

        // Get the latest values
        $newRegionalPressures = new Collection();
        foreach (AltimeterSettingRegion::with('airfields')->get() as $altimeterSettingRegion) {
            $relevantMetars = $metars->whereIn('airfield_id', $altimeterSettingRegion->airfields->pluck('id'))
                ->whereNotNull('qnh');

            if ($relevantMetars->isEmpty()) {
                continue;
            }

            $newRegionalPressures->put(
                $altimeterSettingRegion->key,
                [
                    'altimeter_setting_region_id' => $altimeterSettingRegion->id,
                    'value' => $this->calculateRegionalPressure($altimeterSettingRegion, $relevantMetars),
                ]
            );
        }

        // Work out which regional pressures have changed and upsert. Broadcast the changes if there are any.
        $updatedRegionalPressures = $this->getUpdatedRegionalPressures(
            $newRegionalPressures,
            $currentRegionalPressures
        );
        if ($updatedRegionalPressures->isNotEmpty()) {
            RegionalPressureSetting::upsert(
                $updatedRegionalPressures->values()->toArray(),
                ['altimeter_setting_region_id'],
                ['value']
            );

            event(
                new RegionalPressuresUpdatedEvent(
                    $updatedRegionalPressures->mapWithKeys(
                        function (array $regionalPressureData, string $region) {
                            return [$region => $regionalPressureData['value']];
                        }
                    )->toArray()
                )
            );
        }
    }

    private function getUpdatedRegionalPressures(
        Collection $newRegionalPressures,
        Collection $currentRegionalPressures
    ): Collection {
        $updated = new Collection();
        foreach ($newRegionalPressures as $key => $newRegionalPressure) {
            if (!isset($currentRegionalPressures[$key]) || $currentRegionalPressures[$key] !== $newRegionalPressure) {
                $updated->put($key, $newRegionalPressure);
            }
        }

        return $updated;
    }

    /**
     * Regional pressure is based on the lowest QNH in the region, usually subtract 1. Though at London, Manchester
     * etc it's just the QNH at one airfield.
     */
    private function calculateRegionalPressure(
        AltimeterSettingRegion $altimeterSettingRegion,
        Collection $relevantMetars
    ): int {
        $rps = $relevantMetars->pluck('qnh')->min() + $altimeterSettingRegion->adjustment;
        return $rps < 0 ? 0 : $rps;
    }

    public function getRegionalPressureArray(): array
    {
        return RegionalPressureSetting::with('altimeterSettingRegion')->get()->mapWithKeys(
            function (RegionalPressureSetting $rps) {
                return [
                    $rps->altimeterSettingRegion->key => $rps->value,
                ];
            }
        )->toArray();
    }
}
