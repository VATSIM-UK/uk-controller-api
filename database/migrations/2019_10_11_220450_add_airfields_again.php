<?php

use App\Models\Airfield\Airfield;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAirfieldsAgain extends Migration
{
    const AIRFIELDS = [
        'EGEO',
        'EGPU',
        'EGPR',
        'EGPL',
        'EGQM',
        'EGOM',
        'EGOV',
        'EGCK',
        'EGOY',
        'EGCW',
        'EGWC',
        'EGBO',
        'EGOS',
        'EGXE',
        'EGXC',
        'EGXG',
        'EGNE',
        'EGYD',
        'EGXV',
        'EGDR',
        'EGHE',
        'EGHT',
        'EGFE',
        'EGFH',
        'EGHC',
        'EGHR',
        'EGVO',
        'EGTO',
        'EGDY',
        'EGDX',
        'EGYM',
        'EGXH',
        'EGUL',
        'EGUN',
        'EGYC',
        'EGBP',
        'EGVN',
        'EGPS',
        'EGQK',
        'EGQL',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::AIRFIELDS as $airfield) {
            Airfield::create(
                [
                    'code' => $airfield,
                    'transition_altitude' => 3000,
                    'standard_high' => false,
                    'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => $airfield]),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Airfield::whereIn('code', self::AIRFIELDS)->get()->each(function (Airfield $airfield) {
            $airfield->delete();
        });
    }
}
