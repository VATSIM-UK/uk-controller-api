<?php

namespace App\Models\Vatsim;

/**
 * @see https://www.euroscope.hu/wp/non-standard-extensions/
 */
enum GroundState: string
{
    case NotStarted = 'NSTS';
    case Startup = 'STUP';
    case Pushback = 'PUSH';
    case Taxi = 'TAXI';
    case Departure = 'DEPA';
    case Arrival = 'ARR';
    case TaxiIn = 'TXIN';
    case Parked = 'PARK';
}
