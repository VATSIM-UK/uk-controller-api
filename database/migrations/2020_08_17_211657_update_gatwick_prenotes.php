<?php

use App\Services\DependencyService;
use App\Services\PrenoteService;
use Illuminate\Database\Migrations\Migration;

class UpdateGatwickPrenotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PrenoteService::createNewAirfieldPairingFromPrenote('EGKK', 'EGLF', 'PAIRING_EGKK_LTMA_SOUTH_WEST');
        PrenoteService::createNewAirfieldPairingFromPrenote('EGKK', 'EGWU', 'PAIRING_EGKK_LTMA_SOUTH_EAST');
        DependencyService::touchDependencyByKey('DEPENDENCY_PRENOTE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGKK', 'EGLF');
        PrenoteService::deleteAirfieldPairingPrenoteForPair('EGKK', 'EGWU');
        DependencyService::touchDependencyByKey('DEPENDENCY_PRENOTE');
    }
}
