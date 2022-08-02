<?php

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ControllerPosition::where('callsign', 'EGBB_TWR')
            ->firstOrFail()
            ->update(['frequency' => 118.300]);
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
};
