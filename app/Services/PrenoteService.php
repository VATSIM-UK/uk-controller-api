<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use App\Models\Sid;
use Illuminate\Support\Facades\DB;
use OutOfRangeException;

class PrenoteService
{
    public function getAllPrenotesWithControllers(): array
    {
        return array_merge($this->getAllSidPrenotes(), $this->getAllAirfieldPrenotes());
    }

    public function getAllSidPrenotes(): array
    {
        $prenotes = [];
        Sid::whereHas('prenotes')->get()->each(function (Sid $sid) use (&$prenotes) {
            $sid->prenotes->each(function (Prenote $prenote) use ($sid, &$prenotes) {
                $prenotes[] = [
                    'airfield' => $sid->airfield->code,
                    'departure' => $sid->identifier,
                    'type' => 'sid',
                    'recipient' => $prenote->controllers()->orderBy('order')->pluck('callsign')->toArray(),
                ];
            });
        });
        return $prenotes;
    }

    public function getAllAirfieldPrenotes(): array
    {
        $prenotes = [];
        Airfield::whereHas('prenotePairings')->get()->each(function (Airfield $airfield) use (&$prenotes) {
            $airfield->prenotePairings->each(function (Airfield $pairedAirfield) use ($airfield, &$prenotes) {
                $prenotes[] = [
                    'origin' => $airfield->code,
                    'destination' => $pairedAirfield->code,
                    'type' => 'airfieldPairing',
                    'recipient' => Prenote::findOrFail($pairedAirfield->pivot->prenote_id)
                        ->controllers()
                        ->orderBy('order')
                        ->pluck('callsign')
                        ->toArray(),
                ];
            });
        });
        return $prenotes;
    }

    public static function insertIntoOrderBefore(
        string $prenoteKey,
        string $positionToInsert,
        string $insertBefore
    ): void {
        // Get the models
        $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
        $beforePosition = ControllerPosition::where('callsign', $insertBefore)->firstOrFail();
        $prenote = Prenote::where('key', $prenoteKey)->firstOrFail();

        // Check the insert before is in the handoff order
        $prenoteBefore = $prenote->controllers->where('id', $beforePosition->id)->first();
        if (!$prenoteBefore) {
            throw new OutOfRangeException('Position to insert before not found in prenote order');
        }

        // Increment everything else
        $controllersToChange = $prenote->controllers()
            ->wherePivot('order', '>=', $prenoteBefore->pivot->order)
            ->orderBy('order', 'desc')
            ->get();

        $controllersToChange->each(function (ControllerPosition $position) use ($prenote) {
            DB::table('prenote_orders')
                ->where(['controller_position_id' => $position->id, 'prenote_id' => $prenote->id])
                ->update(['order' => DB::raw('`order` + 1')]);
        });

        // Pop the new position in
        $prenote->controllers()->attach($insertPosition->id, ['order' => $prenoteBefore->pivot->order]);
    }

    public static function insertIntoOrderAfter(string $prenoteKey, string $positionToInsert, string $insertAfter): void
    {
        // Get the models
        $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
        $afterPosition = ControllerPosition::where('callsign', $insertAfter)->firstOrFail();
        $prenote = Prenote::where('key', $prenoteKey)->firstOrFail();

        // Check the insert before is in the handoff order
        $prenoteAfter = $prenote->controllers->where('id', $afterPosition->id)->first();
        if (!$prenoteAfter) {
            throw new OutOfRangeException('Position to insert after not found in prenote order');
        }

        // Increment everything else
        $controllersToChange = $prenote->controllers()
            ->wherePivot('order', '>', $prenoteAfter->pivot->order)
            ->orderBy('order', 'desc')
            ->get();

        $controllersToChange->each(function (ControllerPosition $position) use ($prenote) {
            DB::table('prenote_orders')
                ->where(['controller_position_id' => $position->id, 'prenote_id' => $prenote->id])
                ->update(['order' => DB::raw('`order` + 1')]);
        });

        // Pop the new position in
        $prenote->controllers()->attach($insertPosition->id, ['order' => $prenoteAfter->pivot->order + 1]);
    }

    public static function removeFromPrenoteOrder(string $prenoteKey, string $positionToRemove): void
    {
        // Get the models
        $removePosition = ControllerPosition::where('callsign', $positionToRemove)->firstOrFail();
        $prenote = Prenote::where('key', $prenoteKey)->firstOrFail();

        // Check the insert before is in the handoff order
        $prenoteRemove = $prenote->controllers->where('id', $removePosition->id)->first();
        if (!$prenoteRemove) {
            throw new OutOfRangeException('Position to remove not found in prenote order');
        }

        // Remove the position
        DB::table('prenote_orders')
            ->where(['controller_position_id' => $removePosition->id, 'prenote_id' => $prenote->id])->delete();

        // Decrement order on everything else.
        $controllersToChange = $prenote->controllers()
            ->wherePivot('order', '>', $prenoteRemove->pivot->order)
            ->orderBy('order', 'asc')
            ->get();

        $controllersToChange->each(function (ControllerPosition $position) use ($prenote) {
            DB::table('prenote_orders')
                ->where(['controller_position_id' => $position->id, 'prenote_id' => $prenote->id])
                ->update(['order' => DB::raw('`order` - 1')]);
        });
    }

    public static function updateAllPrenotesWithPosition(string $callsign, string $callsignToAdd, bool $before): void
    {
        $positionToAdd = ControllerPosition::where('callsign', $callsignToAdd)->firstOrFail();
        $positionToAddAdjacent = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $prenotes = DB::table('prenote_orders')
            ->join('prenotes', 'prenote_id', '=', 'prenotes.id')
            ->where('controller_position_id', $positionToAddAdjacent->id)
            ->distinct()
            ->select('prenotes.key')
            ->pluck('prenotes.key');

        $method = $before ? 'insertIntoOrderBefore' : 'insertIntoOrderAfter';
        foreach ($prenotes as $prenote) {
            self::$method($prenote, $positionToAdd->callsign, $positionToAddAdjacent->callsign);
        }
    }

    public static function removePositionFromAllPrenotes(string $callsign): void
    {
        $postionToRemove = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $prenotes = DB::table('prenote_orders')
            ->join('prenotes', 'prenote_id', '=', 'prenotes.id')
            ->where('controller_position_id', $postionToRemove->id)
            ->distinct()
            ->select('prenotes.key')
            ->pluck('prenotes.key');

        foreach ($prenotes as $prenote) {
            self::removeFromPrenoteOrder($prenote, $callsign);
        }
    }
}
