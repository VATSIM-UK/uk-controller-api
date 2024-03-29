<?php

namespace App\Filament\Helpers;

enum SelectOptionCacheKeys: string
{
    case AircraftTypes = 'SELECT_OPTIONS_AIRCRAFT_TYPES';
    case Airfields = 'SELECT_OPTIONS_AIRFIELDS';
    case Airlines = 'SELECT_OPTIONS_AIRLINES';
    case ControllerPositions = 'SELECT_OPTIONS_CONTROLLER_POSITIONS';
    case Handoffs = 'SELECT_OPTIONS_HANDOFFS';
    case NonAirfieldHandoffs = 'SELECT_OPTIONS_NON_AIRFIELD_HANDOFFS';
    case Runways = 'SELECT_OPTIONS_RUNWAYS';
    case WakeSchemes = 'SELECT_OPTIONS_WAKE_SCHEMES';

    case FirExitPoints = 'SELECT_OPTIONS_FIR_EXIT_POINTS';
    case Stands = 'SELECT_OPTIONS_STANDS';
}
