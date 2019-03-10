<?php

namespace App\Helpers\MinStackLevel;

class MinStackCalculator
{
    // Pressure boundaries in MSL calculations
    const STANDARD_PRESSURE = 1013;
    const LOW_PRESSURE_BOUNDARY = 978;

    /**
     * @param MinStackDataProviderInterface $dataProvider
     * @param int $qnh
     * @return int
     */
    public static function calculateMinStack(MinStackDataProviderInterface $dataProvider, int $qnh) : int
    {
        if (
            $qnh > self::STANDARD_PRESSURE ||
            ($qnh === self::STANDARD_PRESSURE && $dataProvider->standardPressureHigh())
        ) {
            return $dataProvider->transitionAltitude() + 1000;
        }

        if ($qnh >= self::LOW_PRESSURE_BOUNDARY) {
            return $dataProvider->transitionAltitude() + 2000;
        }

        return $dataProvider->transitionAltitude() + 3000;
    }
}
