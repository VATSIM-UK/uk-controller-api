<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Prenote;
use App\Models\Flightplan\FlightRules;
use App\Models\Sid;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OutOfRangeException;

class PrenoteService
{
    public function getPrenotesV2Dependency(): array
    {
        return Prenote::all()->map(function (Prenote $prenote) {
            return [
                'id' => $prenote->id,
                'key' => $prenote->key,
                'description' => $prenote->description,
                'controller_positions' => $prenote->controllers()
                    ->orderBy('order')
                    ->pluck('controller_positions.id')
                    ->toArray(),
            ];
        })->toArray();
    }

    /**
     * @deprecated
     */
    public function getAllPrenotesWithControllers(): array
    {
        return array_merge($this->getAllSidPrenotes(), $this->getAllAirfieldPrenotes());
    }

    /**
     * @deprecated
     */
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

    /**
     * @deprecated
     */
    public function getAllAirfieldPrenotes(): array
    {
        $flightRules = FlightRules::all()->mapWithKeys(function (FlightRules $flightRules) {
            return [$flightRules->id => $flightRules->euroscope_key];
        })->toArray();

        $prenotes = [];
        Airfield::whereHas('prenotePairings')->get()->each(
            function (Airfield $airfield) use (&$prenotes, $flightRules) {
                $airfield->prenotePairings->each(
                    function (Airfield $pairedAirfield) use ($airfield, &$prenotes, $flightRules) {
                        $prenotes[] = [
                            'origin' => $airfield->code,
                            'destination' => $pairedAirfield->code,
                            'type' => 'airfieldPairing',
                            'flight_rules' => $pairedAirfield->pivot->flight_rule_id
                                ? $flightRules[$pairedAirfield->pivot->flight_rule_id]
                                : null,
                            'recipient' => Prenote::findOrFail($pairedAirfield->pivot->prenote_id)
                                ->controllers()
                                ->orderBy('order')
                                ->pluck('callsign')
                                ->toArray(),
                        ];
                    }
                );
            }
        );
        return $prenotes;
    }

    public static function insertIntoOrderBefore(
        string $prenoteKey,
        string $positionToInsert,
        string $insertBefore
    ): void {
        try {
            DB::beginTransaction();
            // Get the models
            $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
            $beforePosition = ControllerPosition::where('callsign', $insertBefore)->firstOrFail();
            $prenote = Prenote::where('key', $prenoteKey)->firstOrFail();

            // Check the insert before is in the prenote order
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
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function insertIntoOrderAfter(string $prenoteKey, string $positionToInsert, string $insertAfter): void
    {
        try {
            DB::beginTransaction();
            // Get the models
            $insertPosition = ControllerPosition::where('callsign', $positionToInsert)->firstOrFail();
            $afterPosition = ControllerPosition::where('callsign', $insertAfter)->firstOrFail();
            $prenote = Prenote::where('key', $prenoteKey)->firstOrFail();

            // Check the insert before is in the prenote order
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
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function removeFromPrenoteOrder(string $prenoteKey, string $positionToRemove): void
    {
        try {
            DB::beginTransaction();
            // Get the models
            $removePosition = ControllerPosition::where('callsign', $positionToRemove)->firstOrFail();
            $prenote = Prenote::where('key', $prenoteKey)->firstOrFail();

            // Check the insert before is in the prenote order
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
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public static function updateAllPrenotesWithPosition(string $callsign, string $callsignToAdd, bool $before): void
    {
        $positionToAdd = ControllerPosition::where('callsign', $callsignToAdd)->firstOrFail();
        $positionToAddAdjacent = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        $method = $before ? 'insertIntoOrderBefore' : 'insertIntoOrderAfter';
        foreach (self::getPrenotesForPosition($positionToAddAdjacent->id) as $prenote) {
            self::$method($prenote, $positionToAdd->callsign, $positionToAddAdjacent->callsign);
        }
    }

    public static function removePositionFromAllPrenotes(string $callsign): void
    {
        $postionToRemove = ControllerPosition::where('callsign', $callsign)->firstOrFail();

        foreach (self::getPrenotesForPosition($postionToRemove->id) as $prenote) {
            self::removeFromPrenoteOrder($prenote, $callsign);
        }
    }

    private static function getPrenotesForPosition(int $positionId): Collection
    {
        return DB::table('prenote_orders')
            ->join('prenotes', 'prenote_id', '=', 'prenotes.id')
            ->where('controller_position_id', $positionId)
            ->distinct()
            ->select('prenotes.key')
            ->pluck('prenotes.key');
    }

    public static function createNewAirfieldPairingFromPrenote(
        string $departureAirfield,
        string $arrivalAirfield,
        string $prenoteKey,
        int $flightRuleId
    ): void {
        DB::transaction(
            function () use ($departureAirfield, $arrivalAirfield, $prenoteKey, $flightRuleId) {
                DB::table('airfield_pairing_prenotes')
                    ->insert(
                        [
                            'origin_airfield_id' => Airfield::where('code', $departureAirfield)->firstOrFail()->id,
                            'destination_airfield_id' => Airfield::where('code', $arrivalAirfield)->firstOrFail()->id,
                            'prenote_id' => Prenote::where('key', $prenoteKey)->firstOrFail()->id,
                            'flight_rule_id' => $flightRuleId,
                            'created_at' => Carbon::now(),
                        ]
                    );
            }
        );
    }

    public static function deleteAirfieldPairingPrenoteForPair(
        string $departureAirfield,
        string $arrivalAirfield
    ): void {
        DB::transaction(
            function () use ($departureAirfield, $arrivalAirfield) {
                DB::table('airfield_pairing_prenotes')
                    ->where('origin_airfield_id', Airfield::where('code', $departureAirfield)->firstOrFail()->id)
                    ->where('destination_airfield_id', Airfield::where('code', $arrivalAirfield)->firstOrFail()->id)
                    ->delete();
            }
        );
    }
}
