<?php
namespace App\Services;

use App\Exceptions\MetarException;

/**
 * Service for parsing data in relation to METARs.
 *
 * Class MetarService
 * @package App\Services
 */
class MetarService
{
    /**
     * Returns the QNH from a METAR.
     *
     * @param  string $metar
     * @return mixed The QNH
     * @throws MetarException If the METAR doesn't have a QNH or has more than one
     */
    public function getQnhFromMetar(string $metar) : int
    {
        $matches = [];
        preg_match('/Q\d{4}/', $metar, $matches);

        // Check for dodgy metars
        if (count($matches) === 0) {
            throw new MetarException('QNH not found in METAR: ' . $metar);
        }

        // Strip the Q and handle pressures < 1000hpa
        $value = substr($matches[0], 1);
        return (int) ($value[0] === '0') ? substr($value, 1) : $value;
    }
}
