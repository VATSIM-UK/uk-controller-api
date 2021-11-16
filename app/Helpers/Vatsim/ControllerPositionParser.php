<?php

namespace App\Helpers\Vatsim;

use Illuminate\Support\Str;

class ControllerPositionParser
{
    private const STANDARD_SEPARATOR_CHAR = '_';
    private const ALTERNATE_SEPARATOR_CHAR = '-';

    public function parse(ControllerPositionInterface $position): ?ParsedControllerPosition
    {
        $normalisedCallsign = $this->normaliseCallsign($position->getCallsign());
        if (!Str::contains($normalisedCallsign, self::STANDARD_SEPARATOR_CHAR)) {
            return null;
        }

        return new ParsedControllerPosition(
            $this->parseFacility($normalisedCallsign),
            $this->parseUnitType($normalisedCallsign),
            $position->getFrequency()
        );
    }

    private function normaliseCallsign(string $callsign): string
    {
        return Str::replace(self::ALTERNATE_SEPARATOR_CHAR, self::STANDARD_SEPARATOR_CHAR, $callsign);
    }

    private function parseFacility(string $callsign): string
    {
        return $this->splitCallsignToParts($callsign)[0];
    }

    private function parseUnitType(string $callsign): string
    {
        $parts = $this->splitCallsignToParts($callsign);
        return $parts[sizeof($parts) - 1];
    }

    private function splitCallsignToParts(string $callsign): array
    {
        return explode(self::STANDARD_SEPARATOR_CHAR, $callsign);
    }
}
