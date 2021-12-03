<?php

namespace App\Services\Metar\Parser;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PressureParser implements MetarParser
{
    const QNH_PATTERN = '/^Q(\d{4})$/';
    const ALTIMETER_PATTERN = '/^A(\d{4})$/';

    public function parse(Collection $metarTokens, Collection $parsedData): void
    {
        $metarTokens->each(function (string $token) use ($parsedData) {
            if ($this->tokenIsQnh($token)) {
                $this->parsePressureFromQnh($token, $parsedData);
                return false;
            } elseif ($this->tokenIsAltimeter($token)) {
                $this->parsePressureFromAltimeter($token, $parsedData);
                return false;
            }

            return true;
        });
    }

    private function parsePressureFromQnh(string $qnhToken, Collection $parsedData): void
    {
        $qnh = Str::substr($qnhToken, 1);
        $parsedData->offsetSet('qnh', (int) ($qnh[0] === '0' ? Str::substr($qnh, 1) : $qnh));
        $parsedData->offsetSet('altimeter', $this->getAltimeterFromQnh($parsedData->offsetGet('qnh')));
    }

    private function parsePressureFromAltimeter(string $altimeterToken, Collection $parsedData): void
    {
        $parsedData->offsetSet('altimeter', $this->altimeterStringToFloat(Str::substr($altimeterToken, 1)));
        $parsedData->offsetSet('qnh', $this->getQnhFromAltimeter($parsedData->offsetGet('altimeter')));
    }

    private function getAltimeterFromQnh(int $qnh): float
    {
        return round($qnh * 0.02953, 2);
    }

    private function getQnhFromAltimeter(float $altimeter): int
    {
        return (int)($altimeter * 33.86389);
    }

    private function altimeterStringToFloat(string $altimeter): float
    {
        return (float)sprintf('%s.%s', Str::substr($altimeter, 0, 2), Str::substr($altimeter, 2));
    }

    private function tokenIsQnh(string $token) : bool
    {
        return preg_match(self::QNH_PATTERN, $token);
    }

    private function tokenIsAltimeter(string $token) : bool
    {
        return preg_match(self::ALTIMETER_PATTERN, $token);
    }
}
