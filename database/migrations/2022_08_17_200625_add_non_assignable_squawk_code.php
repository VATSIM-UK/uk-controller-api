<?php

use App\Models\Squawk\Reserved\NonAssignableSquawkCode;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        NonAssignableSquawkCode::insert(
            [
                [
                    'code' => '0200',
                    'description' => 'VATSIM default',
                ],
                [
                    'code' => '0000',
                    'description' => 'VATSIM default',
                ],
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
};
