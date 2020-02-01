<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use Illuminate\Support\Facades\DB;
use OutOfRangeException;

class AirfieldService
{
    /**
     * @return array
     */
    public function getAllAirfieldsWithRelations() : array
    {
        $airfields = [];
        Airfield::all()->each(function (Airfield $airfield) use (&$airfields) {
            $airfieldPairings = $airfield->prenotePairings()->select(['destination_airfield_id', 'prenote_id'])->get();
            $airfieldArray = [];

            $airfieldPairings->each(function (Airfield $airfield) use (&$airfieldArray) {
                $airfieldArray[$airfield->destination_airfield_id][] = $airfield->prenote_id;
            });

            $airfields[] = array_merge(
                $airfield->toArray(),
                [
                    'controllers' =>
                    $airfield->controllers()->orderBy('order')->pluck('controller_position_id')->toArray(),
                    'pairing-prenotes' => $airfieldArray
                ]
            );
        });

        return $airfields;
    }

    public static function insertIntoOrderBefore(
        string $airfieldKey,
        string $positionToInsert,
        string $insertBefore
    ): void {
        try {
            DB::beginTransaction();
            // Get the models
            $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
            $beforePosition = ControllerPosition::where('callsign', $insertBefore)->firstOrFail();
            $airfield = Airfield::where('code', $airfieldKey)->firstOrFail();

            // Check the insert before is in the handoff order
            $airfieldBefore = $airfield->controllers->where('id', $beforePosition->id)->first();
            if (!$airfieldBefore) {
                throw new OutOfRangeException('Position to insert before not found in top down order');
            }

            // Increment everything else
            $controllersToChange = $airfield->controllers()
                ->wherePivot('order', '>=', $airfieldBefore->pivot->order)
                ->orderBy('order', 'desc')
                ->get();

            $controllersToChange->each(function (ControllerPosition $position) use ($airfield) {
                DB::table('top_downs')
                    ->where(['controller_position_id' => $position->id, 'airfield_id' => $airfield->id])
                    ->update(['order' => DB::raw('`order` + 1')]);
            });

            // Pop the new position in
            $airfield->controllers()->attach($insertPosition->id, ['order' => $airfieldBefore->pivot->order]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function insertIntoOrderAfter(string $airfieldKey, string $positionToInsert, string $insertAfter): void
    {
        try {
            DB::beginTransaction();
            // Get the models
            $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
            $afterPosition = ControllerPosition::where('callsign', $insertAfter)->firstOrFail();
            $airfield = Airfield::where('code', $airfieldKey)->firstOrFail();

            // Check the insert before is in the handoff order
            $airfieldAfter = $airfield->controllers->where('id', $afterPosition->id)->first();
            if (!$airfieldAfter) {
                throw new OutOfRangeException('Position to insert after not found in top down order');
            }

            // Increment everything else
            $controllersToChange = $airfield->controllers()
                ->wherePivot('order', '>', $airfieldAfter->pivot->order)
                ->orderBy('order', 'desc')
                ->get();

            $controllersToChange->each(function (ControllerPosition $position) use ($airfield) {
                DB::table('top_downs')
                    ->where(['controller_position_id' => $position->id, 'airfield_id' => $airfield->id])
                    ->update(['order' => DB::raw('`order` + 1')]);
            });

            // Pop the new position in
            $airfield->controllers()->attach($insertPosition->id, ['order' => $airfieldAfter->pivot->order + 1]);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function removeFromTopDownsOrder(string $airfieldKey, string $positionToRemove): void
    {
        try {
            DB::beginTransaction();
            // Get the models
            $removePosition = ControllerPosition::where('callsign', $positionToRemove)->firstOrFail();
            $airfield = Airfield::where('code', $airfieldKey)->firstOrFail();

            // Check the insert before is in the handoff order
            $airfieldRemove = $airfield->controllers->where('id', $removePosition->id)->first();
            if (!$airfieldRemove) {
                throw new OutOfRangeException('Position to remove not found in top down order');
            }

            // Remove the position
            DB::table('top_downs')
                ->where(['controller_position_id' => $removePosition->id, 'airfield_id' => $airfield->id])->delete();

            // Decrement order on everything else.
            $controllersToChange = $airfield->controllers()
                ->wherePivot('order', '>', $airfieldRemove->pivot->order)
                ->orderBy('order', 'asc')
                ->get();

            $controllersToChange->each(function (ControllerPosition $position) use ($airfield) {
                DB::table('top_downs')
                    ->where(['controller_position_id' => $position->id, 'airfield_id' => $airfield->id])
                    ->update(['order' => DB::raw('`order` - 1')]);
            });
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function updateAllTopDownsWithPosition(string $callsign, string $callsignToAdd, bool $before): void
    {
        $positionToAdd = ControllerPosition::where('callsign', $callsignToAdd)->firstOrFail();
        $positionToAddAdjacent = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $airfields = DB::table('top_downs')
            ->join('airfield', 'airfield_id', '=', 'airfield.id')
            ->where('controller_position_id', $positionToAddAdjacent->id)
            ->distinct()
            ->select('airfield.code')
            ->pluck('airfield.code');

        $method = $before ? 'insertIntoOrderBefore' : 'insertIntoOrderAfter';
        foreach ($airfields as $airfield) {
            self::$method($airfield, $positionToAdd->callsign, $positionToAddAdjacent->callsign);
        }
    }

    public static function removePositionFromAllTopDowns(string $callsign): void
    {
        $postionToRemove = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $airfields = DB::table('top_downs')
            ->join('airfield', 'airfield_id', '=', 'airfield.id')
            ->where('controller_position_id', $postionToRemove->id)
            ->distinct()
            ->select('airfield.code')
            ->pluck('airfield.code');

        foreach ($airfields as $airfield) {
            self::removeFromTopDownsOrder($airfield, $callsign);
        }
    }
}
