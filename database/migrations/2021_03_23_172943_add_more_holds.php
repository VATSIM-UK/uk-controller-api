<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMoreHolds extends Migration
{
    const NAVAIDS = [
        [
            'identifier' => 'NQY',
            'latitude' => 'N050.26.33.160',
            'longitude' => 'W004.59.48.030',
        ],
        [
            'identifier' => 'EX',
            'latitude' => 'N050.45.07.750',
            'longitude' => 'W003.17.42.170',
        ],
        [
            'identifier' => 'TOMPO',
            'latitude' => 'N050.42.43.000',
            'longitude' => 'W003.33.19.000',
        ],
        [
            'identifier' => 'BIA',
            'latitude' => 'N050.46.39.620',
            'longitude' => 'W001.50.32.950',
        ],
        [
            'identifier' => 'SAM',
            'latitude' => 'N050.57.18.900',
            'longitude' => 'W001.20.42.200',
        ],
        [
            'identifier' => 'NEDUL',
            'latitude' => 'N050.39.58.000',
            'longitude' => 'W001.32.52.000',
        ],
        [
            'identifier' => 'SPEAR',
            'latitude' => 'N051.34.34.000',
            'longitude' => 'E000.42.01.000',
        ],
        [
            'identifier' => 'GEGMU',
            'latitude' => 'N051.42.53.000',
            'longitude' => 'E001.06.34.000',
        ],
        [
            'identifier' => 'NWI',
            'latitude' => 'N052.40.39.150',
            'longitude' => 'E001.17.29.410',
        ],
        [
            'identifier' => 'FNY',
            'latitude' => 'N053.28.29.360',
            'longitude' => 'W001.00.06.220',
        ],
        [
            'identifier' => 'KELLY',
            'latitude' => 'N053.54.19.000',
            'longitude' => 'W004.21.51.000',
        ],
        [
            'identifier' => 'VANIN',
            'latitude' => 'N053.59.07.000',
            'longitude' => 'W004.02.21.000',
        ],
        [
            'identifier' => 'IOM',
            'latitude' => 'N054.04.00.720',
            'longitude' => 'W004.45.48.510',
        ],
        [
            'identifier' => 'RWY',
            'latitude' => 'N054.04.51.900',
            'longitude' => 'W004.37.22.400',
        ],
        [
            'identifier' => 'MIKEL',
            'latitude' => 'N054.16.30.000',
            'longitude' => 'W004.51.46.000',
        ],
        [
            'identifier' => 'TD',
            'latitude' => 'N054.33.37.880',
            'longitude' => 'W001.20.01.100',
        ],
        [
            'identifier' => 'NT',
            'latitude' => 'N055.03.01.380',
            'longitude' => 'W001.38.33.650',
        ],
        [
            'identifier' => 'ETSES',
            'latitude' => 'N054.42.35.150',
            'longitude' => 'W001.41.43.550',
        ],
        [
            'identifier' => 'TRN',
            'latitude' => 'N055.18.48.000',
            'longitude' => 'W004.47.02.000',
        ],
        [
            'identifier' => 'SUMIN',
            'latitude' => 'N055.19.46.000',
            'longitude' => 'W004.03.18.000',
        ],
        [
            'identifier' => 'INS',
            'latitude' => 'N057.32.33.490',
            'longitude' => 'W004.02.27.990',
        ],
        [
            'identifier' => 'BONBY',
            'latitude' => 'N057.53.30.000',
            'longitude' => 'W004.20.36.000',
        ],
        [
            'identifier' => 'GARVA',
            'latitude' => 'N057.41.18.000',
            'longitude' => 'W004.29.41.000',
        ],
        [
            'identifier' => 'GUSSI',
            'latitude' => 'N057.12.47.000',
            'longitude' => 'W004.07.27.000',
        ],
        [
            'identifier' => 'ADN',
            'latitude' => 'N057.18.37.620',
            'longitude' => 'W002.16.01.950',
        ],
        [
            'identifier' => 'ATF',
            'latitude' => 'N057.04.39.050',
            'longitude' => 'W002.06.20.520',
        ],
    ];

    const HOLDS = [
        'NQY' => [
            [
                'inbound_heading' => 281,
                'minimum_altitude' => 2500,
                'maximum_altitude' => 400,
                'turn_direction' => 'left',
                'description' => 'NQY Runway 12',
            ],
            [
                'inbound_heading' => 141,
                'minimum_altitude' => 2500,
                'maximum_altitude' => 4000,
                'turn_direction' => 'left',
                'description' => 'NQY Runway 30',
            ],
        ],
        'EX' => [
            [
                'inbound_heading' => 108,
                'minimum_altitude' => 2100,
                'maximum_altitude' => 4000,
                'turn_direction' => 'left',
                'description' => 'Exeter',
            ],
        ],
        'TOMPO' => [
            [
                'inbound_heading' => 79,
                'minimum_altitude' => 2800,
                'maximum_altitude' => 4000,
                'turn_direction' => 'left',
                'description' => 'TOMPO',
            ],
        ],
        'BIA' => [
            [
                'inbound_heading' => 75,
                'minimum_altitude' => 1500,
                'maximum_altitude' => 3000,
                'turn_direction' => 'left',
                'description' => 'TOMPO',
            ]
        ],
        'SAM' => [
            [
                'inbound_heading' => 30,
                'minimum_altitude' => 2000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'right',
                'description' => 'SAM',
            ]
        ],
        'NEDUL' => [
            [
                'inbound_heading' => 24,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 15000,
                'turn_direction' => 'right',
                'description' => 'NEDUL',
            ]
        ],
        'SPEAR' => [
            [
                'inbound_heading' => 193,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 7000,
                'turn_direction' => 'right',
                'description' => 'SPEAR',
            ]
        ],
        'GEGMU' => [
            [
                'inbound_heading' => 263,
                'minimum_altitude' => 4000,
                'maximum_altitude' => 6000,
                'turn_direction' => 'right',
                'description' => 'GEGMU',
            ]
        ],
        'NWI' => [
            [
                'inbound_heading' => 268,
                'minimum_altitude' => 2000,
                'maximum_altitude' => 3000,
                'turn_direction' => 'right',
                'description' => 'NWI',
            ],
        ],
        'FNY' => [
            [
                'inbound_heading' => 199,
                'minimum_altitude' => 2000,
                'maximum_altitude' => 8000,
                'turn_direction' => 'left',
                'description' => 'FNY',
            ],
        ],
        'IOM' => [
            [
                'inbound_heading' => 80,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 6000,
                'turn_direction' => 'right',
                'description' => 'IOM',
            ]
        ],
        'RWY' => [
            [
                'inbound_heading' => 260,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 6000,
                'turn_direction' => 'left',
                'description' => 'Ronaldsway Runway 08',
            ],
            [
                'inbound_heading' => 80,
                'minimum_altitude' => 3000,
                'maximum_altitude' => 6000,
                'turn_direction' => 'right',
                'description' => 'Ronaldsway Runway 26',
            ],
        ],
        'TD' => [
            [
                'inbound_heading' => 228,
                'minimum_altitude' => 2500,
                'maximum_altitude' => 6000,
                'turn_direction' => 'right',
                'description' => 'Teeside',
            ]
        ],
        'NT' => [
            [
                'inbound_heading' => 246,
                'minimum_altitude' => 2000,
                'maximum_altitude' => 6000,
                'turn_direction' => 'left',
                'description' => 'Newcastle',
            ]
        ],
        'ETSES' => [
            [
                'inbound_heading' => 11,
                'minimum_altitude' => 9000,
                'maximum_altitude' => 10000,
                'turn_direction' => 'left',
                'description' => 'ETSES',
            ]
        ],
        'TRN' => [
            [
                'inbound_heading' => 29,
                'minimum_altitude' => 6000,
                'maximum_altitude' => 9000,
                'turn_direction' => 'left',
                'description' => 'TRN',
            ]
        ],
        'SUMIN' => [
            [
                'inbound_heading' => 270,
                'minimum_altitude' => 6000,
                'maximum_altitude' => 9000,
                'turn_direction' => 'left',
                'description' => 'SUMIN',
            ]
        ],
        'ADN' => [
            [
                'inbound_heading' => 160,
                'minimum_altitude' => 2500,
                'maximum_altitude' => 11000,
                'turn_direction' => 'left',
                'description' => 'ADN',
            ]
        ],
        'ATF' => [
            [
                'inbound_heading' => 340,
                'minimum_altitude' => 2500,
                'maximum_altitude' => 11000,
                'turn_direction' => 'right',
                'description' => 'ATF',
            ]
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::NAVAIDS as $navaid) {
            $navaidId = $this->makeNavaid($navaid);
            if (isset(self::HOLDS[$navaid['identifier']])) {
                foreach (self::HOLDS[$navaid['identifier']] as $hold) {
                    $this->makeHold($navaidId, $hold);
                }
            }
        }

        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('navaids')->whereIn('identifier', array_column(self::NAVAIDS, 'identifier'))->delete();
        DependencyService::touchDependencyByKey('DEPENDENCY_NAVAIDS');
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }

    private function makeNavaid(array $navaidData): int
    {
        return DB::table('navaids')->insertGetId(array_merge($navaidData, ['created_at' => Carbon::now()]));
    }

    private function makeHold(int $navaidId, array $publishedHoldData): int
    {
        return DB::table('holds')->insertGetId(
            array_merge($publishedHoldData, ['navaid_id' => $navaidId, 'created_at' => Carbon::now()])
        );
    }
}
