<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddReleaseTypeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('enroute_release_types')
            ->insert(
                [
                    [
                        'tag_string' => 'RFC',
                        'description' => 'Released For Climb',
                    ],
                    [
                        'tag_string' => 'RFD',
                        'description' => 'Released For Descent',
                    ],
                    [
                        'tag_string' => 'RFT',
                        'description' => 'Released For Turns',
                    ],
                    [
                        'tag_string' => 'RLS',
                        'description' => 'Fully Released',
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
        DB::table('enroute_release_types')->delete();
    }
}
