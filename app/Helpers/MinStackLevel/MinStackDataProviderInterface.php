<?php

namespace App\Helpers\MinStackLevel;

/**
 * Interface MinStackDataProviderInterface
 *
 * An interface implemented by classes that can provide the necessary data
 * to calculate a minimum stack level
 */
interface MinStackDataProviderInterface
{
    /**
     * The facility against which the MSL should be calculated
     *
     * @return string
     */
    public function calculationFacility() : string;

    /**
     * The transition altitude for the facility in question
     *
     * @return int
     */
    public function transitionAltitude() : int;

    /**
     * True if the facility considers standard pressure (1013) to be
     * high
     *
     * @return bool
     */
    public function standardPressureHigh() : bool;
}
