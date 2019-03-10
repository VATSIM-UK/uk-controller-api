<?php

namespace App\Helpers\MinStackLevel;

class MinStackCalculator
{
    const STANDARD_PRESSURE = 1013;
    const LOW_PRESSURE_BOUNDARY = 978;

    /**
     * @param int $transitionAltitude
     * @param int $qnh
     * @param bool $standardHigh
     * @return int
     */
    public static function calculateMinStack(int $transitionAltitude, int $qnh, bool $standardHigh) : int
    {
        if ($qnh > self::STANDARD_PRESSURE || ($qnh === self::STANDARD_PRESSURE && $standardHigh)) {
            return $transitionAltitude + 1000;
        }

        if ($qnh >= self::LOW_PRESSURE_BOUNDARY) {
            return $transitionAltitude + 2000;
        }

        return $transitionAltitude + 3000;
    }
}
