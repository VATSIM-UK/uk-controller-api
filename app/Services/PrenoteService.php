<?php

namespace App\Services;

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use LogicException;

class PrenoteService
{
    private const CONTROLLERS_PRIMARY_COLUMN = 'controller_positions.id';

    public function getPrenotesV2Dependency(): array
    {
        return Prenote::all()->map(function (Prenote $prenote) {
            return [
                'id' => $prenote->id,
                'description' => $prenote->description,
                'controller_positions' => $prenote->controllers()
                    ->orderBy('order')
                    ->pluck('controller_positions.id')
                    ->toArray(),
            ];
        })->toArray();
    }

    public static function setPositionsForPrenote(Prenote|int $prenote, array $positions): void
    {
        DB::transaction(function () use ($prenote, $positions) {
            $order = 1;

            $prenote->controllers()->sync([]);
            $prenote->controllers()->sync(
                collect($positions)
                    ->map(fn (ControllerPosition|int|string $position) => match (true) {
                        is_string($position) => ControllerPosition::fromCallsign($position)->id,
                        $position instanceof ControllerPosition => $position->id,
                        default => $position
                    })
                    ->mapWithKeys(
                        function (int $controllerId) use (&$order) {
                            return [$controllerId => ['order' => $order++]];
                        }
                    )
                    ->toArray()
            );
        });
    }

    public static function insertPositionIntoPrenoteOrder(
        Prenote $prenote,
        ControllerPosition $position,
        ?ControllerPosition $before = null,
        ?ControllerPosition $after = null
    ): void {
        DB::transaction(function () use ($prenote, $position, $before, $after) {
            if (!$before && !$after) {
                $prenote->controllers()
                    ->attach($position, ['order' => $prenote->controllers()->count() + 1]);

                return;
            }

            if ($before && $after) {
                throw new LogicException('Cannot insert controller position both before and after another');
            }

            $controllerToWorkFrom = $prenote->controllers()
                ->find($before ?? $after);

            if (!$controllerToWorkFrom) {
                throw new InvalidArgumentException('Controller is not part of prenote order');
            }

            $controllersToSync = $prenote->controllers
                ->concat(collect([$position]))
                ->keyBy('id')
                ->map(
                    fn (ControllerPosition $position) => [
                        'order' => static::getNewOrder($position, $controllerToWorkFrom, (bool)$before),
                    ]
                )
                ->toArray();
            $prenote->controllers()->sync([]);
            $prenote->controllers()->sync($controllersToSync);
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

    public static function removeFromPrenoteOrder(Prenote $prenote, string|int|ControllerPosition $position): void
    {
        DB::transaction(function () use ($prenote, $position) {
            $controller = match (true) {
                is_int($position) => ControllerPosition::fromId($position),
                is_string($position) => ControllerPosition::fromCallsign($position),
                default => $position
            };

            $order = 1;
            $controllersToSync = $prenote->controllers()
                ->where(self::CONTROLLERS_PRIMARY_COLUMN, '<>', $controller->id)
                ->get()
                ->mapWithKeys(function (ControllerPosition $position) use (&$order) {
                    return [$position->id => ['order' => $order++]];
                })
                ->toArray();

            $prenote->controllers()->sync([]);
            $prenote->controllers()->sync($controllersToSync);
        });
    }

    public static function moveControllerInPrenoteOrder(
        Prenote|int $prenote,
        ControllerPosition|int $position,
        bool $up
    ): void {
        $position = is_int($position)
            ? ControllerPosition::fromId($position)
            : $position;

        $prenote = is_int($prenote)
            ? Prenote::findOrFail($prenote)
            : $prenote;

        $positions = $prenote->controllers()
            ->pluck(self::CONTROLLERS_PRIMARY_COLUMN);
        $positionToSwap = $positions->search(fn (int $prenotePosition) => $prenotePosition === $position->id);

        if ($positionToSwap === false) {
            throw new InvalidArgumentException('Position not in prenote order');
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

        static::setPositionsForPrenote(
            $prenote,
            $arrayPositions
        );
    }
}
