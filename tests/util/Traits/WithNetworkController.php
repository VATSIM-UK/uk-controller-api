<?php

namespace util\Traits;

use App\Models\Vatsim\NetworkControllerPosition;

trait WithNetworkController
{
    public function setNetworkController(int $cid, int $controllerPositionId = 1)
    {
        NetworkControllerPosition::upsert(
            [
                'cid' => $cid,
                'callsign' => 'FOO_TWR',
                'frequency' => 123.456,
                'controller_position_id' => $controllerPositionId,
            ],
            ['cid']
        );
    }

    public function setNetworkControllerUnrecognisedPosition(int $cid)
    {
        NetworkControllerPosition::upsert(
            [
                'cid' => $cid,
                'callsign' => 'FOO_TWR',
                'frequency' => 123.456,
                'controller_position_id' => null,
            ],
            ['cid']
        );
    }

    public function logoutNetworkController(int $cid)
    {
        NetworkControllerPosition::where('cid', $cid)->delete();
    }
}
