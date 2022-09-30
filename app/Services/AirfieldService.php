<?php

namespace App\Services;

use App\Models\Aircraft\SpeedGroup;
use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AirfieldService
{
    public function getAirfieldsDependency(): array
    {
        return Airfield::with(
            'speedGroups',
            'speedGroups.aircraft',
            'speedGroups.engineTypes',
            'speedGroups.relatedGroups'
        )
            ->get()->map(
                function (Airfield $airfield) {
                    return [
                        'id' => $airfield->id,
                        'identifier' => $airfield->code,
                        'wake_scheme' => $airfield->wake_category_scheme_id,
                        'departure_speed_groups' => $this->getSpeedGroupsForAirfield($airfield),
                        'top_down_controller_positions' =>
                            $airfield->controllers()->orderBy('order')->pluck('controller_positions.id')
                                ->toArray(),
                        'pairing_prenotes' => $airfield->prenotePairings->map(function (Airfield $destination) {
                            return [
                                'airfield_id' => $destination->id,
                                'flight_rule_id' => $destination->pivot->flight_rule_id,
                                'prenote_id' => $destination->pivot->prenote_id,
                            ];
                        })->toArray(),
                        'handoff_id' => $airfield->handoff_id,
                    ];
                }
            )->toArray();
    }

    private function getSpeedGroupsForAirfield(Airfield $airfield): array
    {
        $speedGroups = [];

        foreach ($airfield->speedGroups as $speedGroup) {
            $speedGroups[] = [
                'id' => $speedGroup->id,
                'aircraft_types' => $speedGroup->aircraft->pluck('code')->toArray(),
                'engine_types' => $speedGroup->engineTypes->pluck('euroscope_type')->toArray(),
                'related_groups' => $speedGroup->relatedGroups->mapWithKeys(function (SpeedGroup $related) {
                    return [
                        $related->id => [
                            'following_interval_penalty' => $related->pivot->penalty,
                            'set_following_interval_to' => $related->pivot->set_interval_to,
                        ],
                    ];
                })->toArray(),
            ];
        }

        return $speedGroups;
    }

    /**
     * @return array
     */
    public function getAllAirfieldsWithRelations(): array
    {
        $airfields = [];
        Airfield::all()->each(function (Airfield $airfield) use (&$airfields) {
            $airfieldPairings = $airfield->prenotePairings()->select(['destination_airfield_id', 'prenote_id'])->get();
            $airfieldArray = [];

            $airfieldPairings->each(function (Airfield $airfield) use (&$airfieldArray) {
                $airfieldArray[$airfield->destination_airfield_id][] = $airfield->prenote_id;
            });

            $airfields[] = array_merge(
                $airfield->makeHidden(['latitude', 'longitude'])->toArray(),
                [
                    'controllers' =>
                        $airfield->controllers()->orderBy('order')->pluck('controller_position_id')->toArray(),
                    'pairing-prenotes' => $airfieldArray,
                ]
            );
        });

        return $airfields;
    }

    public static function createNewTopDownOrder(int $airfieldId, array $positions): void
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign(
            Airfield::findOrFail($airfieldId),
            $positions
        );
    }

    public static function updateAllTopDownsWithPosition(
        ControllerPosition|string $positionToAddAdjacent,
        ControllerPosition|string $positionToAdd,
        bool $before
    ): void {
        $positionToAddAdjacent = is_string($positionToAddAdjacent)
            ? ControllerPosition::fromCallsign($positionToAddAdjacent)
            : $positionToAddAdjacent;

        $positionToAdd = is_string($positionToAdd)
            ? ControllerPosition::fromCallsign($positionToAdd)
            : $positionToAdd;

        DB::transaction(function () use ($positionToAddAdjacent, $positionToAdd, $before) {
            foreach (self::getTopDownAirfieldsForPosition($positionToAddAdjacent->id) as $airfield) {
                if ($before) {
                    ControllerPositionHierarchyService::insertPositionIntoHierarchy(
                        $airfield,
                        $positionToAdd,
                        before: $positionToAddAdjacent
                    );
                    continue;
                }

                ControllerPositionHierarchyService::insertPositionIntoHierarchy(
                    $airfield,
                    $positionToAdd,
                    after: $positionToAddAdjacent
                );
            }
        });
    }

    public static function removePositionFromAllTopDowns(string $position): void
    {
        $positionToRemove = is_string($position)
            ? ControllerPosition::fromCallsign($position)
            : $position;

        foreach (self::getTopDownAirfieldsForPosition($positionToRemove->id) as $handoff) {
            ControllerPositionHierarchyService::removeFromHierarchy($handoff, $positionToRemove);
        }
    }

    private static function getTopDownAirfieldsForPosition(int $positionId): Collection
    {
        return Airfield::whereHas('controllers', function (Builder $controllers) use ($positionId) {
            $controllers->where('controller_positions.id', $positionId);
        })->get();
    }

    public static function controllerIsInTopDownOrder(ControllerPosition $controllerPosition, string $airfield): bool
    {
        return Airfield::where('code', $airfield)
            ->whereHas('controllers', function (Builder $controllers) use ($controllerPosition) {
                $controllers->where('controller_positions.id', $controllerPosition->id);
            })
            ->exists();
    }
}
