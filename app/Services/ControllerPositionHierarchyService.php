<?php


namespace App\Services;

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\HasControllerHierarchy;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class ControllerPositionHierarchyService
{
    private const CONTROLLERS_PRIMARY_COLUMN = 'controller_positions.id';

    public static function setPositionsForHierarchyByControllerId(
        HasControllerHierarchy $ownerRecord,
        array $positions
    ): void {
        DB::transaction(function () use ($ownerRecord, $positions) {
            $order = 1;

            $ownerRecord->controllers()->sync([]);
            $ownerRecord->controllers()->sync(
                collect($positions)
                    ->mapWithKeys(
                        function (int $controllerId) use (&$order) {
                            return [$controllerId => ['order' => $order++]];
                        }
                    )
                    ->toArray()
            );
        });
    }

    public static function setPositionsForHierarchyByControllerCallsign(
        HasControllerHierarchy $ownerRecord,
        array $positions
    ): void {
        $controllerPositions = ControllerPosition::whereIn('callsign', $positions)
            ->get()
            ->mapWithKeys(fn (ControllerPosition $position) => [$position->callsign => $position->id]);

        self::setPositionsForHierarchyByControllerId(
            $ownerRecord,
            array_map(
                fn (string $callsign) => $controllerPositions[$callsign],
                $positions
            )
        );
    }

    public static function setPositionsForHierarchyByController(
        HasControllerHierarchy $ownerRecord,
        array $positions
    ): void {
        self::setPositionsForHierarchyByControllerId(
            $ownerRecord,
            array_map(
                fn (ControllerPosition $position) => $position->id,
                $positions
            )
        );
    }

    public static function insertPositionIntoHierarchy(
        HasControllerHierarchy $ownerRecord,
        ControllerPosition $position,
        ?ControllerPosition $before = null,
        ?ControllerPosition $after = null
    ): void {
        DB::transaction(function () use ($ownerRecord, $position, $before, $after) {
            if (!$before && !$after) {
                $ownerRecord->controllers()
                    ->attach($position, ['order' => $ownerRecord->controllers()->count() + 1]);

                return;
            }

            if ($before && $after) {
                throw new LogicException('Cannot insert controller position both before and after another');
            }

            $controllerToWorkFrom = $ownerRecord->controllers()
                ->find($before ?? $after);

            if (!$controllerToWorkFrom) {
                throw new InvalidArgumentException('Relative controller is not part of hierarchy');
            }

            $controllersToSync = $ownerRecord->controllers()
                ->get()
                ->concat([$position])
                ->keyBy('id')
                ->map(
                    fn (ControllerPosition $position) => [
                        'order' => static::getNewOrder($position, $controllerToWorkFrom, (bool)$before),
                    ]
                )
                ->toArray();
            $ownerRecord->controllers()->sync([]);
            $ownerRecord->controllers()->sync($controllersToSync);
        });
    }

    private static function getNewOrder(
        ControllerPosition $position,
        ControllerPosition $positionToInsertAround,
        bool $before
    ): int {
        if (!$position->pivot) {
            return $positionToInsertAround->pivot->order + ($before ? 0 : 1);
        }

        $includeCurrentOffset = $before ? 0 : 1;
        return $position->pivot->order >= $positionToInsertAround->pivot->order + $includeCurrentOffset
            ? $position->pivot->order + 1
            : $position->pivot->order;
    }

    public static function removeFromHierarchy(
        HasControllerHierarchy $ownerRecord,
        string|int|ControllerPosition $position
    ): void {
        DB::transaction(function () use ($ownerRecord, $position) {
            $controller = match (true) {
                is_int($position) => ControllerPosition::fromId($position),
                is_string($position) => ControllerPosition::fromCallsign($position),
                default => $position
            };

            $order = 1;
            $controllersToSync = $ownerRecord->controllers()
                ->where(self::CONTROLLERS_PRIMARY_COLUMN, '<>', $controller->id)
                ->get()
                ->mapWithKeys(function (ControllerPosition $position) use (&$order) {
                    return [$position->id => ['order' => $order++]];
                })
                ->toArray();

            $ownerRecord->controllers()->sync([]);
            $ownerRecord->controllers()->sync($controllersToSync);
        });
    }

    public static function moveControllerInHierarchy(
        HasControllerHierarchy $ownerRecord,
        ControllerPosition|int $position,
        bool $up
    ): void {
        $position = is_int($position)
            ? ControllerPosition::fromId($position)
            : $position;

        $positions = $ownerRecord->controllers()
            ->pluck(self::CONTROLLERS_PRIMARY_COLUMN);
        $positionToSwap = $positions->search(fn (int $handoffPosition) => $handoffPosition === $position->id);

        if ($positionToSwap === false) {
            throw new InvalidArgumentException('Position not in handoff order');
        }

        $arrayPositions = $positions->toArray();

        // No need to do any swapping
        if (
            count($arrayPositions) === 1 ||
            $up && $positionToSwap === 0 ||
            !$up && $positionToSwap === count($arrayPositions) - 1
        ) {
            return;
        }

        $newItemLocation = $up ? $positionToSwap - 1 : $positionToSwap + 1;

        [$arrayPositions[$positionToSwap], $arrayPositions[$newItemLocation]] =
            [$arrayPositions[$newItemLocation], $arrayPositions[$positionToSwap]];

        static::setPositionsForHierarchyByControllerId(
            $ownerRecord,
            $arrayPositions
        );
    }
}
