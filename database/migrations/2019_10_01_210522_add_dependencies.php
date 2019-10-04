<?php

use App\Models\Dependency\Dependency;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddDependencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Dependency::insert(
            [
                [
                    'uri' => '/sid',
                    'local_file' => 'sids.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/airfield',
                    'local_file' => 'airfields.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/controller',
                    'local_file' => 'controllers.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/handoff',
                    'local_file' => 'handoffs.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/hold',
                    'local_file' => 'holds.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/hold/profile',
                    'local_file' => 'holds-profiles.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/storage/dependencies/wake-categories.json',
                    'local_file' => 'wake-categories.json',
                    'created_at' => Carbon::now(),
                ],
                [
                    'uri' => '/storage/dependencies/prenotes.json',
                    'local_file' => 'prenotes.json',
                    'created_at' => Carbon::now(),
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
        Dependency::truncate();
    }
}
