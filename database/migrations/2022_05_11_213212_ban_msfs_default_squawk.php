<?php

use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use Illuminate\Database\Migrations\Migration;

class BanMsfsDefaultSquawk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NonAssignableSquawkCode::create(
            [
                'code' => '1234',
                'description' => 'MSFS Default',
            ]
        );
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
