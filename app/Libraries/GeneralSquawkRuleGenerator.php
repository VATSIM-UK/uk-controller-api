<?php
namespace App\Libraries;

use InvalidArgumentException;

/**
 * Generates the set of rules including order for which to search for a General
 * Use squawk.
 *
 * Class GeneralSquawkRuleGenerator
 * @package App\Libraries
 */

class GeneralSquawkRuleGenerator
{
    /**
     * Generates the search rules for general squawk codes, in the order in which they should be checked.
     *
     * @param string $departureAirport The departure airfield ICAO.
     * @param string $arrivalAirport The arrival airfields ICAO.
     * @return array Array of rules.
     */
    public function generateRules(string $departureAirport, string $arrivalAirport) : array
    {

        // Basic argument checks - ICAO codes.
        if ((strlen($departureAirport) != 4 || strlen($arrivalAirport) != 4) ||
            (!ctype_alpha($departureAirport) || !ctype_alpha($arrivalAirport))
        ) {
            throw new InvalidArgumentException('Invalid airfield ICAOs');
        }

        // Get the departure country and single letter
        $departureCountry = substr($departureAirport, 0, 2);
        $departureLetter = substr($departureAirport, 0, 1);
        $arrivalCountry = substr($arrivalAirport, 0, 2);
        $arrivalLetter = substr($arrivalAirport, 0, 1);


        // Return the rules
        return [
            ['departure_ident' => $departureAirport, 'arrival_ident' => $arrivalAirport],
            ['departure_ident' => $departureAirport, 'arrival_ident' => $arrivalCountry],
            ['departure_ident' => $departureCountry, 'arrival_ident' => $arrivalAirport],
            ['departure_ident' => $departureCountry, 'arrival_ident' => $arrivalCountry],
            ['departure_ident' => $departureLetter, 'arrival_ident' => $arrivalLetter],
            ['departure_ident' => $departureAirport, 'arrival_ident' => $arrivalLetter],
            ['departure_ident' => $departureLetter, 'arrival_ident' => $arrivalAirport],
            ['departure_ident' => $departureCountry, 'arrival_ident' => $arrivalLetter],
            ['departure_ident' => $departureLetter, 'arrival_ident' => $arrivalCountry],
            ['departure_ident' => null, 'arrival_ident' => $arrivalAirport],
            ['departure_ident' => $departureAirport, 'arrival_ident' => null],
            ['departure_ident' => null, 'arrival_ident' => $arrivalCountry],
            ['departure_ident' => $departureCountry, 'arrival_ident' => null],
            ['departure_ident' => null, 'arrival_ident' => $arrivalLetter],
            ['departure_ident' => $departureLetter, 'arrival_ident' => null],
            ['departure_ident' => "CCAMS", 'arrival_ident' => "CCAMS"],
        ];
    }
}
