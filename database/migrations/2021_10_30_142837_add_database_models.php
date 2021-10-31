<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\AltimeterSettingRegions\AltimeterSettingRegion;
use App\Models\Controller\Handoff;
use App\Models\Hold\Hold;
use App\Models\Hold\HoldRestriction;
use App\Models\Release\Enroute\EnrouteReleaseType;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class AddDatabaseModels extends Migration
{
    private const DEPENDENCY_MAP = [
        'DEPENDENCY_HOLDS' => [
            Hold::class,
            HoldRestriction::class,
        ],
        'DEPENDENCY_WAKE' => [
            // TODO: Add model for Aircraft/Wake
            Aircraft::class,
            WakeCategory::class
        ],
        'DEPENDENCY_ASR' => [
            AltimeterSettingRegion::class
        ],
        'DEPENDENCY_NAVAIDS' => [
            \App\Models\Navigation\Navaid::class,
        ],
        'DEPENDENCY_ENROUTE_RELEASE_TYPES' => [
            EnrouteReleaseType::class,
        ],
        'DEPENDENCY_STANDS' => [
            Stand::class,
        ],
        'DEPENDENCY_RECAT' => [
            // TODO: Add model for Aircraft/Wake
            Aircraft::class,
            WakeCategory::class,
        ],
        'DEPENDENCY_AIRFIELD' => [
            Airfield::class,
        ]
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
