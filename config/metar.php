<?php

// Config for downloading METARs in the context of MSL and RPS
return [
    'vatsim_url' => env('VATSIM_METAR_URL', 'https://metar.vatsim.net'),
    'regional_url' => env('APP_REGIONAL_PRESSURES_URL', 'https://www.aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requesttype=retrieve&format=xml&hoursBeforeNow=3&mostRecentForEachStation=constraint&stationString=~gb&fields=raw_text,station_id'),
];
