<?php

use App\Services\AirfieldService;
use Illuminate\Database\Migrations\Migration;

class RemoveCardiffFromBristolTopdown extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AirfieldService::removeFromTopDownsOrder('EGGD', 'EGFF_R_APP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        AirfieldService::insertIntoOrderAfter('EGGD', 'EGFF_R_APP', 'EGGD_APP');
    }
}
