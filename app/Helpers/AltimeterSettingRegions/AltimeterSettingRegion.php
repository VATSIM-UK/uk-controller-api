<?php
namespace App\Helpers\AltimeterSettingRegions;

/**
 * Represents an altimeter setting region and how we can calculate the regional pressure
 * based on the concerned airfields.
 *
 * Class AltimeterSettingRegion
 * @package App\Helpers
 */
class AltimeterSettingRegion
{
    // The name of the ASR
    private $name;

    // List of Airfields.
    private $airfields;

    // How much to vary the regional pressure by
    private $variation;

    // The default QNH to start as the lowest
    const DEFAULT_MIN_QNH = 9999;

    /**
     * AltimeterSettingRegion constructor.
     *
     * @param string $name      The name of the ASR
     * @param int    $variation The maximum amount we randombly vary the regional pressure
     *                          by to simulate regions of low airfield coverage.
     * @param array  $airfields List of airfields
     */
    public function __construct(string $name, int $variation, array $airfields)
    {
        $this->name = $name;
        $this->variation = $variation;
        $this->airfields = $airfields;
    }

    /**
     * Returns the regional pressure setting for the ASR as an integer.
     *
     * @param  array $pressures Array of airfield name => QNH
     * @return int The QNH for the region
     */
    public function calculateRegionalPressure(array $pressures) : int
    {
        $lowestQnh = self::DEFAULT_MIN_QNH;
        foreach ($this->airfields as $airfield) {
            $lowestQnh = (isset($pressures[$airfield]) && $pressures[$airfield] < $lowestQnh)
                ? $pressures[$airfield]
                : $lowestQnh;
        }


        // Vary the regional pressure a little bit, if we haven't got many METARs to go on.
        return ($lowestQnh !== self::DEFAULT_MIN_QNH && $this->variation !== 0)
            ? $this->applyVariation($lowestQnh)
            : $lowestQnh;
    }

    /**
     * Returns the name of the region.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Vary the QNH a little bit - to be used when we only have a few airfields to go by and thus the QNH is uncertain.
     *
     * @param  int $original The original QNH
     * @return int The varied QNH.
     */
    private function applyVariation(int $original) : int
    {
        return rand($original - $this->variation, $original + $this->variation);
    }
}
