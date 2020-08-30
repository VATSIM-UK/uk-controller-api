<?php

use App\Services\DependencyService;
use App\Services\PrenoteService;
use Illuminate\Database\Migrations\Migration;

class UpdateHeathrowPrenotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PrenoteService::createNewAirfieldPairingFromPrenote('EGLL', 'EGLF', 'PAIRING_EGLL_LTMA_SOUTH_WEST');
        PrenoteService::createNewAirfieldPairingFromPrenote('EGLL', 'EGTK', 'PAIRING_EGLL_LTMA_SOUTH_WEST');
        PrenoteService::createNewAirfieldPairingFromPrenote('EGLL', 'EGVN', 'PAIRING_EGLL_LTMA_SOUTH_WEST');
        PrenoteService::createNewAirfieldPairingFromPrenote('EGLL', 'EGMD', 'PAIRING_EGLL_LTMA_SOUTH_EAST');
        DependencyService::touchDependencyByKey('DEPENDENCY_PRENOTE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGLL', 'EGLF');
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGLL', 'EGTK');
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGLL', 'EGVN');
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGLL', 'EGMD');
        DependencyService::touchDependencyByKey('DEPENDENCY_PRENOTE');
    }
}
