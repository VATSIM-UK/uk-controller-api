<?php

namespace App\Helpers\MinStackLevel;

use App\Models\Airfield;
use App\Services\MetarService;

class MinStackCalculator
{
    // Pressure boundaries in MSL calculations
    const STANDARD_PRESSURE = 1013;
    const LOW_PRESSURE_BOUNDARY = 978;

    // The calculation type where it's a direct airfield calculation
    const CALCULATION_TYPE_AIRFIELD = 'airfield';

    // The calculation type where we want the lowest QNH of many
    const CALCULATION_TYPE_LOWEST = 'lowest-of';

    /**
     * @var MetarService
     */
    private $metarService;

    /**
     * MinStackCalculator constructor.
     * @param MetarService $metarService
     */
    public function __construct(MetarService $metarService)
    {
        $this->metarService = $metarService;
    }

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

    /**
     * Calculate the min stack level in a direct relation.
     *
     * @param string $code
     * @param int $transitionAltitude
     * @param bool $standardHigh
     * @return int|null
     */
    public function calculateDirectMinStack(string $code, int $transitionAltitude, bool $standardHigh) : ?int
    {
        $qnh = $this->metarService->getQnhFromVatsimMetar($code);

        if ($qnh === null) {
            return null;
        }

        return self::calculateMinStack(
            $transitionAltitude,
            $qnh,
            $standardHigh
        );
    }

    /**
     * Calculate the MinStack where an airfields MinStack is defined based on the lowest
     * QNH in a group.
     *
     * @param int $transitionAltitude
     * @param bool $standardHigh
     * @param array $airfields
     * @return int|null
     */
    public function calculateLowestQnhMinStack(array $airfields): ?int
    {
        $qnhs = [];
        foreach ($airfields as $icao) {
            $qnh = $this->metarService->getQnhFromVatsimMetar($icao);
            if ($qnh === null) {
                continue;
            }

            $qnhs[$icao] = $qnh;
        }

        // No QNHs, stop
        if (empty($qnhs)) {
            return null;
        }

        // Get the airfield with the lowest QNH and calculate MSL
        $calculationIcao = array_keys($qnhs, min($qnhs))[0];
        $airfield = Airfield::where('code', $calculationIcao)->first();

        if ($airfield === null) {
            return null;
        }

        return self::calculateMinStack(
            $airfield->transition_altitude,
            min($qnhs),
            $airfield->standard_high
        );
    }
}
