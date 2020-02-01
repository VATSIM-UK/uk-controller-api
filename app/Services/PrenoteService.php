<?php

namespace App\Services;

use App\Models\Airfield\Airfield;
use App\Models\Controller\Prenote;
use App\Models\Sid;

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
}
