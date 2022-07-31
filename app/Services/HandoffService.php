<?php

namespace App\Services;

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

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

            self::setPositionsForHandoffByControllerCallsign($handoff, $positions);
        });

        return $handoff;
    }

    public static function setPositionsForHandoffByControllerId(Handoff $handoff, array $positions): void
    {
        DB::transaction(function () use ($handoff, $positions) {
            $order = 1;

            $handoff->controllers()->sync([]);
            $handoff->controllers()->sync(
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

    public static function setPositionsForHandoffByControllerCallsign(Handoff $handoff, array $positions): void
    {
        $controllerPositions = ControllerPosition::whereIn('callsign', $positions)
            ->get()
            ->mapWithKeys(fn (ControllerPosition $position) => [$position->callsign => $position->id]);
        self::setPositionsForHandoffByControllerId(
            $handoff,
            array_map(
                fn (string $callsign) => $controllerPositions[$callsign],
                $positions
            )
        );
    }

    public static function setPositionsForHandoffByController(Handoff $handoff, array $positions): void
    {
        self::setPositionsForHandoffByControllerId(
            $handoff,
            array_map(
                fn (ControllerPosition $position) => $position->id,
                $positions
            )
        );
    }

    public static function insertPositionIntoHandoffOrder(
        Handoff $handoff,
        ControllerPosition $position,
        ?ControllerPosition $before = null,
        ?ControllerPosition $after = null
    ): void {
        DB::transaction(function () use ($handoff, $position, $before, $after) {
            if (!$before && !$after) {
                $handoff->controllers()
                    ->attach($position, ['order' => $handoff->controllers()->count() + 1]);

                return;
            }

            if ($before && $after) {
                throw new LogicException('Cannot insert controller position both before and after another');
            }

            $controllerToWorkFrom = $handoff->controllers()
                ->find($before ?? $after);

            if (!$controllerToWorkFrom) {
                throw new InvalidArgumentException('Controller is not part of handoff order');
            }

            $controllersToSync = $handoff->controllers
                ->concat(collect([$position]))
                ->keyBy('id')
                ->map(
                    fn (ControllerPosition $position) => [
                        'order' => static::getNewOrder($position, $controllerToWorkFrom, (bool)$before),
                    ]
                )
                ->toArray();
            $handoff->controllers()->sync([]);
            $handoff->controllers()->sync($controllersToSync);
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

    public static function removeFromHandoffOrder(Handoff $handoff, string|int|ControllerPosition $position): void
    {
        DB::transaction(function () use ($handoff, $position) {
            $controller = match (true) {
                is_int($position) => ControllerPosition::fromId($position),
                is_string($position) => ControllerPosition::fromCallsign($position),
                default => $position
            };

            $order = 1;
            $controllersToSync = $handoff->controllers()
                ->where(self::CONTROLLERS_PRIMARY_COLUMN, '<>', $controller->id)
                ->get()
                ->mapWithKeys(function (ControllerPosition $position) use (&$order) {
                    return [$position->id => ['order' => $order++]];
                })
                ->toArray();

            $handoff->controllers()->sync([]);
            $handoff->controllers()->sync($controllersToSync);
        });
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
                    self::insertPositionIntoHandoffOrder(
                        $handoff,
                        $positionToAdd,
                        before: $positionToAddAdjacent
                    );
                    continue;
                }

                self::insertPositionIntoHandoffOrder(
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
            self::removeFromHandoffOrder($handoff, $positionToRemove);
        }
    }

    private static function getHandoffsForPosition(int $positionId): Collection
    {
        return Handoff::whereHas('controllers', function (Builder $builder) use ($positionId) {
            $builder->where(self::CONTROLLERS_PRIMARY_COLUMN, $positionId);
        })->get();
    }

    public static function moveControllerInHandoffOrder(
        Handoff|int $handoff,
        ControllerPosition|int $position,
        bool $up
    ): void {
        $position = is_int($position)
            ? ControllerPosition::fromId($position)
            : $position;

        $handoff = is_int($handoff)
            ? Handoff::findOrFail($handoff)
            : $handoff;

        $positions = $handoff->controllers()
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

        static::setPositionsForHandoffByControllerId(
            $handoff,
            $arrayPositions
        );
    }
}
