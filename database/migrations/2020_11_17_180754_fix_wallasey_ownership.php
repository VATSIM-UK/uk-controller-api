<?php

use App\Services\AirfieldService;
use App\Services\DependencyService;
use App\Services\HandoffService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixWallaseyOwnership extends Migration
{
    // The airfields whose top-downs are affected
    const AIRFIELDS = [
        'EGGP',
        'EGNR',
    ];

    // The callsign of PC Wallasey
    const POSITION_WALLASEY = 'MAN_WL_CTR';
    const POSITION_WALLASEY_PENIL = 'MAN_WP_CTR';

    // The position
    const POSITION_TO_ADD_BEFORE = 'MAN_W_CTR';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Handle airfields
        foreach (self::AIRFIELDS as $airfield) {
            AirfieldService::insertIntoOrderBefore(
                $airfield,
                self::POSITION_WALLASEY_PENIL,
                self::POSITION_TO_ADD_BEFORE
            );
            AirfieldService::insertIntoOrderBefore(
                $airfield,
                self::POSITION_WALLASEY,
                self::POSITION_WALLASEY_PENIL
            );
        }

        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Handle airfields
        foreach (self::AIRFIELDS as $airfield) {
            AirfieldService::removeFromTopDownsOrder(
                $airfield,
                self::POSITION_WALLASEY
            );
            AirfieldService::removeFromTopDownsOrder(
                $airfield,
                self::POSITION_WALLASEY_PENIL,
            );
        }

        DependencyService::touchDependencyByKey('DEPENDENCY_AIRFIELD_OWNERSHIP');
    }
}
