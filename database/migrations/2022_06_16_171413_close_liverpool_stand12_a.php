<?php

use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class CloseLiverpoolStand12A extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Stand::where('identifier', '12A')
            ->airfield('EGGP')
            ->firstOrFail()
            ->close();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Stand::where('identifier', '12A')
            ->airfield('EGGP')
            ->firstOrFail()
            ->open();
    }
}
