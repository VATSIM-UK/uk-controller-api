<?php

use App\Models\Controller\Prenote;
use Illuminate\Database\Migrations\Migration;

class RemoveStanstedNugboPrenote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Prenote::where('key', 'EGSS_SID_NUGBO')->firstOrFail()->delete();
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
