<?php

namespace App\Services;

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HandoffService
{
    private const CONTROLLERS_PRIMARY_COLUMN = 'controller_positions.id';

    public function getHandoffsV2Dependency(): array
    {
        return Handoff::with([
            'controllers' => function (BelongsToMany $query) {
                $query->orderBy('order');
            },
        ])->get()->map(function (Handoff $handoff) {
            return [
                'id' => $handoff->id,
                'controller_positions' => $handoff->controllers->pluck('id')->toArray(),
            ];
        })->toArray();
    }

    public static function createNewHandoffOrder(string $description, array $positions): Handoff
    {
        $handoff = null;
        DB::transaction(function () use ($description, $positions, &$handoff) {
            $handoff = Handoff::create(
                [
                    'description' => $description,
                ]
            );

            ControllerPositionHierarchyService::setPositionsForHierarchyByControllerCallsign($handoff, $positions);
        });

        return $handoff;
    }

    public static function updateAllHandoffsWithPosition(
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
            foreach (self::getHandoffsForPosition($positionToAddAdjacent->id) as $handoff) {
                if ($before) {
                    ControllerPositionHierarchyService::insertPositionIntoHierarchy(
                        $handoff,
                        $positionToAdd,
                        before: $positionToAddAdjacent
                    );
                    continue;
                }


                ControllerPositionHierarchyService::insertPositionIntoHierarchy(
                    $handoff,
                    $positionToAdd,
                    after: $positionToAddAdjacent
                );
            }
        });
    }

    public static function removePositionFromAllHandoffs(ControllerPosition|string $position): void
    {
        $positionToRemove = is_string($position)
            ? ControllerPosition::fromCallsign($position)
            : $position;

        foreach (self::getHandoffsForPosition($positionToRemove->id) as $handoff) {
            ControllerPositionHierarchyService::removeFromHierarchy($handoff, $positionToRemove);
        }
    }

    private static function getHandoffsForPosition(int $positionId): Collection
    {
        return Handoff::whereHas('controllers', function (Builder $builder) use ($positionId) {
            $builder->where(self::CONTROLLERS_PRIMARY_COLUMN, $positionId);
        })->get();
    }
}
