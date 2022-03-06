<?php

use App\Models\Airfield\Airfield;
use App\Models\Airfield\VisualReferencePoint;
use App\Services\SectorfileService;
use Illuminate\Database\Migrations\Migration;

class AddVrpData extends Migration
{
    const VRPS = [
        [
            'name' => 'A1(M) Junction 4',
            'latitude' => '514645N',
            'longitude' => '0001328W',
            'airfields' => ['EGGW'],
        ],
        // ADD MORE
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::VRPS as $vrpData) {
            $coordinate = SectorfileService::coordinateFromNats($vrpData['latitude'], $vrpData['longitude']);
            $vrp = VisualReferencePoint::create(
                [
                    'name' => $vrpData['name'],
                    'latitude' =>  $coordinate->getLat(),
                    'longitude' => $coordinate->getLng()
                ]
            );

            $vrp->airfields()->sync(Airfield::whereIn('code', $vrpData['airfields'])->pluck('id'));
        }
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
