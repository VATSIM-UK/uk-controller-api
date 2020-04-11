<?php

// Config for downloading METARs in the context of MSL and RPS
return [
    'vatsim_url' => env('VATSIM_METAR_URL', 'metar.vatsim.net'),
    'regional_url' => env('APP_REGIONAL_PRESSURES_URL'),
];
