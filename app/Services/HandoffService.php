<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use App\Models\Sid;
use Exception;
use Illuminate\Support\Facades\DB;
use OutOfRangeException;

class HandoffService
{
    public function getAllHandoffsWithControllers(): array
    {
        return Handoff::all()->mapWithKeys(function (Handoff $handoff) {
            return [
                $handoff->key => $handoff->controllers()->orderBy('order', 'asc')->pluck('callsign')->toArray(),
            ];
        })->toArray();
    }

    public function mapSidsToHandoffs(): array
    {
        $sidMap = [];
        Sid::whereHas('handoff')->each(function (Sid $sid) use (&$sidMap) {
            $sidMap[$sid->airfield->code][$sid->identifier] = $sid->handoff->key;
        });

        return $sidMap;
    }

    public static function createNewHandoffOrder(string $key, string $description, array $positions): void
    {
        DB::transaction(function () use ($key, $description, $positions) {
            $handoff = Handoff::create(
                [
                    'key' => $key,
                    'description' => $description,
                ]
            );


            $handoffOrder = [];
            foreach ($positions as $index => $position) {
                $handoffOrder[] = [
                    'handoff_id' => $handoff->id,
                    'controller_position_id' => ControllerPosition::where('callsign', $position)->firstOrFail()->id,
                    'order' => $index + 1,
                ];
            }

            DB::table('handoff_orders')
                ->insert($handoffOrder);
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
                ->orderBy('order', 'desc')
                ->get();

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
        // Get the models
        try {
            DB::beginTransaction();
            $removePosition = ControllerPosition::where('callsign', $positionToRemove)->firstOrFail();
            $handoff = Handoff::where('key', $handoffKey)->firstOrFail();

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

        $handoffs = DB::table('handoff_orders')
            ->join('handoffs', 'handoff_id', '=', 'handoffs.id')
            ->where('controller_position_id', $positionToAddAdjacent->id)
            ->distinct()
            ->select('handoffs.key')
            ->pluck('handoffs.key');

        $method = $before ? 'insertIntoOrderBefore' : 'insertIntoOrderAfter';

        foreach ($handoffs as $handoff) {
            self::$method($handoff, $positionToAdd->callsign, $positionToAddAdjacent->callsign);
        }
    }

    public static function removePositionFromAllHandoffs(string $callsign): void
    {
        $postionToRemove = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $handoffs = DB::table('handoff_orders')
            ->join('handoffs', 'handoff_id', '=', 'handoffs.id')
            ->where('controller_position_id', $postionToRemove->id)
            ->distinct()
            ->select('handoffs.key')
            ->pluck('handoffs.key');

        foreach ($handoffs as $handoff) {
            self::removeFromHandoffOrder($handoff, $callsign);
        }
    }

    public static function setHandoffForSid(string $airfield, string $identifier, string $handoff)
    {
        /** @var Sid */
        $sid = Sid::where('airfield_id', Airfield::where('code', $airfield)->firstOrFail()->id)->firstOrFail();
        $sid->handoff()->save($handoff);
    }
}
