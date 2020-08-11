<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
                        'tag_string' => 'C',
                        'description' => 'Climb',
                    ],
                    [
                        'tag_string' => 'D',
                        'description' => 'Descent',
                    ],
                    [
                        'tag_string' => 'T',
                        'description' => 'Turns',
                    ],
                    [
                        'tag_string' => 'F',
                        'description' => 'Full',
                    ],
                    [
                        'tag_string' => 'LT',
                        'description' => 'Left Turns',
                    ],
                    [
                        'tag_string' => 'RT',
                        'description' => 'Right Turns',
                    ],
                    [
                        'tag_string' => 'TD',
                        'description' => 'Turns and Descent',
                    ],
                    [
                        'tag_string' => 'LTD',
                        'description' => 'Left Turns and Descent',
                    ],
                    [
                        'tag_string' => 'LTD',
                        'description' => 'Right Turns and Descent',
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
