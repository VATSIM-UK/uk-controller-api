<?php

namespace App\Services;

use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Models\Sid;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OutOfRangeException;

class HandoffService
{
    public function getHandoffsV2Dependency(): array
    {
        return Handoff::with(['controllers' => function (BelongsToMany $query) {
            $query->orderBy('order');
        }])->get()->map(function (Handoff $handoff) {
            return [
                'id' => $handoff->id,
                'key' => $handoff->key,
                'controller_positions' => $handoff->controllers->pluck('id')->toArray(),
            ];
        })->toArray();
    }

    public static function createNewHandoffOrder(string $key, string $description, array $positions): ?Handoff
    {
        $handoff = null;
        DB::transaction(function () use ($key, $description, $positions, &$handoff) {
            $handoff = Handoff::create(
                [
                    'key' => $key,
                    'description' => $description,
                ]
            );

            self::setPositionsForHandoffOrder($key, $positions);
        });

        return $handoff;
    }

    public static function setPositionsForHandoffOrder(string $key, array $positions): void
    {
        self::setPositionsForHandoffId(
            DB::table('handoffs')->where('key', $key)->first()->id,
            $positions
        );
    }

    public static function setPositionsForHandoffId(int $handoff, array $positions): void
    {
        DB::transaction(function () use ($handoff, $positions) {
            DB::table('handoff_orders')
                ->where('handoff_id', $handoff)
                ->delete();

            $handoffOrder = [];
            foreach ($positions as $index => $position) {
                $handoffOrder[] = [
                    'handoff_id' => $handoff,
                    'controller_position_id' => ControllerPosition::where('callsign', $position)->firstOrFail()->id,
                    'order' => $index + 1,
                ];
            }

            DB::table('handoff_orders')->insert($handoffOrder);
        });
    }

    public static function insertIntoOrderBefore(
        string $handoffKey,
        string $positionToInsert,
        string $insertBefore
    ): void {
        try {
            DB::beginTransaction();
            // Get the models
            $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
            $beforePosition = ControllerPosition::where('callsign', $insertBefore)->firstOrFail();
            $handoff = Handoff::where('key', $handoffKey)->firstOrFail();

            // Check the insert before is in the handoff order
            $handoffBefore = $handoff->controllers->where('id', $beforePosition->id)->first();
            if (!$handoffBefore) {
                throw new OutOfRangeException('Position to insert before not found in handoff order');
            }

            // Increment everything else
            $controllersToChange = $handoff->controllers()
                ->wherePivot('order', '>=', $handoffBefore->pivot->order)
                ->get();
            $controllersToChange = $controllersToChange->sortByDesc('pivot.order');

            $controllersToChange->each(function (ControllerPosition $position) use ($handoff) {
                DB::table('handoff_orders')
                    ->where(['controller_position_id' => $position->id, 'handoff_id' => $handoff->id])
                    ->update(['order' => DB::raw('`order` + 1')]);
            });

            // Pop the new position in
            $handoff->controllers()->attach($insertPosition->id, ['order' => $handoffBefore->pivot->order]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function insertIntoOrderAfter(string $handoffKey, string $positionToInsert, string $insertAfter): void
    {
        try {
            DB::beginTransaction();
            // Get the models
            $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
            $afterPosition = ControllerPosition::where('callsign', $insertAfter)->firstOrFail();
            $handoff = Handoff::where('key', $handoffKey)->firstOrFail();

            // Check the insert before is in the handoff order
            $handoffAfter = $handoff->controllers->where('id', $afterPosition->id)->first();
            if (!$handoffAfter) {
                throw new OutOfRangeException('Position to insert after not found in handoff order');
            }

            // Increment everything else
            $controllersToChange = $handoff->controllers()
                ->wherePivot('order', '>', $handoffAfter->pivot->order)
                ->orderBy('order', 'desc')
                ->get();

            $controllersToChange->each(function (ControllerPosition $position) use ($handoff) {
                DB::table('handoff_orders')
                    ->where(['controller_position_id' => $position->id, 'handoff_id' => $handoff->id])
                    ->update(['order' => DB::raw('`order` + 1')]);
            });

            // Pop the new position in
            $handoff->controllers()->attach($insertPosition->id, ['order' => $handoffAfter->pivot->order + 1]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function removeFromHandoffOrder(string $handoffKey, string $positionToRemove): void
    {
        self::removeFromHandoffOrderByModel(
            Handoff::where('key', $handoffKey)
                ->firstOrFail(),
            $positionToRemove
        );
    }

    public static function removeFromHandoffOrderByModel(Handoff $handoff, string $positionToRemove): void
    {
        // Get the models
        try {
            DB::beginTransaction();
            $removePosition = ControllerPosition::where('callsign', $positionToRemove)->firstOrFail();

            // Check the insert before is in the handoff order
            $handoffRemove = $handoff->controllers->where('id', $removePosition->id)->first();
            if (!$handoffRemove) {
                throw new OutOfRangeException('Position to remove not found in handoff order');
            }

            // Remove the position
            DB::table('handoff_orders')
                ->where(['controller_position_id' => $removePosition->id, 'handoff_id' => $handoff->id])->delete();

            // Decrement order on everything else.
            $controllersToChange = $handoff->controllers()
                ->wherePivot('order', '>', $handoffRemove->pivot->order)
                ->orderBy('order', 'asc')
                ->get();

            $controllersToChange->each(function (ControllerPosition $position) use ($handoff) {
                DB::table('handoff_orders')
                    ->where(['controller_position_id' => $position->id, 'handoff_id' => $handoff->id])
                    ->update(['order' => DB::raw('`order` - 1')]);
            });
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function updateAllHandoffsWithPosition(string $callsign, string $callsignToAdd, bool $before): void
    {
        $positionToAdd = ControllerPosition::where('callsign', $callsignToAdd)->firstOrFail();
        $positionToAddAdjacent = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $method = $before ? 'insertIntoOrderBefore' : 'insertIntoOrderAfter';

        foreach (self::getHandoffsForPosition($positionToAddAdjacent->id) as $handoff) {
            self::$method($handoff, $positionToAdd->callsign, $positionToAddAdjacent->callsign);
        }
    }

    public static function removePositionFromAllHandoffs(string $callsign): void
    {
        $postionToRemove = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        foreach (self::getHandoffsForPosition($postionToRemove->id) as $handoff) {
            self::removeFromHandoffOrder($handoff, $callsign);
        }
    }

    public static function setHandoffForSid(string $airfield, string $identifier, string $handoff)
    {
        Sid::whereHas('runway.airfield', function (Builder $airfieldQuery) use ($airfield) {
            return $airfieldQuery->where('code', $airfield);
        })
            ->where('identifier', $identifier)
            ->update(['handoff_id' => Handoff::where('key', $handoff)->firstOrFail()->id]);
    }

    public static function deleteHandoffByKey(string $handoffKey)
    {
        Handoff::where('key', $handoffKey)->delete();
    }

    private static function getHandoffsForPosition(int $positionId): Collection
    {
        return  DB::table('handoff_orders')
            ->join('handoffs', 'handoff_id', '=', 'handoffs.id')
            ->where('controller_position_id', $positionId)
            ->distinct()
            ->select('handoffs.key')
            ->pluck('handoffs.key');
    }
}
