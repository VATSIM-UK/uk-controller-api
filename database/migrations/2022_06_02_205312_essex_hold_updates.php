<?php

use App\Models\Hold\Hold;
use App\Models\Navigation\Navaid;
use App\Services\SectorfileService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class EssexHoldUpdates extends Migration
{
    const HOLDS = [
        [
            'identifier' => 'ZAGZO',
            'latitude' => 'N052.18.12.970',
            'longitude' => 'W000.13.52.540',
            'inbound_heading' => 149,
            'minimum_altitude' => 8000,
            'maximum_altitude' => 14000,
            'turn_direction' => 'right',
            'description' => 'ZAGZO',
        ],
        [
            'identifier' => 'WOBUN',
            'latitude' => 'N052.01.10.000',
            'longitude' => 'W000.44.00.000',
            'inbound_heading' => 83,
            'minimum_altitude' => 8000,
            'maximum_altitude' => 14000,
            'turn_direction' => 'left',
            'description' => 'WOBUN',
        ],
        [
            'identifier' => 'LAPRA',
            'latitude' => 'N052.07.07.000',
            'longitude' => 'E001.12.36.000',
            'inbound_heading' => 246,
            'minimum_altitude' => 15000,
            'maximum_altitude' => 21000,
            'turn_direction' => 'right',
            'description' => 'LAPRA',
        ],
        [
            'identifier' => 'MUCTE',
            'latitude' => 'N052.10.31.130',
            'longitude' => 'E001.12.25.430',
            'inbound_heading' => 277,
            'minimum_altitude' => 15000,
            'maximum_altitude' => 21000,
            'turn_direction' => 'right',
            'description' => 'MUCTE',
        ],
        [
            'identifier' => 'VATON',
            'latitude' => 'N051.26.04.000',
            'longitude' => 'W000.20.56.000',
            'inbound_heading' => 25,
            'minimum_altitude' => 18000,
            'maximum_altitude' => 20000,
            'turn_direction' => 'left',
            'description' => 'VATON',
        ],
        [
            'identifier' => 'BOMBO',
            'latitude' => 'N051.59.44.000',
            'longitude' => 'W000.23.47.000',
            'inbound_heading' => 91,
            'minimum_altitude' => 8000,
            'maximum_altitude' => 14000,
            'turn_direction' => 'left',
            'description' => 'BOMBO',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            // New holds
            foreach (self::HOLDS as $hold) {
                $coordinate = SectorfileService::coordinateFromSectorfile($hold['latitude'], $hold['longitude']);
                $navaid = Navaid::firstOrCreate(
                    [
                        'identifier' => $hold['identifier'],
                        'latitude' => $coordinate->getLat(),
                        'longitude' => $coordinate->getLng(),
                    ]
                );

                Hold::create(
                    array_merge(
                        [
                            'navaid_id' => $navaid->id,
                        ],
                        $hold
                    )
                );
            }

            // Update LOREL
            Hold::where('navaid_id', Navaid::where('identifier', 'LOREL')->firstOrFail()->id)
                ->update(
                    [
                        'inbound_heading' => 187,
                    ]
                );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
